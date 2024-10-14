<?php
session_start();
require 'config.php';

// Redirecionar para o login se o usuário não estiver logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Consultar todos os serviços no banco de dados, ordenando pelo prazo
$stmt = $pdo->query("SELECT * FROM servicos ORDER BY prazo ASC");
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviços</title>
    <link rel="stylesheet" href="./css/servicos.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }
        .expired {
            color: red;
        }
        .urgent {
            color: red;
        }
    </style>
</head>
<body>
    <h1>Lista de Serviços</h1>

    <!-- Filtros -->
    <div>
        <label for="prazoFiltro">Filtro por Prazo:</label>
        <select id="prazoFiltro">
            <option value="">Todos</option>
            <option value="1">Falta 1 dia</option>
            <option value="2">Falta 2 dias</option>
            <option value="3">Falta 3 dias</option>
            <option value="7">Falta 1 semana</option>
            <option value="30">Falta 1 mês</option>
        </select>

        <label for="tecnicoFiltro">Filtro por Técnico:</label>
        <select id="tecnicoFiltro">
            <option value="">Todos</option>
            <?php
            // Pegar todos os técnicos disponíveis
            $tecnicos = array_unique(array_column($servicos, 'tecnico'));
            foreach ($tecnicos as $tecnico) {
                echo "<option value=\"" . htmlspecialchars($tecnico) . "\">" . htmlspecialchars($tecnico) . "</option>";
            }
            ?>
        </select>

        <label for="responsavelFiltro">Filtro por Responsável OS:</label>
        <select id="responsavelFiltro">
            <option value="">Todos</option>
            <?php
            // Pegar todos os responsáveis disponíveis
            $responsaveis = array_unique(array_column($servicos, 'responsavel_os'));
            foreach ($responsaveis as $responsavel) {
                echo "<option value=\"" . htmlspecialchars($responsavel) . "\">" . htmlspecialchars($responsavel) . "</option>";
            }
            ?>
        </select>

        <label for="entregaFiltro">Filtro por Entrega:</label>
        <select id="entregaFiltro">
            <option value="">Todos</option>
            <option value="1">Sim</option>
            <option value="0">Não</option>
        </select>

        <button onclick="filtrar()">Filtrar</button>
    </div>

    <table id="servicosTable">
        <thead>
            <tr>
                <th>OS</th>
                <th>Modelo</th>
                <th>Serviço</th>
                <th>Prazo</th>
                <th>Responsável OS</th>
                <th>Técnico</th>
                <th>Carimbo</th>
                <th>Entrega</th>
                <th>Timer</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($servicos): ?>
                <?php foreach ($servicos as $servico): ?>
                    <tr data-prazo="<?= htmlspecialchars($servico['prazo']) ?>" data-tecnico="<?= htmlspecialchars($servico['tecnico']) ?>" data-responsavel="<?= htmlspecialchars($servico['responsavel_os']) ?>" data-entrega="<?= htmlspecialchars($servico['entrega']) ?>">
                        <td><?= htmlspecialchars($servico['os']) ?></td>
                        <td><?= htmlspecialchars($servico['modelo']) ?></td>
                        <td><?= htmlspecialchars($servico['servico']) ?></td>
                        <td><?= htmlspecialchars(date('d/m/y H:i:s', strtotime($servico['prazo']))) ?></td> <!-- Formatação do prazo -->
                        <td><?= htmlspecialchars($servico['responsavel_os']) ?></td>
                        <td><?= htmlspecialchars($servico['tecnico']) ?></td>
                        <td><?= htmlspecialchars(date('d/m/y H:i:s', strtotime($servico['carimbo']))) ?></td> <!-- Formatação do carimbo -->
                        <td><?= htmlspecialchars($servico['entrega']) == 1 ? 'Sim' : 'Não' ?></td>
                        <td id="timer-<?= $servico['id'] ?>">
                            <?php if ($servico['prazo'] != '0000-00-00 00:00:00'): ?>
                                <script>
                                    (function() {
                                        const endTime = new Date("<?= htmlspecialchars($servico['prazo']) ?>");
                                        const elementId = 'timer-<?= $servico['id'] ?>';

                                        function updateTimer() {
                                            const now = new Date();
                                            const remaining = endTime - now;

                                            const timerElement = document.getElementById(elementId);
                                            if (remaining < 0) {
                                                timerElement.innerHTML = 'Expirado';
                                                timerElement.classList.add('expired');
                                            } else {
                                                const days = Math.floor(remaining / (1000 * 60 * 60 * 24));
                                                const hours = Math.floor((remaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                                const minutes = Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60));
                                                const seconds = Math.floor((remaining % (1000 * 60)) / 1000);

                                                timerElement.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;

                                                if (remaining <= 3600000) {
                                                    timerElement.classList.add('urgent');
                                                }
                                            }
                                        }

                                        setInterval(updateTimer, 1000);
                                        updateTimer(); // Inicializa imediatamente
                                    })();
                                </script>
                            <?php else: ?>
                                Sem Prazo
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">Nenhum dado disponível para exibir.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <p style="text-align: center; margin-top: 20px;">
        <a href="dashboard.php">Voltar ao Painel</a>
    </p>

    <script>
        function filtrar() {
            const prazoFiltro = document.getElementById('prazoFiltro').value;
            const tecnicoFiltro = document.getElementById('tecnicoFiltro').value;
            const responsavelFiltro = document.getElementById('responsavelFiltro').value;
            const entregaFiltro = document.getElementById('entregaFiltro').value;

            console.log('Filtros:', {
                prazo: prazoFiltro,
                tecnico: tecnicoFiltro,
                responsavel: responsavelFiltro,
                entrega: entregaFiltro
            });

            const tableRows = document.querySelectorAll('#servicosTable tbody tr');

            tableRows.forEach(row => {
                const prazo = new Date(row.getAttribute('data-prazo')).getTime(); // Convertendo para milissegundos
                const tecnico = row.getAttribute('data-tecnico');
                const responsavel = row.getAttribute('data-responsavel');
                const entrega = row.getAttribute('data-entrega');

                const now = new Date();
                let mostrar = true;

                // Filtrar por prazo
                if (prazoFiltro) {
                    const prazoLimite = new Date(now);
                    prazoLimite.setDate(now.getDate() + parseInt(prazoFiltro));
                    if (prazo > prazoLimite) {
                        mostrar = false;
                    }
                }

                // Filtrar por técnico
                if (tecnicoFiltro && tecnico !== tecnicoFiltro) {
                    mostrar = false;
                }

                // Filtrar por responsável OS
                if (responsavelFiltro && responsavel !== responsavelFiltro) {
                    mostrar = false;
                }

                // Filtrar por entrega
                if (entregaFiltro && entrega != entregaFiltro) {
                    mostrar = false;
                }

                row.style.display = mostrar ? '' : 'none';
            });
        }

        // Atualiza a tabela a cada 5 segundos
        setInterval(atualizarTabela, 5000);
    </script>
</body>
</html>
