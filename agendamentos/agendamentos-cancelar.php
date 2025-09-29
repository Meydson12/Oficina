<?php

if (!isset($_GET['id'])) {
    header("Location: agendamentos.php?erro=id_nao_informado");
    exit;
}

$id = intval($_GET['id']);

require_once '../config/database.php';
require_once '../config/funcoes.php';

$database = new Database();
$pdo = $database->getConnection();

if ($pdo) {
    try {
        $sql = "UPDATE agendamentos SET status = 'cancelado' WHERE id = ?";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([$id])) {
            header("Location: ../agendamentos.php?sucesso=cancelado");
        } else {
            header("Location: ../agendamentos.php?erro=cancelamento");
        }
    } catch (PDOException $e) {
        header("Location: agendamentos.php?erro=banco_dados");
    }
} else {
    header("Location: agendamentos.php?erro=conexao");
}
exit;
