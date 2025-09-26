<?php
session_start();
require_once 'config/database.php';
require_once 'config/funcoes.php';

// 🔐 Verificar login
if(!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

// 📦 Conectar ao banco
$database = new Database();
$db = $database->getConnection();

// 🎯 Processar formulário
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 🧹 Sanitizar e validar dados
        $cliente_nome = trim($_POST['cliente_nome']);
        $telefone = trim($_POST['telefone'] ?? '');
        $veiculo = trim($_POST['veiculo']);
        $placa = trim($_POST['placa'] ?? '');
        $servico = trim($_POST['servico']);
        $data_agendamento = $_POST['data_agendamento'];
        $valor_estimado = $_POST['valor_estimado'] ? floatval($_POST['valor_estimado']) : 0;
        $observacoes = trim($_POST['observacoes'] ?? '');

        // ✅ Validações
        if(empty($cliente_nome) || empty($veiculo) || empty($servico) || empty($data_agendamento)) {
            throw new Exception("Preencha todos os campos obrigatórios!");
        }

        if($valor_estimado < 0) {
            throw new Exception("Valor estimado não pode ser negativo!");
        }

        // 💾 Inserir no banco
        $query = "INSERT INTO agendamentos 
                  (cliente_nome, telefone, veiculo, placa, servico, data_agendamento, valor_estimado, observacoes, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'agendado')";
        
        $stmt = $db->prepare($query);
        $success = $stmt->execute([
            $cliente_nome,
            $telefone,
            $veiculo,
            $placa,
            $servico,
            $data_agendamento,
            $valor_estimado,
            $observacoes
        ]);

        if($success) {
            // 📝 Registrar log (opcional)
            registrarLogAgendamento($db, $_SESSION['usuario_id'], 'criacao', $cliente_nome);
            
            // 🔄 Redirecionar com sucesso
            header("Location: agendamentos.php?sucesso=1");
            exit;
        } else {
            throw new Exception("Erro ao salvar agendamento no banco de dados");
        }

    } catch (Exception $e) {
        $erro = $e->getMessage();
        header("Location: agendamento-cadastrar.php?erro=" . urlencode($erro));
        exit;
    }
} else {
    // 🚫 Se não for POST, redirecionar
    header("Location: agendamento-cadastrar.php");
    exit;
}

/**
 * Registrar log de agendamento
 */
function registrarLogAgendamento($db, $usuario_id, $acao, $cliente_nome) {
    try {
        $query = "INSERT INTO logs_sistema (usuario_id, acao, descricao) 
                  VALUES (?, 'agendamento', ?)";
        
        $descricao = "Agendamento {$acao} para cliente: {$cliente_nome}";
        $stmt = $db->prepare($query);
        $stmt->execute([$usuario_id, $descricao]);
    } catch (Exception $e) {
        // Não quebrar o fluxo se o log falhar
        error_log("Erro ao registrar log: " . $e->getMessage());
    }
}
?>