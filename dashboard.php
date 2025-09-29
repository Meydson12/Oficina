<?php
session_start();

// Verificar se está logado
if(!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

// Conectar ao banco
require_once 'config/database.php';
require_once 'config/funcoes.php';

$database = new Database();
$db = $database->getConnection();

// Buscar dados para o dashboard
$total_agendamentos_hoje = contarAgendamentosHoje($db);
$total_vendas_mes = calcularTotalVendasMes($db);
$pecas_estoque_baixo = pecasEstoqueBaixo($db);
$agendamentos_hoje = agendamentosDoDia($db);

$paginaTitulo = "Dashboard";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="imagens/engrenagem.png" type="image/x-icon">
    <title>GenAuto - <?php echo $paginaTitulo; ?></title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <h2>🔧 GenAuto</h2>
            </div>
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="agendamentos.php" class="nav-link">Agendamentos</a>
                <a href="vendas.php" class="nav-link">Vendas</a>
                <a href="estoque.php" class="nav-link">Estoque</a>
                <a href="logout.php" class="nav-link logout">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1>👋 Bem-vindo, <?php echo $_SESSION['usuario_nome']; ?>!</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>📅 Agendamentos Hoje</h3>
                <p class="stat-number"><?php echo $total_agendamentos_hoje; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>💰 Vendas do Mês</h3>
                <p class="stat-number">R$ <?php echo number_format($total_vendas_mes, 2, ',', '.'); ?></p>
            </div>
            
            <div class="stat-card">
                <h3>⚠️ Estoque Baixo</h3>
                <p class="stat-number"><?php echo $pecas_estoque_baixo; ?></p>
            </div>
        </div>

        <div class="section">
            <h2>📋 Agendamentos de Hoje (<?php echo date('d/m/Y'); ?>)</h2>
            <div class="table-container">
                <?php if(count($agendamentos_hoje) > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Veículo</th>
                                <th>Horário</th>
                                <th>Serviço</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($agendamentos_hoje as $agendamento): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($agendamento['cliente_nome']); ?></td>
                                <td><?php echo htmlspecialchars($agendamento['veiculo']); ?></td>
                                <td><?php echo formatarHora($agendamento['data_agendamento']); ?></td>
                                <td><?php echo htmlspecialchars($agendamento['servico']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="no-data">🎉 Nenhum agendamento para hoje!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>