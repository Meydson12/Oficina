<?php
session_start();
require_once '../config/database.php';
require_once '../config/funcoes.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$database = new Database();
$pdo = $database->getConnection();

$produtos_stmt = $pdo->query("SELECT * FROM produtos ORDER BY nome ASC");
$produtos = $produtos_stmt->fetchAll(PDO::FETCH_ASSOC);

$clientes_stmt = $pdo->query("SELECT id, cliente_nome, telefone FROM agendamentos GROUP BY cliente_nome ORDER BY cliente_nome ASC");
$clientes = $clientes_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente_id = $_POST['cliente_id'] ?: null;
    $itens = json_decode($_POST['itens_carrinho'], true);
    $total = $_POST['total_venda'];
    
    try {
        $pdo->beginTransaction();
        
        $stmt_venda = $pdo->prepare("INSERT INTO vendas (cliente_id, total) VALUES (?, ?)");
        $stmt_venda->execute([$cliente_id, $total]);
        $venda_id = $pdo->lastInsertId();
        
        foreach ($itens as $item) {
            $stmt_item = $pdo->prepare("INSERT INTO venda_itens (venda_id, produto_id, quantidade, preco_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
            $stmt_item->execute([$venda_id, $item['id'], $item['quantidade'], $item['preco'], $item['subtotal']]);
            
            if ($item['tipo'] == 'produto') {
                $stmt_estoque = $pdo->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ?");
                $stmt_estoque->execute([$item['quantidade'], $item['id']]);
            }
        }
        
        $pdo->commit();
        
        header("Location: ../vendas.php?sucesso=cadastrada");
        exit;
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        $erro = "Erro ao processar venda: " . $e->getMessage();
    }
}
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
    <title>Nova Venda - GenAuto</title>
    <style>
        .carrinho-item {
            border-left: 4px solid #007bff;
        }
        .produto-item:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }
        .carrinho-vazio {
            opacity: 0.6;
        }
        #total-venda {
            font-size: 1.5rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4" style="margin-top: 80px;">

                <!-- Alertas -->
                <?php if (isset($erro)): ?>
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        <strong>❌ Erro!</strong> <?php echo $erro; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>


                <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>
                        <i class="bi bi-cart-plus"></i> Nova Venda
                    </h2>
                    <a href="vendas.php" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>

                <form method="POST" id="form-venda">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-person"></i> Cliente
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <select class="form-select" id="cliente_id" name="cliente_id">
                                                <option value="">Venda Avulsa (Sem cliente)</option>
                                                <?php foreach ($clientes as $cliente): ?>
                                                    <option value="<?php echo $cliente['id']; ?>">
                                                        <?php echo htmlspecialchars($cliente['cliente_nome']); ?>
                                                        <?php if ($cliente['telefone']): ?>
                                                            - <?php echo htmlspecialchars($cliente['telefone']); ?>
                                                        <?php endif; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-outline-secondary w-100" onclick="document.getElementById('cliente_id').value = ''">
                                                <i class="bi bi-x-circle"></i> Limpar Cliente
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-box-seam"></i> Produtos e Serviços
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Nome</th>
                                                    <th>Tipo</th>
                                                    <th>Preço</th>
                                                    <th>Estoque</th>
                                                    <th>Ação</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($produtos as $produto): ?>
                                                    <tr class="produto-item" data-produto='<?php echo json_encode($produto); ?>'>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($produto['nome']); ?></strong>
                                                            <?php if ($produto['descricao']): ?>
                                                                <br><small class="text-muted"><?php echo htmlspecialchars(substr($produto['descricao'], 0, 50)); ?>...</small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-<?php echo $produto['tipo'] == 'produto' ? 'info' : 'warning'; ?>">
                                                                <?php echo ucfirst($produto['tipo']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <strong>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></strong>
                                                        </td>
                                                        <td>
                                                            <?php if ($produto['tipo'] == 'produto'): ?>
                                                                <span class="badge bg-<?php echo $produto['estoque'] > 0 ? 'success' : 'danger'; ?>">
                                                                    <?php echo $produto['estoque']; ?> un
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-success btn-adicionar" 
                                                                    <?php echo ($produto['tipo'] == 'produto' && $produto['estoque'] == 0) ? 'disabled' : ''; ?>>
                                                                <i class="bi bi-plus-circle"></i> Adicionar
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card sticky-top" style="top: 100px;">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-cart"></i> Carrinho
                                        <span class="badge bg-primary ms-2" id="contador-carrinho">0</span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div id="carrinho-vazio" class="text-center py-4 carrinho-vazio">
                                        <i class="bi bi-cart-x display-4"></i>
                                        <p class="mt-2">Carrinho vazio</p>
                                        <small class="text-muted">Adicione produtos ao carrinho</small>
                                    </div>

                                    <div id="carrinho-itens" style="display: none;"></div>

                                    <div class="border-top pt-3 mt-3" id="total-container" style="display: none;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5>Total:</h5>
                                            <h5 id="total-venda" class="text-success">R$ 0,00</h5>
                                        </div>
                                        
                                        <input type="hidden" name="itens_carrinho" id="itens_carrinho">
                                        <input type="hidden" name="total_venda" id="total_venda">
                                        
                                        <div class="d-grid gap-2 mt-3">
                                            <button type="submit" class="btn btn-success btn-lg" id="btn-finalizar">
                                                <i class="bi bi-check-circle"></i> Finalizar Venda
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/vendas-cadastrar.js"></script>
</body>
</html>