<?php
session_start();
require 'config.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Inicializar variáveis
$servicos = [];
$servicoParaEditar = null;

// Processar o formulário para editar um serviço
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_id'])) {
    // Obter dados do formulário
    $id = $_POST['edit_id'];
    $os = $_POST['os'];
    $modelo = $_POST['modelo'];
    $servico = $_POST['servico'];
    $prazo = $_POST['prazo'];
    $responsavel_os = $_POST['responsavel_os'];
    $tecnico = $_POST['tecnico'];

    // Atualizar dados no banco de dados
    $stmt = $pdo->prepare("UPDATE servicos SET os = ?, modelo = ?, servico = ?, prazo = ?, responsavel_os = ?, tecnico = ? WHERE id = ?");
    $stmt->execute([$os, $modelo, $servico, $prazo, $responsavel_os, $tecnico, $id]);
    header("Location: editar_servico.php"); // Redirecionar após a atualização
    exit();
}

// Lógica para buscar serviços
$searchQuery = '';
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $stmt = $pdo->prepare("SELECT * FROM servicos WHERE os LIKE ? OR modelo LIKE ? OR servico LIKE ? ORDER BY prazo ASC");
    $stmt->execute(["%$searchQuery%", "%$searchQuery%", "%$searchQuery%"]);
} else {
    // Consultar todos os serviços se não houver busca
    $stmt = $pdo->query("SELECT * FROM servicos ORDER BY prazo ASC");
}

$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Se um serviço for selecionado para edição, busque os dados dele
if (isset($_GET['edit'])) {
    $idEditar = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = ?");
    $stmt->execute([$idEditar]);
    $servicoParaEditar = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Serviços</title>
    <link rel="stylesheet" href="./css/inserir_info.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"> <!-- Flatpickr CSS -->
    <style>
        /* Estilos adicionais para o formulário de busca */
        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .search-container input[type="text"] {
            width: 300px; /* Largura do campo de busca */
            padding: 8px;
            margin-right: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .search-container button {
            padding: 8px 12px;
            border: none;
            background-color: #28a745;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-container button:hover {
            background-color: #218838; /* Cor ao passar o mouse */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar Serviços</h1>

        <!-- Formulário de busca -->
        <div class="search-container">
            <form method="GET">
                <input type="text" name="search" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Buscar...">
                <button type="submit">Buscar</button>
            </form>
        </div>

        <!-- Se um serviço para editar for selecionado, exiba o formulário de edição -->
        <?php if ($servicoParaEditar): ?>
            <h2>Editar Serviço</h2>
            <form method="POST">
                <input type="hidden" name="edit_id" value="<?= htmlspecialchars($servicoParaEditar['id']); ?>">
                <label>O.S.:</label>
                <input type="text" name="os" value="<?= htmlspecialchars($servicoParaEditar['os']); ?>" required>
                
                <label>Modelo:</label>
                <input type="text" name="modelo" value="<?= htmlspecialchars($servicoParaEditar['modelo']); ?>" required>
                
                <label>Serviço:</label>
                <input type="text" name="servico" value="<?= htmlspecialchars($servicoParaEditar['servico']); ?>" required>
                
                <label>Prazo:</label>
                <input type="text" id="prazo" name="prazo" value="<?= date('d/m/Y H:i', strtotime($servicoParaEditar['prazo'])); ?>" required>
                
                <label>Responsável O.S.:</label>
                <input type="text" name="responsavel_os" value="<?= htmlspecialchars($servicoParaEditar['responsavel_os']); ?>" required>
                
                <label>Técnico:</label>
                <input type="text" name="tecnico" value="<?= htmlspecialchars($servicoParaEditar['tecnico']); ?>" required>
                
                <button type="submit">Atualizar</button>
            </form>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>O.S.</th>
                        <th>Modelo</th>
                        <th>Serviço</th>
                        <th>Prazo</th>
                        <th>Responsável O.S.</th>
                        <th>Técnico</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($servicos) > 0): ?>
                        <?php foreach ($servicos as $servico): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($servico['id']); ?></td>
                                <td><?php echo htmlspecialchars($servico['os']); ?></td>
                                <td><?php echo htmlspecialchars($servico['modelo']); ?></td>
                                <td><?php echo htmlspecialchars($servico['servico']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($servico['prazo'])); ?></td>
                                <td><?php echo htmlspecialchars($servico['responsavel_os']); ?></td>
                                <td><?php echo htmlspecialchars($servico['tecnico']); ?></td>
                                <td>
                                    <a href="?edit=<?= $servico['id']; ?>">Editar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">Nenhum serviço encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <p><a href="dashboard.php">Voltar ao Painel</a></p>
    </div>

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Inicializar Flatpickr no campo prazo
        flatpickr("#prazo", {
            enableTime: true,
            dateFormat: "d/m/Y H:i", // Formato brasileiro
            time_24hr: true,
            allowInput: true
        });
    </script>
</body>
</html>
