<?php
session_start();
require_once '../config/database.php';
require_once '../config/funcoes.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$paginaTitulo = "Novo Agendamento";
?>




<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../imagens/engrenagem.png" type="image/x-icon">
    <title>GenAuto - cadastrar <?php echo $paginaTitulo; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    
</head>

<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="bi bi-plus-circle"></i> Novo Agendamento
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="../agendamentos.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar para Lista
                        </a>
                    </div>
                </div>




                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calendar-plus"></i> Preencha os dados do agendamento
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="agendamento-salvar.php" method="POST">
                            <div class="row">
                                <!-- Dados do Cliente -->
                                <div class="col-md-6">
                                    <h6 class="border-bottom pb-2 mb-3">
                                        <i class="bi bi-person"></i> Dados do Cliente
                                    </h6>

                                    <div class="mb-3">
                                        <label for="cliente_nome" class="form-label">Nome do Cliente *</label>
                                        <input type="text" class="form-control" id="cliente_nome" name="cliente_nome"
                                            required placeholder="Digite o nome completo">
                                    </div>

                                    <div class="mb-3">
                                        <label for="telefone" class="form-label">Telefone</label>
                                        <input type="tel" class="form-control" id="telefone" name="telefone"
                                            placeholder="(11) 99999-9999">
                                    </div>
                                </div>

                                <!-- Dados do Veículo -->
                                <div class="col-md-6">
                                    <h6 class="border-bottom pb-2 mb-3">
                                        <i class="bi bi-car-front"></i> Dados do Veículo
                                    </h6>

                                    <div class="mb-3">
                                        <label for="veiculo" class="form-label">Veículo *</label>
                                        <input type="text" class="form-control" id="veiculo" name="veiculo"
                                            required placeholder="Ex: Honda Civic 2020">
                                    </div>

                                    <div class="mb-3">
                                        <label for="placa" class="form-label">Placa</label>
                                        <input type="text" class="form-control" id="placa" name="placa"
                                            placeholder="ABC1D23" style="text-transform: uppercase;">
                                    </div>
                                </div>
                            </div>

                            <!-- Dados do Serviço -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6 class="border-bottom pb-2 mb-3">
                                        <i class="bi bi-tools"></i> Dados do Serviço
                                    </h6>

                                    <div class="mb-3">
                                        <label for="servico" class="form-label">Serviço a ser realizado *</label>
                                        <textarea class="form-control" id="servico" name="servico" rows="4"
                                            required placeholder="Descreva detalhadamente o serviço..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Data e Valor -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="data_agendamento" class="form-label">Data e Hora *</label>
                                        <input type="datetime-local" class="form-control" id="data_agendamento"
                                            name="data_agendamento" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="valor_estimado" class="form-label">Valor Estimado (R$)</label>
                                        <input type="number" class="form-control" id="valor_estimado"
                                            name="valor_estimado" step="0.01" min="0" placeholder="0,00">
                                    </div>
                                </div>
                            </div>

                            <!-- Observações -->
                            <div class="mb-3">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea class="form-control" id="observacoes" name="observacoes" rows="3"
                                    placeholder="Observações adicionais..."></textarea>
                            </div>

                            <!-- Botões -->
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="../agendamentos.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Salvar Agendamento
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Informações -->
                <div class="alert alert-info mt-4">
                    <h6><i class="bi bi-info-circle"></i> Informações importantes:</h6>
                    <ul class="mb-0">
                        <li>Campos marcados com * são obrigatórios</li>
                        <li>O agendamento será criado com status "Agendado"</li>
                        <li>Você poderá editar essas informações posteriormente</li>
                    </ul>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            now.setHours(now.getHours() + 1);
            now.setMinutes(0);
            now.setSeconds(0);

            const dateTimeLocal = now.toISOString().slice(0, 16);
            document.getElementById('data_agendamento').value = dateTimeLocal;

            document.getElementById('telefone').addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 11) value = value.slice(0, 11);

                if (value.length > 10) {
                    value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
                } else if (value.length > 6) {
                    value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
                } else if (value.length > 2) {
                    value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
                }
                e.target.value = value;
            });
        });
    </script>
</body>

</html>