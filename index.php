<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link rel="shortcut icon" href="imagens/engrenagem.png">
  <title>Oficina - Login</title>

  <!-- Font Awesome CSS (use a versão CSS - mais simples que a versão JS) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="" crossorigin="anonymous" />

  <!-- Seu CSS -->
  <link rel="stylesheet" href="css/login.css" />
</head>
<body>
  <div class="container">
    <form id="login_form" method="post">
      <div class="parte-cima">
        <h1>Login</h1>
        <!-- ícone: começa em lua -->
        <i id="mode_icon" class="fa-solid fa-moon" role="button" aria-label="Alternar tema"></i>
      </div>

      <div class="campos">
        <div class="input_box">
          <label for="nome">Nome</label>
          <div class="input_field">
            <input type="text" name="nome" id="login" />
          </div>
        </div>

        <div class="input_box">
          <label for="senha">Senha</label>
          <div class="input_field">
            <input type="password" name="senha" id="senha" />
          </div>
        </div>
      </div>

      <button type="submit" id="login_button" onclick="logar(); return false;">Login</button>
    </form>
  </div>

  <script>
    function logar() {
      // Placeholder - teste simples
      alert('Função logar() chamada (substitua por validação real).');
    }
  </script>

  <!-- Carrega o JS depois do DOM e depois da lib de ícones -->
  <script src="js/login.js"></script>
</body>
</html>
