<?php
// app/Views/auth/login_participante.php
// Recibe $errors (array) y $old (array) desde AuthController
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Participante · ExpoEscom</title>
  <link rel="icon" href="/expoescom/assets/images/favicon.ico" />
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css?family=Nunito:400,600&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="/expoescom/assets/css/login.css" />
  <script defer src="/expoescom/assets/js/login.js"></script>
</head>

<body class="login-page participante-login">
  <header class="site-header">
    <a href="/expoescom/"><i class="fa-solid fa-arrow-left"></i> Inicio</a>
    <div>
      <a href="https://www.ipn.mx" target="_blank">
        <img src="/expoescom/assets/images/IPN_Logo.png" alt="IPN" />
      </a>
      <a href="https://www.escom.ipn.mx" target="_blank">
        <img src="/expoescom/assets/images/Escom_Logo.png" alt="ESCOM" />
      </a>
    </div>
  </header>

  <main>
    <div class="login-card">
      <h1 class="login-title">Login Participante · ExpoEscom</h1>

      <?php if (!empty($errors)): ?>
        <div class="form-errors">
          <ul>
            <?php foreach ($errors as $e): ?>
              <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form id="loginForm" action="/expoescom/login/participante" method="POST" novalidate>
        <div class="field-group <?= isset($errors['identifier']) ? 'invalid' : '' ?>">
          <label for="identifier">
            <i class="fa-solid fa-id-badge"></i> Boleta o Correo
          </label>
          <input
            type="text"
            id="identifier"
            name="identifier"
            placeholder="PE12345678 o usuario@alumno.ipn.mx"
            value="<?= htmlspecialchars($old['identifier'] ?? '') ?>"
            required
          />
          <small id="identifierError" class="error">
            <?= htmlspecialchars($errors['identifier'] ?? '') ?>
          </small>
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
          <button type="submit" class="btn-submit">Iniciar sesión</button>
        </div>
      </form>

      <div class="login-footer">
        <a href="/expoescom/register">¿No tienes cuenta? Regístrate</a> |
        <a href="/expoescom/login/admin">Soy Administrador</a>
      </div>
    </div>
  </main>
</body>
</html>
