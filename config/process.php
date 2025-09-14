<?php
// Solução para o erro de sessão: Inicia a sessão apenas se ela não estiver ativa
if (!session_id()) {
    session_start();
}

include_once("dbconection.php");
include_once("url.php");

$data = $_POST;

function login($conn, $data, $BASE_URL)
{
    $email = $data["email"];
    $pass  = $data["senha"];
    $tipo  = $data["tipo"];

    $query = "SELECT id, email, senha, tipo FROM usuarios WHERE email = :email AND tipo = :tipo";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":tipo", $tipo);

    try {
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($pass === $user["senha"]) {
                $_SESSION["msg"] = "Login realizado com sucesso";
                $_SESSION["usuario_id"] = $user["id"];
                $_SESSION["tipo"] = $user["tipo"];

                if ($user["tipo"] === 'aluno') {
                    header("Location: $BASE_URL/entrada_aluno.php");
                } elseif ($user["tipo"] === 'professor') {
                    header("Location: $BASE_URL/entrada_professor.php");
                }
                exit;
            } else {
                $_SESSION["msg"] = "Senha incorreta";
            }
        } else {
            $_SESSION["msg"] = "Email não encontrado";
        }
    } catch (PDOException $e) {
        echo "Erro na tentativa de login: " . $e->getMessage();
    }
    header("Location: $BASE_URL/index.php");
    exit;
}

function uploadEntrega($conn, $data, $BASE_URL)
{

    $usuarioId = $_SESSION["usuario_id"] ?? null;
    $tarefaId  = $data["id"] ?? null;
    $dataEnvio = date("Y-m-d H:i:s");

    if ($usuarioId && $tarefaId && isset($_FILES["arquivo"])) {
        $arquivo = $_FILES["arquivo"];
        $nomeFinal = uniqid() . "-" . basename($arquivo["name"]);

        $destino = __DIR__ . "/../arquivos/" . $nomeFinal;

        if (move_uploaded_file($arquivo["tmp_name"], $destino)) {
            // Verifica se a entrega já existe para decidir entre INSERT ou UPDATE
            $checkQuery = "SELECT COUNT(*) FROM entregas WHERE aluno_id = :aluno_id AND tarefa_id = :tarefa_id";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bindParam(":aluno_id", $usuarioId);
            $checkStmt->bindParam(":tarefa_id", $tarefaId);
            $checkStmt->execute();
            $existeEntrega = $checkStmt->fetchColumn();

            if ($existeEntrega > 0) {
                // Se já existe, atualiza o registro e define o status como 'enviado'
                $updateQuery = "UPDATE entregas SET arquivo = :arquivo, data_envio = :data_envio, status = 'enviado' WHERE aluno_id = :aluno_id AND tarefa_id = :tarefa_id";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bindParam(":arquivo", $nomeFinal);
                $stmt->bindParam(":data_envio", $dataEnvio);
                $stmt->bindParam(":aluno_id", $usuarioId);
                $stmt->bindParam(":tarefa_id", $tarefaId);
                $stmt->execute();
                $_SESSION["msg"] = "Entrega atualizada com sucesso!";
            } else {
                // Se não existe, insere um novo registro e define o status como 'enviado'
                $insertQuery = "INSERT INTO entregas (aluno_id, tarefa_id, arquivo, data_envio, status) VALUES (:aluno_id, :tarefa_id, :arquivo, :data_envio, 'enviado')";
                $stmt = $conn->prepare($insertQuery);
                $stmt->bindParam(":aluno_id", $usuarioId);
                $stmt->bindParam(":tarefa_id", $tarefaId);
                $stmt->bindParam(":arquivo", $nomeFinal);
                $stmt->bindParam(":data_envio", $dataEnvio);
                $stmt->execute();
                $_SESSION["msg"] = "Entrega enviada com sucesso!";
            }
        } else {
            $_SESSION["msg"] = "Erro ao enviar o arquivo.";
        }
    }
    header("Location: $BASE_URL/entrada_aluno.php");
    exit;
}

if (!empty($data)) {
    $acao = $data["acao"] ?? "";
    $type = $data["type"] ?? "";

    if ($type === "login") {
        login($conn, $data, $BASE_URL);
    }

    if ($acao === "upload") {
        uploadEntrega($conn, $data, $BASE_URL);
    }

    if ($acao === "reuniao") {
        registrar_reuniao($conn, $data, $BASE_URL);
    }

    if ($acao === "proposta_ava") {
        registrar_notas_avaliacao_proposta_tc($conn, $data, $BASE_URL);
    }
}

function registrar_notas_avaliacao_proposta_tc($conn, $data, $BASE_URL, $alunoId = null)
{
    $professor_id = $_SESSION['usuario_id'] ?? null;
    if (!$alunoId) {
        $alunoId = $data['aluno_id'] ?? null;
    }
    $tarefaId = $data['tarefaId'] ?? 1; // ajuste conforme sua lógica

    // Notas dos requisitos
    $introducao   = floatval($data['introducao'] ?? 0);
    $objetivos    = floatval($data['objetivos'] ?? 0);
    $rev_biblio1  = floatval($data['rev_biblio1'] ?? 0);
    $rev_biblio2  = floatval($data['rev_biblio2'] ?? 0);
    $orientacao1  = floatval($data['orientacao1'] ?? 0);
    $orientacao2  = floatval($data['orientacao2'] ?? 0);

    // Resumo = soma das notas
    $resumo = $introducao + $objetivos + $rev_biblio1 + $rev_biblio2 + $orientacao1 + $orientacao2;
    $nota   = $resumo; // ou use média se preferir

    // Verifica se já existe avaliação deste professor para este aluno/tarefa
    $checkSql = "SELECT COUNT(*) FROM avaliacoes_proposta WHERE aluno_id = :aluno_id AND professor_id = :professor_id AND tarefa_id = :tarefa_id";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bindParam(':aluno_id', $alunoId);
    $checkStmt->bindParam(':professor_id', $professor_id);
    $checkStmt->bindParam(':tarefa_id', $tarefaId);
    $checkStmt->execute();
    $existe = $checkStmt->fetchColumn();

    if ($existe > 0) {
        $_SESSION["msg"] = "Você já avaliou este aluno.";
        header("Location: $BASE_URL/entrada_professor.php");
        exit;
    }

    // Insere a avaliação individual
    $sql = "INSERT INTO avaliacoes_proposta (
        aluno_id, professor_id, tarefa_id,
        introducao, objetivos, rev_biblio1, rev_biblio2,
        orientacao1, orientacao2, resumo, data_avaliacao
    ) VALUES (
        :aluno_id, :professor_id, :tarefa_id,
        :introducao, :objetivos, :rev_biblio1, :rev_biblio2,
        :orientacao1, :orientacao2, :resumo, NOW()
    )";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':aluno_id', $alunoId);
        $stmt->bindParam(':professor_id', $professor_id);
        $stmt->bindParam(':tarefa_id', $tarefaId);
        $stmt->bindParam(':introducao', $introducao);
        $stmt->bindParam(':objetivos', $objetivos);
        $stmt->bindParam(':rev_biblio1', $rev_biblio1);
        $stmt->bindParam(':rev_biblio2', $rev_biblio2);
        $stmt->bindParam(':orientacao1', $orientacao1);
        $stmt->bindParam(':orientacao2', $orientacao2);
        $stmt->bindParam(':resumo', $resumo);
        $stmt->execute();
        $_SESSION["msg"] = "Avaliação registrada com sucesso!";
    } catch (PDOException $e) {
        $_SESSION["msg"] = "Erro ao registrar avaliação: " . $e->getMessage();
        header("Location: $BASE_URL/entrada_professor.php");
        exit;
    }

    // Após inserir, verifica se já existem 3 avaliações para este aluno/tarefa
    $countSql = "SELECT AVG(nota) as media, COUNT(*) as total FROM avaliacoes_proposta WHERE aluno_id = :aluno_id AND tarefa_id = :tarefa_id";
    $countStmt = $conn->prepare($countSql);
    $countStmt->bindParam(':aluno_id', $alunoId);
    $countStmt->bindParam(':tarefa_id', $tarefaId);
    $countStmt->execute();
    $result = $countStmt->fetch(PDO::FETCH_ASSOC);

    if ($result['total'] == 3) {
        // Atualiza a entrega com a média final
        $media = round($result['media'], 2);
        $updateSql = "UPDATE entregas SET status = 'avaliado', nota = :media WHERE aluno_id = :aluno_id AND tarefa_id = :tarefa_id";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bindParam(':media', $media);
        $updateStmt->bindParam(':aluno_id', $alunoId);
        $updateStmt->bindParam(':tarefa_id', $tarefaId);
        $updateStmt->execute();
        $_SESSION["msg"] .= " | Média final registrada: $media";
    }

    header("Location: $BASE_URL/entrada_professor.php");
    exit;
}

function registrar_reuniao($conn, $data, $BASE_URL)
{
    $aluno_id = $_SESSION['usuario_id'];
    $datareuniao = $data['data'];
    $assunto = $data['assunto'];
    $prof = $data['prof'] ?: '✔';
    $aluno = $data['aluno'] ?: '✔';

    $sql_prof = "SELECT professor_id FROM usuarios WHERE id = :aluno_id";
    $stmt = $conn->prepare($sql_prof);
    $stmt->bindParam(':aluno_id', $aluno_id);
    $stmt->execute();
    $professor = $stmt->fetch(PDO::FETCH_ASSOC);
    $professor_id = $professor['professor_id'];

    // Adicione prof e aluno ao SQL se existirem na tabela
    $sql = "INSERT INTO reunioes(aluno_id, professor_id, data, assunto, ass_prof, ass_aluno)
        VALUES (:aluno_id, :professor_id, :datareuniao, :assunto, :prof, :aluno);";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':aluno_id', $aluno_id);
        $stmt->bindParam(':professor_id', $professor_id);
        $stmt->bindParam(':datareuniao', $datareuniao);
        $stmt->bindParam(':assunto', $assunto);
        $stmt->bindParam(':prof', $prof);
        $stmt->bindParam(':aluno', $aluno);
        $stmt->execute();
        $_SESSION["msg"] = "Reunião registrada com sucesso!";
    } catch (PDOException $e) {
        $_SESSION["msg"] = "Erro ao registrar reunião: " . $e->getMessage();
    }
    header("Location: $BASE_URL/entrada_aluno.php");
    exit;
}

function getReunioesAluno($conn)
{
    $aluno_id = $_SESSION['usuario_id'] ?? null;
    if (!$aluno_id) return [];

    try {
        $sql = "SELECT data, assunto, ass_prof, ass_aluno FROM reunioes WHERE aluno_id = :aluno_id ORDER BY data DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':aluno_id', $aluno_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getAlunosVinculadosAoProfessor($conn)
{
    $professor_id = $_SESSION['usuario_id'] ?? null;
    if (!$professor_id) return [];

    try {
        $sql = "
            SELECT 
                u.id,
                u.nome,
                u.matricula,
                CASE 
                    WHEN EXISTS (
                        SELECT 1 FROM entregas e 
                        WHERE e.aluno_id = u.id 
                          AND e.status = 'enviado'
                    )
                    THEN 'enviado'
                    ELSE 'não enviado'
                END AS status
            FROM aluno_professores ap
            JOIN usuarios u ON ap.aluno_id = u.id
            WHERE ap.professor_id = :professor_id
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':professor_id', $professor_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function getAlunosProfessores($conn)
{
    try {
        $sql = "select * from usuarios";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {

        return [];
    }
}

function getEnviosStatus($status)
{
    $properties = ['disabled' => false, 'button_text' => 'ENVIAR'];

    if ($status === 'pendente') {
        $properties['disabled'] = false;
        $properties['button_text'] = 'CORRIGIR';
    } elseif ($status === 'enviado') {
        $properties['disabled'] = true;
        $properties['button_text'] = 'ENVIADO';
    } elseif ($status === 'avaliado') {
        $properties['disabled'] = true;
        $properties['button_text'] = 'AVALIADO';
    }

    return $properties;
}

function getTarefasEntregasAluno($conn, $alunoId = null)
{
    if (!$alunoId) {
        $alunoId = $_SESSION["usuario_id"] ?? null;
    }

    if (!$alunoId) {
        return [];
    }

    try {
        $query = "SELECT tarefa_id, status, nota, arquivo FROM entregas WHERE aluno_id = :aluno_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":aluno_id", $alunoId);
        $stmt->execute();

        $entregasDoAluno = [];
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $row) {
            $entregasDoAluno[$row['tarefa_id']] = [
                'status' => $row['status'],
                'nota'   => $row['nota'],
                'arquivo' => $row['arquivo']
            ];
        }
        return $entregasDoAluno;
    } catch (PDOException $e) {
        return [];
    }
}

function getProfessoresDoAluno($conn, $alunoId)
{
    try {
        $sql = "
            SELECT 
                p.id,
                p.nome,
                ap.tipo
            FROM aluno_professores ap
            JOIN usuarios p ON ap.professor_id = p.id
            WHERE ap.aluno_id = :aluno_id
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':aluno_id', $alunoId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
