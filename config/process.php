<?php
    session_start();
    include_once ("dbconection.php");
    include_once ("url.php");

    $data = $_POST;

    if(!empty($data)){
        if(($data["type"] ?? '') === "login") {
        
            $email = $data["email"];
            $pass = $data["senha"];
            $tipo = $data["tipo"];

            $query = "Select id, email, senha, tipo from usuarios WHERE email = :email and tipo = :tipo";

            $stmt = $conn->prepare($query);

            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":tipo", $tipo);
            
            try{
                $stmt->execute();

                if($stmt->rowCount() > 0){
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if($pass === $user["senha"]){
                        $_SESSION["msg"] = "Login realizado com sucesso";
                        $_SESSION["usuario_id"] = $user["id"];
                        $_SESSION["usuario_tipo"] = $user["tipo"];
                        
                        if($user["tipo"] === 'aluno'){
                            header("location: $BASE_URL/entrada_aluno.php");
                        } elseif ($user["tipo"] === 'professor') {
                            header("location: $BASE_URL/entrada_professor.php");
                        }
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

function getEntregas() {
    
    
}

?>