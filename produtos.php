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

// 🔍 Buscar produtos
$stmt = $pdo->query("SELECT * FROM produtos ORDER BY nome ASC");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>GenAuto - Produtos e Serviços</title>
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
                        switch ($_GET['sucesso']) {
                            case 'cadastrado':
                                echo 'Produto cadastrado com sucesso!';
                                break;
                            case 'editado':
                                echo 'Produto atualizado com sucesso!';
                                break;
                            case 'excluido':
                                echo 'Produto excluído com sucesso!';
                                break;
                            default:
                                echo 'Operação realizada com sucesso!';
                        }
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>
                        <i class="bi bi-box-seam"></i> Produtos e Serviços
                    </h2>
                    <a href="produtos-cadastrar.php" class="btn btn-success btn-sm">
                        <i class="bi bi-plus-circle"></i> Novo Produto/Serviço
                    </a>
                </div>

                <!-- Tabela de Produtos -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-list-ul"></i> Lista de Produtos/Serviços
                            <span class="badge bg-primary ms-2"><?php echo count($produtos); ?></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($produtos) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Nome</th>
                                            <th>Tipo</th>
                                            <th>Preço</th>
                                            <th>Estoque</th>
                                            <th>Descrição</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($produtos as $produto): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($produto['nome']); ?></strong>
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
                                                        <span class="badge bg-<?php echo $produto['estoque'] > 5 ? 'success' : ($produto['estoque'] > 0 ? 'warning' : 'danger'); ?>">
                                                            <?php echo $produto['estoque']; ?> unidades
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($produto['descricao'])): ?>
                                                        <span title="<?php echo htmlspecialchars($produto['descricao']); ?>">
                                                            <?php echo strlen($produto['descricao']) > 50 ?
                                                                substr($produto['descricao'], 0, 50) . '...' :
                                                                $produto['descricao']; ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">Sem descrição</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="produtos-editar.php?id=<?php echo $produto['id']; ?>"
                                                            class="btn btn-outline-primary" title="Editar">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <a href="produtos-excluir.php?id=<?php echo $produto['id']; ?>"
                                                            class="btn btn-outline-danger"
                                                            onclick="return confirm('Tem certeza que deseja excluir <?php echo htmlspecialchars($produto['nome']); ?>?')"
                                                            title="Excluir">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-box display-1 text-muted"></i>
                                <h4 class="text-muted">Nenhum produto/serviço cadastrado</h4>
                                <p class="text-muted">Clique no botão abaixo para cadastrar o primeiro item.</p>
                                <a href="produtos-cadastrar.php" class="btn btn-primary btn-lg mt-3">
                                    <i class="bi bi-plus-circle"></i> Cadastrar Primeiro Produto
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Estatísticas -->
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Total</h6>
                                        <h3><?php echo count($produtos); ?></h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-box display-6"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Produtos</h6>
                                        <h3><?php echo count(array_filter($produtos, fn($p) => $p['tipo'] === 'produto')); ?></h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-box-seam display-6"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Serviços</h6>
                                        <h3><?php echo count(array_filter($produtos, fn($p) => $p['tipo'] === 'servico')); ?></h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-tools display-6"></i>
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