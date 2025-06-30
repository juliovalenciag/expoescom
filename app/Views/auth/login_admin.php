<?php
// app/Views/auth/login_admin.php
// Recibe $errors (array) y $old (array)
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Login Administrador · ExpoEscom</title>
  <link rel="icon" href="/expoescom/assets/images/favicon.ico"/>
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css?family=Nunito:400,600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/expoescom/assets/css/login.css"/>
  <script defer src="/expoescom/assets/js/login.js"></script>
</head>
<body class="login-page admin-login">
  <header class="site-header">
    <a href="/expoescom/"><i class="fa-solid fa-arrow-left"></i> Inicio</a>
    <div>
      <a href="https://www.ipn.mx" target="_blank">
        <img src="/expoescom/assets/images/IPN_Logo.png" alt="IPN"/>
      </a>
      <a href="https://www.escom.ipn.mx" target="_blank">
        <img src="/expoescom/assets/images/Escom_Logo.png" alt="ESCOM"/>
      </a>
    </div>
  </header>

  <main class="wrapper">
    <div class="login-card">
      <h1 class="login-title">Login Administrador</h1>

      <?php if (!empty($errors)): ?>
        <div class="form-errors">
          <ul>
            <?php foreach ($errors as $e): ?>
              <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form id="loginForm" action="/expoescom/login/admin" method="POST" novalidate>
        <div class="field-group<?= !empty($errors['usuario'] ) ? ' invalid' : '' ?>">
          <label for="usuario"><i class="fa-solid fa-user-shield"></i> Usuario</label>
          <input
            type="text"
            id="usuario"
            name="usuario"
            value="<?= htmlspecialchars($old['usuario'] ?? '') ?>"
            placeholder="Tu usuario"
            required
          />
          <small id="usuarioError" class="error"></small>
        </div>

        

        <div class="field-group pwd-group <?= isset($errors['password']) ? 'invalid' : '' ?>">
          <label for="password">
            <i class="fa-solid fa-lock"></i> Contraseña
          </label>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="Tu contraseña"
            required
          />
          <button type="button" class="eye-btn" data-target="password">
            <i class="fa-solid fa-eye"></i>
          </button>
          <small id="passwordError" class="error">
            <?= htmlspecialchars($errors['password'] ?? '') ?>
          </small>
        </div>


        <div class="buttons">
          <button type="submit" class="btn-submit">Entrar</button>
        </div>

        <div class="login-footer">
          <a href="/expoescom/register">¿Eres participante? Regístrate</a> |
          <a href="/expoescom/login/participante">Login Participante</a>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
