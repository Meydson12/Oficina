<?php
session_start();

/**
 * Sistema de Login - GenAuto Oficina
 * Processa autenticação com segurança e tratamento de erros
 */

// 🎯 Configurar sistema de mensagens
$mensagem = '';
$tipo_mensagem = '';

// 🔄 Verificar mensagens de logout
if (isset($_GET['logout'])) {
  switch ($_GET['logout']) {
    case 'success':
      $mensagem = "Logout realizado com sucesso! Volte sempre!";
      $tipo_mensagem = 'success';
      break;

    case 'error':
      $mensagem = isset($_GET['mensagem']) ?
        "Erro no logout: " . htmlspecialchars($_GET['mensagem']) :
        "Erro durante o logout.";
      $tipo_mensagem = 'error';
      break;

    case 'error_critico':
      $mensagem = "Erro crítico no sistema. Tente novamente.";
      $tipo_mensagem = 'error';
      break;
  }
}

// 🔐 Verificar se já está logado (evitar acesso duplo)
if (isset($_SESSION['usuario_id'])) {
  header("Location: dashboard.php");
  exit;
}

// 📦 Incluir configurações do banco
require_once 'config/database.php';

// 🚀 PROCESSAR LOGIN SE FORMULÁRIO FOI ENVIADO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['senha'])) {

  try {
    // 🔌 Conectar ao banco
    $database = new Database();
    $db = $database->getConnection();

    // 🧹 Sanitizar inputs
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $senha = trim($_POST['senha']);

    // ✅ Validações básicas
    if (empty($email) || empty($senha)) {
      throw new Exception("Preencha todos os campos!");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      throw new Exception("Email inválido!");
    }

    // 🔍 Buscar usuário no banco
    $query = "SELECT id, nome, email, senha, nivel, ativo FROM usuarios WHERE email = ? LIMIT 1";
    $stmt = $db->prepare($query);

    if (!$stmt->execute([$email])) {
      throw new Exception("Erro na consulta ao banco de dados");
    }

    // 👤 Verificar se usuário existe
    if ($stmt->rowCount() === 1) {
      $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

      // ✅ Verificar se usuário está ativo
      if (!$usuario['ativo']) {
        throw new Exception("Usuário desativado. Contate o administrador.");
      }

      // 🔐 VERIFICAR SENHA (versão temporária - vamos melhorar depois)
      if ($senha === '123456') { // Senha fixa para teste

        // 💾 Registrar dados na sessão
        $_SESSION['usuario_id'] = (int)$usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_nivel'] = $usuario['nivel'];
        $_SESSION['login_time'] = time();
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];

        // 📝 Registrar log de login (opcional)
        registrarLogLogin($db, $usuario['id']); //

        // 🔄 Redirecionar para dashboard
        header("Location: dashboard.php");
        exit;
      } else {
        throw new Exception("Senha incorreta! Use: 123456");
      }
    } else {
      throw new Exception("Usuário não encontrado! Use: admin@genauto.com");
    }
  } catch (Exception $e) {
    $mensagem = $e->getMessage();
    $tipo_mensagem = 'error';

    // 🕒 Delay de segurança para evitar brute force
    sleep(2);
  }
}

/**
 * Registrar log de login no banco (opcional)
 */
function registrarLogLogin($db, $usuario_id)
{
  try {
    $query = "INSERT INTO logs_sistema (usuario_id, acao, descricao, ip_address, user_agent) 
                  VALUES (?, 'login', 'Usuário fez login no sistema', ?, ?)";

    $stmt = $db->prepare($query);
    $stmt->execute([
      $usuario_id,
      $_SERVER['REMOTE_ADDR'] ?? 'unknown',
      $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ]);
  } catch (Exception $e) {
    // Não quebrar o login se o log falhar
    error_log("Erro ao registrar log de login: " . $e->getMessage());
  }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GenAuto - Login</title>
  <link rel="icon" type="image/x-icon" href="imagens/favicon.ico">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --primary: #2c3e50;
      --secondary: #3498db;
      --success: #27ae60;
      --danger: #e74c3c;
      --warning: #f39c12;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .login-container {
      width: 100%;
      max-width: 420px;
    }

    .login-box {
      background: white;
      padding: 2.5rem;
      border-radius: 15px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .login-header {
      text-align: center;
      margin-bottom: 2rem;
    }

    .login-header h2 {
      color: var(--primary);
      margin-bottom: 0.5rem;
      font-size: 1.8rem;
    }

    .login-header p {
      color: #7f8c8d;
      font-size: 1rem;
    }

    /* 🎨 Estilos para mensagens */
    .alert {
      padding: 12px 15px;
      margin-bottom: 1.5rem;
      border-radius: 8px;
      font-weight: 500;
      border-left: 4px solid transparent;
    }

    .alert-success {
      background: #d4edda;
      color: #155724;
      border-left-color: #28a745;
    }

    .alert-error {
      background: #f8d7da;
      color: #721c24;
      border-left-color: #dc3545;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: #2c3e50;
    }

    .form-group input {
      width: 100%;
      padding: 12px 15px;
      border: 2px solid #e9ecef;
      border-radius: 8px;
      font-size: 16px;
      transition: all 0.3s ease;
    }

    .form-group input:focus {
      outline: none;
      border-color: var(--secondary);
      box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .btn-login {
      width: 100%;
      padding: 14px;
      background: linear-gradient(135deg, var(--primary), #34495e);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .btn-login:active {
      transform: translateY(0);
    }

    .login-info {
      margin-top: 2rem;
      padding: 1.5rem;
      background: #f8f9fa;
      border-radius: 8px;
      border-left: 4px solid var(--warning);
    }

    .login-info h4 {
      color: #e67e22;
      margin-bottom: 0.5rem;
      font-size: 1rem;
    }

    .login-info p {
      margin: 0.3rem 0;
      font-size: 0.9rem;
      color: #7f8c8d;
    }

    .password-warning {
      font-size: 0.8rem;
      color: var(--danger);
      margin-top: 0.5rem;
      font-style: italic;
    }

    /* Responsividade */
    @media (max-width: 480px) {
      .login-box {
        padding: 2rem 1.5rem;
      }

      .login-header h2 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>

<body>
  <div class="login-container">
    <div class="login-box">
      <div class="login-header">
        <h2>🔧 GenAuto Oficina</h2>
        <p>Sistema de Gerenciamento</p>
      </div>

      <!-- 🎯 Sistema de Mensagens -->
      <?php if (!empty($mensagem)): ?>
        <div class="alert alert-<?php echo $tipo_mensagem; ?>">
          <?php
          switch ($tipo_mensagem) {
            case 'success':
              echo '✅ ';
              break;
            case 'error':
              echo '❌ ';
              break;
          }
          echo $mensagem;
          ?>
        </div>
      <?php endif; ?>

      <!-- 📝 Formulário de Login -->
      <form method="POST" autocomplete="on">
        <div class="form-group">
          <label for="email">📧 Email:</label>
          <input type="email" id="email" name="email"
            value="admin@genauto.com" required
            autocomplete="email" placeholder="seu@email.com">
        </div>

        <div class="form-group">
          <label for="senha">🔑 Senha:</label>
          <input type="password" id="senha" name="senha"
            value="123456" required
            autocomplete="current-password" placeholder="Sua senha">
          <div class="password-warning">
            ⚠️ Sistema em desenvolvimento - Use a senha padrão
          </div>
        </div>

        <button type="submit" class="btn-login">
          🚀 Entrar no Sistema
        </button>
      </form>

      <div class="login-info">
        <h4>💡 Dados para Teste</h4>
        <p><strong>Email:</strong> admin@genauto.com</p>
        <p><strong>Senha:</strong> 123456</p>
        <p><strong>Nível:</strong> Administrador</p>
      </div>
    </div>
  </div>

  <script>
    // 🎯 Foco automático no campo de email
    document.getElementById('email').focus();

    // 🔄 Prevenir reenvio do formulário ao recarregar
    if (window.history.replaceState) {
      window.history.replaceState(null, null, window.location.href);
    }

    // 🎨 Efeitos visuais nos inputs
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
      input.addEventListener('focus', function() {
        this.parentElement.style.transform = 'translateY(-2px)';
      });

      input.addEventListener('blur', function() {
        this.parentElement.style.transform = 'translateY(0)';
      });
    });
  </script>
</body>

</html>