<?php
session_start();
require_once 'config/database.php';
require_once 'config/funcoes.php';

// 🔐 Verificar login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

// 📦 Conectar ao banco
$database = new Database();
$pdo = $database->getConnection();

// 🔍 Buscar vendas com JOIN para cliente
$stmt = $pdo->query("
    SELECT v.*, a.cliente_nome 
    FROM vendas v 
    LEFT JOIN agendamentos a ON v.cliente_id = a.id 
    ORDER BY v.data_venda DESC
");
$vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="imagens/engrenagem.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/responsivo.css">
    <title>GenAuto - Vendas</title>
</head>
<body>
    <!-- Navbar -->
    <?php include 'includes/navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4" style="margin-top: 80px;">

                <!-- Alertas -->
                <?php if (isset($_GET['sucesso'])): ?>
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        <strong>✅ Sucesso!</strong> 
                        <?php
                        switch($_GET['sucesso']) {
                            case 'cadastrada': echo 'Venda cadastrada com sucesso!'; break;
                            case 'cancelada': echo 'Venda cancelada com sucesso!'; break;
                            default: echo 'Operação realizada com sucesso!';
                        }
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>
                        <i class="bi bi-currency-dollar"></i> Vendas
                    </h2>
                    <a href="vendas/vendas-cadastrar.php" class="btn btn-success btn-sm">
                        <i class="bi bi-plus-circle"></i> Nova Venda
                    </a>
                </div>

                <!-- Tabela de Vendas -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-receipt"></i> Histórico de Vendas
                            <span class="badge bg-primary ms-2"><?php echo count($vendas); ?></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($vendas) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Data</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($vendas as $venda): ?>
                                            <tr>
                                                <td>#<?php echo str_pad($venda['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                                <td>
                                                    <?php if ($venda['cliente_nome']): ?>
                                                        <?php echo htmlspecialchars($venda['cliente_nome']); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">Venda Avulsa</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($venda['data_venda'])); ?></td>
                                                <td>
                                                    <strong>R$ <?php echo number_format($venda['total'], 2, ',', '.'); ?></strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo getStatusVendaColor($venda['status']); ?>">
                                                        <?php echo ucfirst($venda['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="vendas/vendas-detalhes.php?id=<?php echo $venda['id']; ?>" 
                                                           class="btn btn-outline-info" title="Ver Detalhes">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <?php if ($venda['status'] == 'pendente'): ?>
                                                            <a href="vendas-cancelar.php?id=<?php echo $venda['id']; ?>" 
                                                               class="btn btn-outline-danger"
                                                               onclick="return confirm('Tem certeza que deseja cancelar esta venda?')"
                                                               title="Cancelar Venda">
                                                                <i class="bi bi-x-circle"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-receipt display-1 text-muted"></i>
                                <h4 class="text-muted">Nenhuma venda registrada</h4>
                                <p class="text-muted">Clique no botão abaixo para realizar a primeira venda.</p>
                                <a href="vendas-cadastrar.php" class="btn btn-primary btn-lg mt-3">
                                    <i class="bi bi-plus-circle"></i> Realizar Primeira Venda
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Estatísticas -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Total Vendas</h6>
                                        <h3><?php echo count($vendas); ?></h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-receipt display-6"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Pendentes</h6>
                                        <h3><?php echo count(array_filter($vendas, fn($v) => $v['status'] === 'pendente')); ?></h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-clock display-6"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Pagas</h6>
                                        <h3><?php echo count(array_filter($vendas, fn($v) => $v['status'] === 'pago')); ?></h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-check-circle display-6"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-danger">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Canceladas</h6>
                                        <h3><?php echo count(array_filter($vendas, fn($v) => $v['status'] === 'cancelado')); ?></h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-x-circle display-6"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Auto-fechar alerts após 5 segundos
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>

<?php
// Função para cor do badge baseado no status da venda
function getStatusVendaColor($status) {
    switch ($status) {
        case 'pendente':
            return 'warning';
        case 'pago':
            return 'success';
        case 'cancelado':
            return 'danger';
        default:
            return 'secondary';
    }
}
?>