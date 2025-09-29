<?php
session_start();
require_once '../config/database.php';
require_once '../config/funcoes.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}
if (!isset($_GET['id'])) {
    header("Location: ../produtos.php?erro=id_nao_informado");
    exit;
}

$id = $_GET['id'];

$database = new Database();
$pdo = $database->getConnection();

try {
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$produto) {
        header("Location: ../produtos.php?erro=produto_nao_encontrado");
        exit;
    }
    
    $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
    
    if ($stmt->execute([$id])) {
        header("Location: ../produtos.php?sucesso=excluido");
    } else {
        header("Location: ../produtos.php?erro=exclusao");
    }
} catch (PDOException $e) {
    header("Location: ../produtos.php?erro=banco_dados");
}
exit;
?>