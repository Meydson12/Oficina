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

// 🔍 Buscar agendamentos
$stmt = $pdo->query("SELECT * FROM agendamentos ORDER BY data_agendamento DESC");
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="imagens/engrenagem.png" type="image/x-icon">
    <title>GenAuto - Agendamentos</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Sucesso!</strong> Agendamento atualizado com êxito.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <!-- Navbar -->
    <?php include 'includes/navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4" style="margin-top: 80px;">


                <!-- Alerta atualização-->
                <?php if (isset($_GET['sucesso'])): ?>
                    <?php if ($_GET['sucesso'] == '1'): ?>
                        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                            <strong>✅ Sucesso!</strong> Agendamento atualizado com êxito.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>

                        <!-- Alerta cancelado-->
                    <?php elseif ($_GET['sucesso'] == 'cancelado'): ?>
                        <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                            <strong>⚠️ Sucesso!</strong> Agendamento cancelado com êxito.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (isset($_GET['erro'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        <strong>❌ Erro:
                            <?php
                            switch ($_GET['erro']) {
                                case 'cancelamento':
                                    echo 'Falha ao cancelar agendamento';
                                    break;
                                case 'id_nao_informado':
                                    echo 'ID do agendamento não informado';
                                    break;
                                case 'banco_dados':
                                    echo 'Erro no banco de dados';
                                    break;
                                default:
                                    echo 'Erro desconhecido';
                            }
                            ?>
                        </strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>





                <!-- Filtros -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label for="data" class="form-label">Data</label>
                                <input type="date" class="form-control" id="data" name="data">
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Todos</option>
                                    <option value="agendado">Agendado</option>
                                    <option value="em_andamento">Em Andamento</option>
                                    <option value="concluido">Concluído</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-funnel"></i> Filtrar
                                </button>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <a href="agendamentos.php" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-arrow-clockwise"></i> Limpar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabela de Agendamentos -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-list-ul me-2"></i> Lista de Agendamentos
                            <span class="badge bg-primary ms-2"><?php echo count($agendamentos); ?></span>
                        </h5>

                        <a href="agendamentos-cadastrar.php" class="btn btn-success btn-sm py-1">
                            <i class="bi bi-plus-circle me-1"></i> Novo
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (count($agendamentos) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Cliente</th>
                                            <th>Veículo</th>
                                            <th>Serviço</th>
                                            <th>Data/Hora</th>
                                            <th>Status</th>
                                            <th>Valor</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($agendamentos as $agendamento): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($agendamento['cliente_nome']); ?></strong>
                                                    <?php if (!empty($agendamento['telefone'])): ?>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars($agendamento['telefone']); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($agendamento['veiculo']); ?>
                                                    <?php if (!empty($agendamento['placa'])): ?>
                                                        <br><small class="text-muted">Placa: <?php echo htmlspecialchars($agendamento['placa']); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span title="<?php echo htmlspecialchars($agendamento['servico']); ?>">
                                                        <?php echo strlen($agendamento['servico']) > 50 ?
                                                            substr($agendamento['servico'], 0, 50) . '...' :
                                                            $agendamento['servico']; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo formatarData($agendamento['data_agendamento']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo getBadgeColor($agendamento['status']); ?>">
                                                        <?php echo ucfirst($agendamento['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong>R$ <?php echo number_format($agendamento['valor_estimado'], 2, ',', '.'); ?></strong>
                                                </td>

                                                <td>
                                                    <!-- Tabela de Agendamentos -->
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="agendamentos-editar.php?id=<?php echo $agendamento['id']; ?>"
                                                            class="btn btn-outline-primary" title="Editar">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <!-- Tabela de exclusão -->
                                                        <?php if ($agendamento['status'] != 'cancelado'): ?>
                                                            <a href="agendamentos-cancelar.php?id=<?php echo $agendamento['id']; ?>"
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('Tem certeza que deseja cancelar o agendamento de <?php echo htmlspecialchars($agendamento['cliente_nome']); ?>?')">
                                                                <i class="bi bi-x-circle"></i>
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Cancelado</span>
                                                        <?php endif; ?>
                                                        <!-- fim da exclusão -->



                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-calendar-x display-1 text-muted"></i>
                                <h4 class="text-muted">Nenhum agendamento encontrado</h4>
                                <p class="text-muted">Clique no botão abaixo para cadastrar o primeiro agendamento.</p>
                                <!-- 🎯 BOTÃO ALTERNATIVO QUANDO NÃO HÁ AGENDAMENTOS -->
                                <a href="agendamento-cadastrar.php" class="btn btn-primary btn-lg mt-3">
                                    <i class="bi bi-plus-circle"></i> Criar Primeiro Agendamento
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Estatísticas Rápidas -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Total</h6>
                                        <h3><?php echo count($agendamentos); ?></h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-calendar-check display-6"></i>
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
                                        <h6>Agendados</h6>
                                        <h3><?php echo count(array_filter($agendamentos, fn($a) => $a['status'] === 'agendado')); ?></h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-clock display-6"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Em Andamento</h6>
                                        <h3><?php echo count(array_filter($agendamentos, fn($a) => $a['status'] === 'em_andamento')); ?></h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-gear display-6"></i>
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
                                        <h6>Concluídos</h6>
                                        <h3><?php echo count(array_filter($agendamentos, fn($a) => $a['status'] === 'concluido')); ?></h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-check-circle display-6"></i>
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

        // Focar no campo de busca ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            const searchField = document.querySelector('input[type="search"]');
            if (searchField) {
                searchField.focus();
            }
        });
    </script>
</body>

</html>

<?php
// Função para cor do badge baseado no status
function getBadgeColor($status)
{
    switch ($status) {
        case 'agendado':
            return 'warning';
        case 'em_andamento':
            return 'info';
        case 'concluido':
            return 'success';
        case 'cancelado':
            return 'danger';
        default:
            return 'secondary';
    }
}
?>