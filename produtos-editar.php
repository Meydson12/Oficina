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

// Buscar dados do produto
$produto = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$produto) {
    header("Location: produtos.php");
    exit;
}

// Processar atualização
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $tipo = $_POST['tipo'];
    $preco = $_POST['preco'];
    $estoque = $_POST['estoque'];
    $descricao = $_POST['descricao'];
    
    // Se for serviço, estoque deve ser 0
    if ($tipo == 'servico') {
        $estoque = 0;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE produtos SET nome = ?, tipo = ?, preco = ?, estoque = ?, descricao = ? WHERE id = ?");
        
        if ($stmt->execute([$nome, $tipo, $preco, $estoque, $descricao, $id])) {
            header("Location: produtos.php?sucesso=editado");
            exit;
        }
    } catch (PDOException $e) {
        $erro = "Erro ao atualizar produto: " . $e->getMessage();
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
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/responsivo.css">
    <title>Editar Produto - GenAuto</title>
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
                <?php if (isset($erro)): ?>
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        <strong>❌ Erro!</strong> <?php echo $erro; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>
                        <i class="bi bi-pencil-square"></i> Editar Produto/Serviço
                    </h2>
                    <a href="produtos.php" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>

                <!-- Formulário -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-box"></i> Editando: <?php echo htmlspecialchars($produto['nome']); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nome" class="form-label">Nome *</label>
                                        <input type="text" class="form-control" id="nome" name="nome" required 
                                               value="<?php echo htmlspecialchars($produto['nome']); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tipo" class="form-label">Tipo *</label>
                                        <select class="form-select" id="tipo" name="tipo" required>
                                            <option value="produto" <?php echo $produto['tipo'] == 'produto' ? 'selected' : ''; ?>>Produto</option>
                                            <option value="servico" <?php echo $produto['tipo'] == 'servico' ? 'selected' : ''; ?>>Serviço</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="preco" class="form-label">Preço (R$) *</label>
                                        <input type="number" step="0.01" min="0" class="form-control" id="preco" name="preco" required 
                                               value="<?php echo number_format($produto['preco'], 2, '.', ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="estoque" class="form-label">Estoque</label>
                                        <input type="number" min="0" class="form-control" id="estoque" name="estoque" 
                                               value="<?php echo $produto['estoque']; ?>"
                                               <?php echo $produto['tipo'] == 'servico' ? 'readonly' : ''; ?>>
                                        <div class="form-text">
                                            <?php echo $produto['tipo'] == 'servico' ? 'Serviços não têm controle de estoque' : 'Apenas para produtos'; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control" id="descricao" name="descricao" rows="3"><?php echo htmlspecialchars($produto['descricao']); ?></textarea>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="produtos.php" class="btn btn-secondary me-md-2">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Atualizar Produto
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Mostrar/ocultar estoque baseado no tipo
        document.getElementById('tipo').addEventListener('change', function() {
            const estoqueField = document.getElementById('estoque');
            if (this.value === 'servico') {
                estoqueField.value = '0';
                estoqueField.readOnly = true;
                estoqueField.parentNode.querySelector('.form-text').textContent = 'Serviços não têm controle de estoque';
            } else {
                estoqueField.readOnly = false;
                estoqueField.parentNode.querySelector('.form-text').textContent = 'Apenas para produtos';
            }
        });
    </script>
</body>
</html>