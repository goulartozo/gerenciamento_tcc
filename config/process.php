<?php
session_start();
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

    $query2 = "INSERT INTO entregas (aluno_id, tarefa_id, arquivo, data_envio) VALUES (:aluno_id, :tarefa_id, :arquivo, :data_envio)";

    if ($usuarioId && $tarefaId && isset($_FILES["arquivo"])) {
        $arquivo = $_FILES["arquivo"];
        $nomeFinal = uniqid() . "-" . basename($arquivo["name"]);
        $destino = "../arquivos/" . $nomeFinal;

        if (move_uploaded_file($arquivo["tmp_name"], $destino)) {
            $stmt = $conn->prepare($query2);
            $stmt->bindParam(":aluno_id", $usuarioId);
            $stmt->bindParam(":tarefa_id", $tarefaId);
            $stmt->bindParam(":arquivo", $nomeFinal);
            $stmt->bindParam(":data_envio", $dataEnvio);
            $stmt->execute();

            $_SESSION["msg"] = "Entrega enviada com sucesso!";
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