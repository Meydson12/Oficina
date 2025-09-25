<?php
// config/funcoes.php

/**
 * Funções úteis para o sistema
 */

// Formatar data para exibição
function formatarData($data) {
    if(empty($data)) return '-';
    return date('d/m/Y H:i', strtotime($data));
}

// Formatar data apenas hora
function formatarHora($data) {
    if(empty($data)) return '-';
    return date('H:i', strtotime($data));
}

// Formatar moeda brasileira
function formatarMoeda($valor) {
    if(empty($valor)) return 'R$ 0,00';
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

// Calcular total de vendas do mês
function calcularTotalVendasMes($db) {
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
    } catch(Exception $e) {
        return 0;
    }
}

// Contar agendamentos de hoje
function contarAgendamentosHoje($db) {
    try {
        $query = "SELECT COUNT(*) as total 
                  FROM agendamentos 
                  WHERE DATE(data_agendamento) = CURDATE()";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $resultado['total'];
    } catch(Exception $e) {
        return 0;
    }
}

// Verificar peças com estoque baixo
function pecasEstoqueBaixo($db) {
    try {
        $query = "SELECT COUNT(*) as total 
                  FROM estoque 
                  WHERE quantidade <= estoque_minimo";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $resultado['total'];
    } catch(Exception $e) {
        return 0;
    }
}

// Buscar agendamentos do dia
function agendamentosDoDia($db) {
    try {
        $query = "SELECT cliente_nome, veiculo, servico, data_agendamento 
                  FROM agendamentos 
                  WHERE DATE(data_agendamento) = CURDATE() 
                  AND status != 'concluido'
                  ORDER BY data_agendamento ASC";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(Exception $e) {
        return [];
    }
}

// Sanitizar dados de entrada (segurança)
function sanitizar($dados) {
    if(is_array($dados)) {
        return array_map('sanitizar', $dados);
    }
    return htmlspecialchars(trim($dados), ENT_QUOTES, 'UTF-8');
}

// Verificar se é admin
function isAdmin() {
    return ($_SESSION['usuario_nivel'] ?? '') === 'admin';
}

// Redirecionar com mensagem
function redirect($url, $mensagem = '') {
    if(!empty($mensagem)) {
        $_SESSION['mensagem'] = $mensagem;
    }
    header("Location: $url");
    exit;
}
?>