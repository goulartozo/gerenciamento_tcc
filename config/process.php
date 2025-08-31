<?php
    session_start();
    include_once ("dbconection.php");
    include_once ("url.php");

    $data = $_POST;

    /*
        CREATE TABLE users (
        id SERIAL PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        tipo VARCHAR(20) NOT NULL CHECK (tipo IN ('aluno','professor')),
        criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    O codigo de processamento de login teve como base esta tabela
    */

    if(!empty($data)){
        if(($data["type"] ?? '') === "login") {
        
            $email = $data["email"];
            $pass = $data["password"];
            $tipo = $data["tipo"];

            $query = "Select email, password from users WHERE email = :email and tipo = :tipo";

            $stmt = $conn->prepare($query);

            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":tipo", $tipo);
            
            try{
                $stmt->execute();

                if($stmt->rowCount() > 0){
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if(password_verify($pass, $user["password"])){
                        $_SESSION["msg"] = "Login realizado com sucesso";
                        $_SESSION["usuário_id"] = $user["id"];
                        $_SESSION["usuário_tipo"] = $user["tipo"];
                        header("location: $BASE_URL/sucesso.php");
                        exit;
                    } else {
                        $_SESSION["msg"] = "Senha incorreta";
                        header("location: $BASE_URL/index.php");
                        exit;
                    }
                } else {
                    $_SESSION["msg"] = "Email não encontrado";
                    header("location: $BASE_URL/index.php");
                    exit;
                }
            } catch (PDOException $e){
                echo "erro na tentativa de login" . $e->getMessage();
            }
        }
    }

?>