<?php
// Solução para o erro de sessão: Inicia a sessão apenas se ela não estiver ativa
if (!session_id()) {
    session_start();
}

include_once("dbconection.php");
include_once("url.php");

$data = $_POST;

// === FUNÇÕES ===
function login($conn, $data, $BASE_URL) {
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
                $_SESSION["usuario_tipo"] = $user["tipo"];

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

function uploadEntrega($conn, $data, $BASE_URL) {

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
}

function getEnviosStatus($status){
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

function getTarefasEntregasAluno($conn) {
    $alunoId = $_SESSION["usuario_id"] ?? null;

    if (!$alunoId) {
        return [];
    }

    try {
        $query = "SELECT tarefa_id, status, nota FROM entregas WHERE aluno_id = :aluno_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":aluno_id", $alunoId);
        $stmt->execute();

        $entregasDoAluno = [];
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $row) {
            $entregasDoAluno[$row['tarefa_id']] = [
                'status' => $row['status'],
                'nota'   => $row['nota']
            ];
        }
        return $entregasDoAluno;
    } catch (PDOException $e) {
        return [];
    }
}