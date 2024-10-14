<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel</title>
    <link rel="stylesheet" href="./css/dashboard.css">
</head>
<body>
    <div class="container">
        <h1>Bem-vindo ao painel!</h1>
        <div class="nav-buttons">
            <a href="usuarios.php"><button>Gerenciar Usuários</button></a>
            <a href="servicos.php"><button>Serviços</button></a>
            <a href="inserir_info.php"><button>Cadastrar O.S.</button></a>
            <a href="editar_servico.php"><button>Editar O.S.</button></a>
            <!-- Adicione mais botões conforme necessário -->
        </div>
        <a class="logout" href="logout.php">Sair</a>
    </div>
</body>
</html>
