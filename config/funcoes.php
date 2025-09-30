<?php
function formatarData($data)
{
    if (empty($data)) return '-';
    return date('d/m/Y H:i', strtotime($data));
}

function formatarHora($data)
{
    if (empty($data)) return '-';
    return date('H:i', strtotime($data));
}

function formatarMoeda($valor)
{
    if (empty($valor)) return 'R$ 0,00';
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function calcularTotalVendasMes($db)
{
    try {
        $query = "SELECT COALESCE(SUM(valor_estimado), 0) as total 
                  FROM agendamentos 
                  WHERE status = 'concluido' 
                  AND MONTH(data_agendamento) = MONTH(CURRENT_DATE())
                  AND YEAR(data_agendamento) = YEAR(CURRENT_DATE())";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado['total'];
    } catch (Exception $e) {
        return 0;
    }
}

function contarAgendamentosHoje($db)
{
    try {
        $query = "SELECT COUNT(*) as total 
                  FROM agendamentos 
                  WHERE DATE(data_agendamento) = CURDATE()";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado['total'];
    } catch (Exception $e) {
        return 0;
    }
}
function pecasEstoqueBaixo($db)
{
    try {
        $query = "SELECT COUNT(*) as total 
                  FROM estoque 
                  WHERE quantidade <= estoque_minimo";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado['total'];
    } catch (Exception $e) {
        return 0;
    }
}

function agendamentosDoDia($db)
{
    try {
        $query = "SELECT cliente_nome, veiculo, servico, data_agendamento 
                  FROM agendamentos 
                  WHERE DATE(data_agendamento) = CURDATE() 
                  AND status != 'concluido'
                  ORDER BY data_agendamento ASC";

        $stmt = $db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function sanitizar($dados)
{
    if (is_array($dados)) {
        return array_map('sanitizar', $dados);
    }
    return htmlspecialchars(trim($dados), ENT_QUOTES, 'UTF-8');
}

function isAdmin()
{
    return ($_SESSION['usuario_nivel'] ?? '') === 'admin';
}

function redirect($url, $mensagem = '')
{
    if (!empty($mensagem)) {
        $_SESSION['mensagem'] = $mensagem;
    }
    header("Location: $url");
    exit;
}
?>

<!-- FUNÇÃO DO PIX QUE PEGUEI NA INTERNET-->
<?php
function exibirInstrucoesPagamento($valor, $venda_id)
{
    $config = [
        'chave' => 'af52a422-113c-406b-a37d-2a9250d3bc5a',
        'nome' => 'Seu Nome',
        'cidade' => 'Boa Vista',
        'uf' => 'RR'
    ];
?>
    <div class="card border-primary shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-credit-card"></i> PAGAMENTO PIX</h5>
            <span class="badge bg-light text-primary">AGUARDANDO PAGAMENTO</span>
        </div>
        <div class="card-body">

            <!-- Alertas -->
            <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 'pago'): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle"></i>
                    <strong>Pagamento confirmado!</strong> A venda foi marcada como paga.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Chave PIX e Valor -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white py-2">
                            <small><i class="bi bi-key"></i> CHAVE PIX</small>
                        </div>
                        <div class="card-body">
                            <div class="input-group">
                                <input type="text"
                                    class="form-control text-center fw-bold border-0 bg-light"
                                    value="<?php echo $config['chave']; ?>"
                                    id="chavePixFinal"
                                    readonly
                                    style="font-size: 0.9rem;">
                                <button class="btn btn-success"
                                    onclick="copiarChaveFinal()">
                                    <i class="bi bi-copy"></i> Copiar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark py-2">
                            <small><i class="bi bi-currency-dollar"></i> VALOR</small>
                        </div>
                        <div class="card-body text-center">
                            <h3 class="text-success mb-0">R$ <?php echo number_format($valor, 2, ',', '.'); ?></h3>
                            <small class="text-muted">Venda #<?php echo $venda_id; ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botão de Confirmação -->
            <div class="text-center mt-4 p-4 bg-light rounded">
                <h5 class="text-success mb-3">
                    <i class="bi bi-question-circle"></i> Já realizou o pagamento?
                </h5>
                <p class="text-muted mb-4">
                    Após pagar, clique no botão abaixo para confirmar o pagamento.
                </p>

                <a href="vendas-pagar.php?id=<?php echo $venda_id; ?>"
                    class="btn btn-success btn-lg"
                    onclick="return confirm('Confirmar que o pagamento da venda #<?php echo $venda_id; ?> foi realizado?')">
                    <i class="bi bi-check-circle"></i> Pago
                </a>

                <div class="mt-3">
                    <small class="text-muted">
                        Esta ação irá atualizar o status da venda para <strong>"PAGO"</strong>
                    </small>
                </div>
            </div>

            <!-- Instruções -->
            <div class="mt-4">
                <h6><i class="bi bi-list-ol"></i> Como pagar:</h6>
                <ol class="list-group list-group-numbered">
                    <li class="list-group-item border-0">Copie a chave PIX acima</li>
                    <li class="list-group-item border-0">Abra seu app bancário</li>
                    <li class="list-group-item border-0">Cole a chave no campo PIX</li>
                    <li class="list-group-item border-0">Digite o valor: <strong>R$ <?php echo number_format($valor, 2, ',', '.'); ?></strong></li>
                    <li class="list-group-item border-0">Confirme o pagamento</li>
                    <li class="list-group-item border-0 text-success">
                        <strong>Volte aqui e clique em "Pago"</strong>
                    </li>
                </ol>
            </div>
        </div>
    </div>

    <script>
        function copiarChaveFinal() {
            navigator.clipboard.writeText('<?php echo $config['chave']; ?>').then(function() {
                const btn = event.target;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check"></i> Copiado!';
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-success');

                setTimeout(function() {
                    btn.innerHTML = originalText;
                    btn.classList.remove('btn-outline-success');
                    btn.classList.add('btn-success');
                }, 2000);
            });
        }
    </script>
<?php
}


function marcarVendaComoPaga($venda_id)
{
    $database = new Database();
    $pdo = $database->getConnection();

    try {
        $stmt = $pdo->prepare("UPDATE vendas SET status = 'pago' WHERE id = ?");
        $stmt->execute([$venda_id]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}
