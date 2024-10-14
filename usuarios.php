<?php
session_start();
require 'config.php';

// Redirecionar para o login se o usuário não estiver logado
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Lógica para criar um novo usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Criptografa a senha

    // Verifica se o usuário já existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE username = ?");
    $stmt->execute([$username]);
    $userCount = $stmt->fetchColumn();

    if ($userCount > 0) {
        $errorMessage = "Erro: O nome de usuário já está em uso.";
    } else {
        // Inserir novo usuário no banco de dados
        $stmt = $pdo->prepare("INSERT INTO usuarios (username, password, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$username, $password]);
    }
}

// Lógica para editar um usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $userId = $_POST['user_id'];
    $username = $_POST['username'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null; // Criptografa a senha, se fornecida

    // Atualiza o usuário no banco de dados
    if ($password) {
        $stmt = $pdo->prepare("UPDATE usuarios SET username = ?, password = ? WHERE id = ?");
        $stmt->execute([$username, $password, $userId]);
    } else {
        $stmt = $pdo->prepare("UPDATE usuarios SET username = ? WHERE id = ?");
        $stmt->execute([$username, $userId]);
    }
}

// Lógica para excluir um usuário
if (isset($_GET['delete'])) {
    $userId = $_GET['delete'];

    // Deletar o usuário do banco de dados
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->execute([$userId]);
}

// Consultar usuários
$stmt = $pdo->query("SELECT * FROM usuarios");
$usuarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários</title>
    <link rel="stylesheet" href="./css/users.css">
</head>
<body>
    <div class="container">
        <h1>Gerenciar Usuários</h1>
        
        <h2>Criar Usuário</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Nome de Usuário" required>
            <input type="password" name="password" placeholder="Senha" required>
            <button type="submit" name="create_user">Criar Usuário</button>
        </form>
        
        <?php if (isset($errorMessage)): ?>
            <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>

        <h2>Lista de Usuários</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuário</th>
                    <th>Data de Criação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($usuarios) > 0): ?>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario['id']) ?></td>
                            <td><?= htmlspecialchars($usuario['username']) ?></td>
                            <td><?= htmlspecialchars($usuario['created_at']) ?></td>
                            <td>
                                <button type="button" class="edit-button" onclick="openEditModal(<?= $usuario['id'] ?>, '<?= htmlspecialchars($usuario['username']) ?>')">Editar</button>
                                <a href="?delete=<?= $usuario['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Nenhum usuário encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a class="back-button" href="dashboard.php">Voltar</a>

        <!-- Modal para edição de usuário -->
        <div id="editModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" onclick="closeEditModal()">&times;</span>
                <h2>Editar Usuário</h2>
                <form id="editUserForm" method="POST">
                    <input type="hidden" name="user_id" id="user_id">
                    <input type="text" name="username" id="edit_username" required>
                    <input type="password" name="password" placeholder="Nova Senha (deixe em branco para não alterar)">
                    <button type="submit" name="edit_user">Salvar Alterações</button>
                </form>
            </div>
        </div>

        <script>
            function openEditModal(userId, username) {
                document.getElementById('user_id').value = userId;
                document.getElementById('edit_username').value = username;
                document.getElementById('editModal').style.display = "block";
            }

            function closeEditModal() {
                document.getElementById('editModal').style.display = "none";
            }

            // Fecha o modal ao clicar fora dele
            window.onclick = function(event) {
                const modal = document.getElementById('editModal');
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            };
        </script>

        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 20px;
            }

            .container {
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                background: white;
                border-radius: 8px;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            }

            h1, h2 {
                color: #333;
                text-align: center;
            }

            form {
                margin-bottom: 30px; /* Espaçamento entre os formulários */
            }

            input[type="text"],
            input[type="password"] {
                width: 100%;
                padding: 10px;
                margin: 10px 0;
                border: 1px solid #ddd;
                border-radius: 5px;
            }

            button {
                background: #007bff;
                color: #fff;
                border: none;
                padding: 10px 15px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
                transition: background-color 0.3s;
            }

            button:hover {
                background: #0056b3;
            }

            .error {
                color: red;
                text-align: center;
                margin-bottom: 20px; /* Espaço inferior para a mensagem de erro */
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px; /* Espaço inferior para a tabela */
            }

            table, th, td {
                border: 1px solid #ddd;
            }

            th, td {
                padding: 10px;
                text-align: left;
            }

            th {
                background-color: #f2f2f2;
            }

            .back-button {
                display: block;
                text-align: center;
                font-size: 16px;
                color: #007bff;
                text-decoration: none;
                margin-top: 20px; /* Espaço acima do link de voltar */
            }

            .back-button:hover {
                text-decoration: underline;
            }

            /* Estilos para o modal */
            .modal {
                display: none; /* Ocultar por padrão */
                position: fixed;
                z-index: 1;
                left: 0;
                top: 0;
                width: 100%; /* Largura total */
                height: 100%; /* Altura total */
                overflow: auto; /* Ativar rolagem se necessário */
                background-color: rgb(0,0,0); /* Cor de fundo */
                background-color: rgba(0,0,0,0.4); /* Fundo com opacidade */
            }
            .modal-content {
                background-color: #fefefe;
                margin: 15% auto; /* 15% do topo e centralizado */
                padding: 20px;
                border: 1px solid #888;
                width: 80%; /* Pode ser mais ou menos conforme necessário */
                border-radius: 8px; /* Borda arredondada */
            }
            .close {
                color: #aaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
            }
            .close:hover,
            .close:focus {
                color: black;
                text-decoration: none;
                cursor: pointer;
            }
        </style>
    </div>
</body>
</html>
