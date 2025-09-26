<?php
// agendamentos-concluir.php
require_once 'config/database.php';
require_once 'config/funcoes.php';

// Verificar se o ID foi passado
if (!isset($_GET['id'])) {
    header("Location: agendamentos.php?erro=id_nao_informado");
    exit;
}

$id = intval($_GET['id']);

// Conectar ao banco
$database = new Database();
$pdo = $database->getConnection();

try {
    // Atualizar status para 'concluido'
    $sql = "UPDATE agendamentos SET status = 'concluido' WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$id])) {
        header("Location: agendamentos.php?sucesso=concluido");
    } else {
        header("Location: agendamentos.php?erro=conclusao");
    }
} catch (PDOException $e) {
    header("Location: agendamentos.php?erro=banco_dados");
}
exit;
?>