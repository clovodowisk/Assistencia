<?php
session_start();

// Redirecionar para o dashboard se o usuário já estiver logado
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Controle</title>
    <link rel="stylesheet" href="./css/style.css"> <!-- Link para o CSS -->
</head>
<body>
    <h1>Bem-vindo ao Painel de Controle!</h1>
    <p><a href="login.php">Entrar</a></p>
</body>
</html>
