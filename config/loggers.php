<?php
class Logger {
    private $logFile;
    
    public function __construct($logFile = 'logs/sistema.log') {
        $this->logFile = $logFile;
        
        // Criar pasta de logs se não existir
        if (!is_dir('logs')) {
            mkdir('logs', 0755, true);
        }
    }
    
    public function log($acao, $detalhes = []) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'acao' => $acao,
            'usuario' => $_SESSION['usuario_id'] ?? 'null',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'detalhes' => $detalhes
        ];
        
        file_put_contents(
            $this->logFile, 
            json_encode($logEntry) . PHP_EOL, 
            FILE_APPEND | LOCK_EX
        );
    }
}
?>