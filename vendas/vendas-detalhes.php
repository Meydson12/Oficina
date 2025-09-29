<?php
session_start();
require_once '../config/database.php';
require_once '../config/funcoes.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: vendas.php?erro=id_nao_informado");
    exit;
}

$venda_id = $_GET['id'];

$database = new Database();
$pdo = $database->getConnection();

$stmt_venda = $pdo->prepare("
    SELECT v.*, a.cliente_nome, a.telefone 
    FROM vendas v 
    LEFT JOIN agendamentos a ON v.cliente_id = a.id 
    WHERE v.id = ?
");
$stmt_venda->execute([$venda_id]);
$venda = $stmt_venda->fetch(PDO::FETCH_ASSOC);

if (!$venda) {
    header("Location: ../vendas.php?erro=venda_nao_encontrada");
    exit;
}

$stmt_itens = $pdo->prepare("
    SELECT vi.*, p.nome as produto_nome, p.tipo as produto_tipo 
    FROM venda_itens vi 
    JOIN produtos p ON vi.produto_id = p.id 
    WHERE vi.venda_id = ?
");
$stmt_itens->execute([$venda_id]);
$itens = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="imagens/engrenagem.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/responsivo.css">
    <link rel="stylesheet" href="../css/recibo.css">
    <title>Detalhes da Venda #<?php echo $venda_id; ?> - GenAuto</title>
    <style>
        .recibo {
            border: 2px solid #dee2e6;
            border-radius: 10px;
            background: white;
        }

        .recibo-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px 8px 0 0;
        }

        .item-venda {
            border-left: 4px solid #28a745;
        }

        .badge-status {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }
    </style>
</head>

<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar.php'; ?>



            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4" style="margin-top: 80px;">


                <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>
                        <i class="bi bi-receipt"></i> Detalhes da Venda
                    </h2>
                    <div>
                        <a href="vendas.php" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                        <button onclick="imprimirRecibo()" class="btn btn-primary btn-sm ms-2">
                            <i class="bi bi-printer"></i> Imprimir Recibo
                        </button>
                    </div>
                </div>





                <!-- Recibo -->
                <div class="recibo-elegante">
                    <div class="header text-center">
                        <div class="logo mb-3">
                            <i class="bi bi-gear-fill display-4"></i>
                        </div>
                        <h2 class="fw-bold mb-1">GenAuto</h2>
                        <p class="mb-0 opacity-75">Excelência em serviços automotivos</p>
                    </div>

                    <div class="content">
                        <!-- Informações -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h5 class="text-dark mb-3">📋 Detalhes da Venda</h5>
                                <div class="info-item mb-2">
                                    <strong>Nº Venda:</strong>
                                    <span class="text-primary">#<?php echo str_pad($venda_id, 4, '0', STR_PAD_LEFT); ?></span>
                                </div>
                                <div class="info-item mb-2">
                                    <strong>Data:</strong>
                                    <?php echo date('d/m/Y \à\s H:i', strtotime($venda['data_venda'])); ?>
                                </div>
                                <?php if ($venda['cliente_nome']): ?>
                                    <div class="info-item">
                                        <strong>Cliente:</strong>
                                        <?php echo htmlspecialchars($venda['cliente_nome']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="status-badge p-3 rounded" style="background: #f8f9fa;">
                                    <small class="text-muted d-block">Status</small>
                                    <span class="badge bg-<?php echo getStatusVendaColor($venda['status']); ?> mt-1">
                                        <?php echo ucfirst($venda['status']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Itens -->
                        <h5 class="text-dark mb-3">🛒 Itens Vendidos</h5>
                        <?php foreach ($itens as $item): ?>
                            <div class="recibo-item">
                                <div class="item-info">
                                    <div class="fw-bold"><?php echo htmlspecialchars($item['produto_nome']); ?></div>
                                    <small class="text-muted">
                                        <?php echo $item['quantidade']; ?> x
                                        R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?>
                                        <span class="badge bg-<?php echo $item['produto_tipo'] == 'produto' ? 'info' : 'warning'; ?> ms-2">
                                            <?php echo $item['produto_tipo'] == 'produto' ? 'Produto' : 'Serviço'; ?>
                                        </span>
                                    </small>
                                </div>
                                <div class="item-total fw-bold">
                                    R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Total -->
                        <div class="total-section mt-4 p-4 rounded" style="background: linear-gradient(135deg, #27ae60, #2ecc71); color: white;">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Total</h4>
                                <h2 class="mb-0">R$ <?php echo number_format($venda['total'], 2, ',', '.'); ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4 d-print-none">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-info-circle"></i> Informações Técnicas
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>ID da Venda:</strong> <?php echo $venda_id; ?>
                            </div>
                            <div class="col-md-4">
                                <strong>Data de Criação:</strong> <?php echo date('d/m/Y H:i', strtotime($venda['data_venda'])); ?>
                            </div>
                            <div class="col-md-4">
                                <strong>ID do Cliente:</strong> <?php echo $venda['cliente_id'] ?: 'N/A'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
                            <script src="../js/imprimir.js"></script>
</html>

<?php
// Função para cor do badge baseado no status da venda
function getStatusVendaColor($status)
{
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