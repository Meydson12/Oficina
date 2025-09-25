<?php
// logout.php

/**
 * Sistema de Logout Seguro - GenAuto Oficina
 * @author Seu Nome
 * @version 2.0
 * @package Security
 */

// 🔐 Definir constante de segurança
define('SESSAO_ATIVA', true);

class LogoutSystem
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    public function realizarLogout()
    {
        try {
            // 🔐 Verificar se há sessão ativa
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // 🎯 Verificar se realmente há um usuário logado
            if (!$this->usuarioEstaLogado()) {
                $this->redirecionarComErro("Nenhum usuário logado para fazer logout");
            }

            // 📝 Registrar log do logout
            $this->registrarLog();

            // 🧹 Limpar sessão de forma segura
            $this->limparSessao();

            // 🔄 Redirecionar com sucesso
            $this->redirecionarComSucesso();
        } catch (Exception $e) {
            // 🚨 Em caso de erro, redirecionar com mensagem
            $this->redirecionarComErro("Erro durante o logout: " . $e->getMessage());
        }
    }

    private function usuarioEstaLogado()
    {
        return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
    }

    private function registrarLog()
    {
        // 📊 Registrar logout no banco de dados (opcional)
        if (isset($_SESSION['usuario_id'])) {
            $query = "INSERT INTO logs_sistema (usuario_id, acao, descricao, data_hora) 
                      VALUES (?, 'logout', 'Usuário realizou logout do sistema', NOW())";

            $stmt = $this->db->prepare($query);
            $stmt->execute([$_SESSION['usuario_id']]);
        }

        // 📝 Registrar também em arquivo de log
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'usuario_id' => $_SESSION['usuario_id'] ?? 'null',
            'usuario_nome' => $_SESSION['usuario_nome'] ?? 'Desconhecido',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ];

        $logMessage = json_encode($logData) . PHP_EOL;
        file_put_contents('logs/logout.log', $logMessage, FILE_APPEND | LOCK_EX);
    }

    private function limparSessao()
    {
        // 🎯 Backup do nome do usuário para mensagem
        $usuario_nome = $_SESSION['usuario_nome'] ?? 'Usuário';

        // 🧹 Limpar todas as variáveis de sessão
        $_SESSION = [];

        // 🍪 Expirar cookie de sessão
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // 💥 Destruir a sessão
        session_destroy();

        return $usuario_nome;
    }

    private function redirecionarComSucesso()
    {
        // 🔄 Redirecionar para login com mensagem de sucesso
        header("Location: index.php?logout=success&tipo=info");
        exit;
    }

    private function redirecionarComErro($mensagem)
    {
        // 🔄 Redirecionar para login com mensagem de erro
        header("Location: index.php?logout=error&mensagem=" . urlencode($mensagem));
        exit;
    }
}

// 🚀 EXECUÇÃO PRINCIPAL
try {
    // 📦 Incluir configurações do banco
    require_once 'config/database.php';

    // 🔌 Conectar ao banco de dados
    $database = new Database();
    $db = $database->getConnection();

    // 🎯 Criar instância do sistema de logout
    $logoutSystem = new LogoutSystem($db);

    // 🚪 Executar logout
    $logoutSystem->realizarLogout();
} catch (Exception $e) {
    // 🚨 Fallback em caso de erro crítico
    session_start();
    session_destroy();
    header("Location: index.php?logout=error_critico");
    exit;
}
