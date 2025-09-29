<?php
// editar.php
require_once '../config/database.php';
require_once '../config/funcoes.php';

$database = new Database();
$pdo = $database->getConnection();

// Buscar dados do agendamento
$agendamento = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM agendamentos WHERE id = ?");
    $stmt->execute([$id]);
    $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$agendamento) {
    header("Location: ../agendamentos.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente_nome = isset($_POST['cliente_nome']) ? $_POST['cliente_nome'] : '';
    $veiculo = isset($_POST['veiculo']) ? $_POST['veiculo'] : '';
    $placa = isset($_POST['placa']) ? $_POST['placa'] : '';
    $servico = isset($_POST['servico']) ? $_POST['servico'] : '';
    $data_agendamento = isset($_POST['data_agendamento']) ? $_POST['data_agendamento'] : '';
    $valor_estimado = isset($_POST['valor_estimado']) ? $_POST['valor_estimado'] : null;
    $telefone = isset($_POST['telefone']) ? $_POST['telefone'] : '';
    $observacoes = isset($_POST['observacoes']) ? $_POST['observacoes'] : '';

    if ($valor_estimado === '') {
        $valor_estimado = null;
    }

    try {
        $stmt = $pdo->prepare("UPDATE agendamentos SET 
            cliente_nome = ?, 
            telefone = ?,
            veiculo = ?, 
            placa = ?,
            servico = ?, 
            data_agendamento = ?, 
            valor_estimado = ?,
            observacoes = ?
            WHERE id = ?");

        if ($stmt->execute([$cliente_nome, $telefone, $veiculo, $placa, $servico, $data_agendamento, $valor_estimado, $observacoes, $id])) {
    header("Location: ../agendamentos.php?sucesso=editado");
    exit;
        } else {
            $erro = "Erro ao atualizar agendamento.";
        }
    } catch (PDOException $e) {
        $erro = "Erro no banco de dados: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Agendamento - GenAuto</title>
    <link rel="icon" href="../imagens/engrenagem.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</head>

<body>
    <div class="container mt-4">
        <?php if (isset($erro)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Erro!</strong> <?php echo $erro; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-10 mx-auto">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-pencil-square"></i> Editar Agendamento #<?php echo $agendamento['id']; ?>
                        </h5>
                        <a href="agendamentos.php" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cliente_nome" class="form-label">Cliente *</label>
                                        <input type="text" class="form-control" id="cliente_nome" name="cliente_nome"
                                            value="<?php echo htmlspecialchars($agendamento['cliente_nome']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="telefone" class="form-label">Telefone</label>
                                        <input type="text" class="form-control" id="telefone" name="telefone"
                                            value="<?php echo htmlspecialchars($agendamento['telefone']); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="veiculo" class="form-label">Veículo *</label>
                                        <input type="text" class="form-control" id="veiculo" name="veiculo"
                                            value="<?php echo htmlspecialchars($agendamento['veiculo']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="placa" class="form-label">Placa</label>
                                        <input type="text" class="form-control" id="placa" name="placa"
                                            value="<?php echo htmlspecialchars($agendamento['placa']); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="servico" class="form-label">Serviço *</label>
                                <textarea class="form-control" id="servico" name="servico" rows="3" required><?php echo htmlspecialchars($agendamento['servico']); ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="data_agendamento" class="form-label">Data e Hora *</label>
                                        <input type="datetime-local" class="form-control" id="data_agendamento" name="data_agendamento"
                                            value="<?php echo date('Y-m-d\TH:i', strtotime($agendamento['data_agendamento'])); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="valor_estimado" class="form-label">Valor Estimado (R$)</label>
                                        <input type="number" step="0.01" min="0" class="form-control" id="valor_estimado" name="valor_estimado"
                                            value="<?php echo $agendamento['valor_estimado'] ? number_format($agendamento['valor_estimado'], 2, '.', '') : ''; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea class="form-control" id="observacoes" name="observacoes" rows="2"><?php echo htmlspecialchars($agendamento['observacoes']); ?></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Atualizar Agendamento
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>