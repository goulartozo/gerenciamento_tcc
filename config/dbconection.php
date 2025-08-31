<?php
    
$host = "localhost";
$dbname = "cadastro";
$user = "postgres";
$pass = "postgres";
$port = "5432";

try {

$conn = new PDO("pgsql:host=$host;port=5432;dbname=$dbname", $user, $pass);

$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e){
$error = $e->getMessage();
echo "Error: $error";
}

