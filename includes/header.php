<?php
// includes/header.php - Versão simples
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/sistema_oficina/imagens/engrenagem.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- CSS com caminhos absolutos da raiz -->
    <link rel="stylesheet" href="/sistema_oficina/css/base.css">
    <link rel="stylesheet" href="/sistema_oficina/css/layout.css">
    <link rel="stylesheet" href="/sistema_oficina/css/agendamentos.css">
    <link rel="stylesheet" href="/sistema_oficina/css/responsivo.css">
    
    <title><?php echo $pagina_titulo ?? 'GenAuto'; ?></title>
</head>
<body>