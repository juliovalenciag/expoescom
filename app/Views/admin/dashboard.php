<?php
// app/Views/admin/dashboard.php
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panel Admin · ExpoEscom</title>
    <link rel="icon" href="/expoescom/assets/images/favicon.ico" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link
      href="https://fonts.googleapis.com/css?family=Nunito:400,600&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="/expoescom/assets/css/admin-dashboard.css" />
  </head>
  <body class="admin-dashboard">
    <header class="site-header">
      <a href="/expoescom/"><i class="fa-solid fa-arrow-left"></i> Inicio</a>
      <div class="header-right">
        <a href="https://www.ipn.mx" target="_blank"
          ><img src="/expoescom/assets/images/IPN_Logo.png" alt="IPN"
        /></a>
        <a href="https://www.escom.ipn.mx" target="_blank"
          ><img src="/expoescom/assets/images/Escom_Logo.png" alt="ESCOM"
        /></a>
        <a href="/expoescom/logout" class="btn-logout"
          ><i class="fa-solid fa-sign-out-alt"></i> Cerrar sesión</a
        >
      </div>
    </header>

    <main class="dashboard-container">
      <h1 class="dashboard-title">Panel de Administración</h1>

      <section class="overview">
        <div class="card">
          <div class="icon"><i class="fa-solid fa-user-graduate"></i></div>
          <div class="info">
            <p class="stat"><?= $totales['alumnos'] ?></p>
            <p class="label">Participantes</p>
          </div>
        </div>
        <div class="card">
          <div class="icon"><i class="fa-solid fa-users"></i></div>
          <div class="info">
            <p class="stat"><?= $totales['equipos'] ?></p>
            <p class="label">Equipos</p>
          </div>
        </div>
        <div class="card">
          <div class="icon"><i class="fa-solid fa-trophy"></i></div>
          <div class="info">
            <p class="stat"><?= $totales['ganadores'] ?></p>
            <p class="label">Ganadores</p>
          </div>
        </div>
      </section>

      <section class="actions">
        <a href="/expoescom/admin/participantes" class="btn-action">
          <i class="fa-solid fa-list"></i> Ver Participantes
        </a>
        <!-- <a href="/expoescom/admin/participantes" class="btn-action">
          <i class="fa-solid fa-door-open"></i> Asignar Salones
        </a> -->
      </section>
    </main>
  </body>
</html>
