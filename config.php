<?php
// config.php

$host = 'localhost';
$db = 'centrocell'; // Nome do banco de dados
$user = 'root'; // Substitua pelo seu usuário do MySQL
$pass = ''; // Substitua pela sua senha do MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
}
?>
