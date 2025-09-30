<?php
session_start();
require_once '../config/database.php';
require_once '../config/funcoes.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: ../vendas.php?erro=id_nao_informado");
    exit;
}

$venda_id = $_GET['id'];

if (marcarVendaComoPaga($venda_id)) {
    header("Location: vendas-detalhes.php?id=$venda_id&sucesso=pago");
} else {
    header("Location: vendas-detalhes.php?id=$venda_id&erro=atualizacao");
}
exit;
?>