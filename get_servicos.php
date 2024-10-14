<?php
session_start();
require 'config.php';

// Redirecionar para o login se o usuário não estiver logado
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

// Consultar todos os serviços no banco de dados
$stmt = $pdo->query("SELECT * FROM servicos");
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retornar os serviços como JSON
header('Content-Type: application/json');
echo json_encode($servicos);
