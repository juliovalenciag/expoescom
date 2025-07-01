<!-- app/Views/auth/login_admin.php -->
<?php
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Admin Login · ExpoESCOM</title>
  <link rel="icon" href="/expoescom/assets/images/favicon.ico" />
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css?family=Nunito:400,600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/expoescom/assets/css/admin-login.css" />
  <script defer src="/expoescom/assets/js/login_admin.js"></script>
</head>

<body class="admin-login">
  <header class="site-header">
    <a href="/expoescom/"><i class="fa-solid fa-arrow-left"></i> Inicio</a>
    <div class="header-right">
      <a href="https://www.ipn.mx" target="_blank">
        <img src="/expoescom/assets/images/IPN_Logo.png" alt="IPN" />
      </a>
      <a href="https://www.escom.ipn.mx" target="_blank">
        <img src="/expoescom/assets/images/Escom_Logo.png" alt="ESCOM" />
      </a>
    </div>
  </header>

  <main class="login-container">
    <div class="login-card">
      <h1 class="login-title">Administrador</h1>

      <?php if ($errors): ?>
        <div class="form-errors">
          <ul>
            <?php foreach ($errors as $e): ?>
              <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form id="loginForm" action="/expoescom/login/admin" method="POST" novalidate>
        <!-- Usuario -->
        <div class="field-group">
          <label for="usuario"><i class="fa-solid fa-user-shield"></i> Usuario</label>
          <input type="text" id="usuario" name="usuario" value="<?= htmlspecialchars($old['usuario'] ?? '') ?>"
            placeholder="Tu usuario" required />
          <small class="error" id="usuarioError"></small>
        </div>

        <!-- Contraseña -->
        <div class="field-group pwd-group">
          <label for="password"><i class="fa-solid fa-lock"></i> Contraseña</label>
          <input type="password" id="password" name="password" placeholder="Tu contraseña" required />
          <button type="button" class="eye-btn" data-target="password">
            <i class="fa-solid fa-eye"></i>
          </button>
          <small class="error" id="passwordError"></small>
        </div>

        <button type="submit" class="btn-submit">Entrar</button>

        <div class="login-footer">
          <a href="/expoescom/register">¿Eres participante? Regístrate</a> |
          <a href="/expoescom/login/participante">Login Participante</a>
        </div>
      </form>
    </div>
  </main>
</body>

</html>