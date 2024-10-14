<?php
session_start();
require 'config.php';

// Inicializar variáveis
$os = $modelo = $servico = $prazo = $responsavel_os = $tecnico = '';
$successMessage = '';
$errorMessage = '';

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $os = $_POST['os'];
    $modelo = $_POST['modelo'];
    $servico = $_POST['servico'];
    $prazo = $_POST['prazo'];
    $responsavel_os = $_POST['responsavel_os'];
    $tecnico = $_POST['tecnico'];
    $entrega = isset($_POST['entrega']) ? 1 : 0; // 1 para Sim, 0 para Não

    // Converter a data de 'd/m/Y H:i' para 'Y-m-d H:i:s' antes de inserir no banco de dados
    $prazoConvertido = DateTime::createFromFormat('d/m/Y H:i', $prazo);
    if ($prazoConvertido) {
        $prazoSQL = $prazoConvertido->format('Y-m-d H:i:s'); // Formato aceito pelo banco
    } else {
        $errorMessage = "Formato de data inválido.";
    }

    // Inserir os dados no banco de dados
    if (!$errorMessage) {
        $stmt = $pdo->prepare("INSERT INTO servicos (os, modelo, servico, prazo, responsavel_os, tecnico, carimbo, entrega) VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, ?)");
        if ($stmt->execute([$os, $modelo, $servico, $prazoSQL, $responsavel_os, $tecnico, $entrega])) {
            $successMessage = "Cadastrado com sucesso!";
            // Limpar campos após inserção
            $os = $modelo = $servico = $prazo = $responsavel_os = $tecnico = '';
        } else {
            $errorMessage = "Erro ao cadastrar.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Novo Serviço</title>
    <link rel="stylesheet" href="./css/inserir_info.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"> <!-- Flatpickr CSS -->
</head>
<body>
    <div class="container">
        <h1>Cadastrar Novo Serviço</h1>

        <?php if ($successMessage): ?>
            <p class="success"><?= $successMessage ?></p>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
            <p class="error"><?= $errorMessage ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="os" placeholder="OS" value="<?= htmlspecialchars($os) ?>" required>
            <input type="text" name="modelo" placeholder="Modelo" value="<?= htmlspecialchars($modelo) ?>" required>
            <input type="text" name="servico" placeholder="Serviço" value="<?= htmlspecialchars($servico) ?>" required>

            <!-- Campo para selecionar data e hora no formato dd/mm/yyyy -->
            <input type="text" id="prazo" name="prazo" placeholder="Selecione o prazo" value="<?= htmlspecialchars($prazo) ?>" required>

            <input type="text" name="responsavel_os" placeholder="Responsável OS" value="<?= htmlspecialchars($responsavel_os) ?>" required>
            <input type="text" name="tecnico" placeholder="Técnico" value="<?= htmlspecialchars($tecnico) ?>" required>
            
            <label for="entrega">Entrega:</label>
            <input type="checkbox" name="entrega" id="entrega" value="1"> Sim

            <button type="submit">Cadastrar</button>
        </form>

        <p><a class="back-link" href="dashboard.php">Voltar ao Painel</a></p>
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
