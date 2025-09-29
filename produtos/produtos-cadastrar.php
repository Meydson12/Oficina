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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $tipo = $_POST['tipo'];
    $preco = $_POST['preco'];
    $estoque = $_POST['estoque'];
    $descricao = $_POST['descricao'];
    
    if ($tipo == 'servico') {
        $estoque = 0;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO produtos (nome, tipo, preco, estoque, descricao) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$nome, $tipo, $preco, $estoque, $descricao])) {
            header("Location: ../produtos.php?sucesso=cadastrado");
            exit;
        }
    } catch (PDOException $e) {
        $erro = "Erro ao cadastrar produto: " . $e->getMessage();
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
    <title>Cadastrar Produto - GenAuto</title>
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
                        <i class="bi bi-plus-circle"></i> Cadastrar Produto/Serviço
                    </h2>
                    <a href="produtos-cadastrar.php" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>

                <!-- Formulário -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-box"></i> Informações do Produto/Serviço
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nome" class="form-label">Nome *</label>
                                        <input type="text" class="form-control" id="nome" name="nome" required 
                                               placeholder="Ex: Pastilha de Freio, Troca de Óleo">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tipo" class="form-label">Tipo *</label>
                                        <select class="form-select" id="tipo" name="tipo" required>
                                            <option value="">Selecione o tipo</option>
                                            <option value="produto">Produto</option>
                                            <option value="servico">Serviço</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="preco" class="form-label">Preço (R$) *</label>
                                        <input type="number" step="0.01" min="0" class="form-control" id="preco" name="preco" required 
                                               placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="estoque" class="form-label">Estoque Inicial</label>
                                        <input type="number" min="0" class="form-control" id="estoque" name="estoque" value="0"
                                               placeholder="Apenas para produtos">
                                        <div class="form-text">Apenas para produtos. Serviços ficam com estoque 0.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control" id="descricao" name="descricao" rows="3" 
                                          placeholder="Descrição detalhada do produto ou serviço..."></textarea>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="../produtos.php" class="btn btn-secondary me-md-2">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Cadastrar Produto
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-lightbulb"></i> Dicas
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li><strong>Produtos:</strong> Itens físicos que têm estoque (peças, acessórios)</li>
                            <li><strong>Serviços:</strong> Mão-de-obra que não tem estoque (reparos, manutenções)</li>
                            <li>Use descrições claras para facilitar a identificação</li>
                        </ul>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        
        document.getElementById('tipo').addEventListener('change', function() {
            const estoqueField = document.getElementById('estoque');
            if (this.value === 'servico') {
                estoqueField.value = '0';
                estoqueField.readOnly = true;
                estoqueField.parentNode.querySelector('.form-text').textContent = 'Serviços não têm controle de estoque';
            } else {
                estoqueField.readOnly = false;
                estoqueField.parentNode.querySelector('.form-text').textContent = 'Apenas para produtos. Serviços ficam com estoque 0.';
            }
        });
    </script>
</body>
</html>