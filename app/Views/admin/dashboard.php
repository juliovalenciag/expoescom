<?php
// $totales = [ 'alumnos'=>…, 'equipos'=>…, 'ganadores'=>…, 'salones'=>…, 'bloques'=>… ]
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Admin · ExpoESCOM</title>
  <link rel="icon" href="/expoescom/assets/images/favicon.ico" />
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css?family=Nunito:400,600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/expoescom/assets/css/admin-dashboard.css" />
</head>

<body class="admin-dashboard">
  <header class="site-header">
    <a href="/expoescom/"><i class="fa-solid fa-arrow-left"></i> Inicio</a>
    <div class="header-right">
      <a href="https://www.ipn.mx" target="_blank"><img src="/expoescom/assets/images/IPN_Logo.png" alt="IPN" /></a>
      <a href="https://www.escom.ipn.mx" target="_blank"><img src="/expoescom/assets/images/Escom_Logo.png"
          alt="ESCOM" /></a>
      <a href="/expoescom/logout" class="btn-logout"><i class="fa-solid fa-sign-out-alt"></i> Cerrar sesión</a>
    </div>
  </header>

  <main class="dashboard-container">
    <h1 class="dashboard-title">Panel Administración</h1>

   

    <!-- RESUMEN ESTADÍSTICO -->
    <section class="overview">
      <?php foreach ([
        ['icon' => 'fa-user-graduate', 'stat' => $totales['alumnos'], 'label' => 'Participantes'],
        ['icon' => 'fa-users', 'stat' => $totales['equipos'], 'label' => 'Equipos'],
        ['icon' => 'fa-trophy', 'stat' => $totales['ganadores'], 'label' => 'Ganadores'],
        ['icon' => 'fa-door-closed', 'stat' => $totales['salones'], 'label' => 'Salones'],
        ['icon' => 'fa-clock', 'stat' => $totales['bloques'], 'label' => 'Bloques']
      ] as $c): ?>
        <div class="card">
          <div class="icon"><i class="fa-solid <?= $c['icon'] ?>"></i></div>
          <div class="info">
            <p class="stat"><?= $c['stat'] ?></p>
            <p class="label"><?= $c['label'] ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </section>

    <!-- ATAJOS DE ACCIÓN -->
    <section class="shortcuts">
      <a href="/expoescom/admin/participantes" class="shortcut">
        <i class="fa-solid fa-list"></i><span>Participantes</span>
      </a>
      <a href="/expoescom/admin/salones" class="shortcut">
        <i class="fa-solid fa-door-open"></i><span>Gestionar Salones</span>
      </a>
      <a href="/expoescom/admin/bloques" class="shortcut">
        <i class="fa-solid fa-clock"></i><span>Gestionar Bloques</span>
      </a>
      <a href="/expoescom/admin/catalogos" class="shortcut">
        <i class="fa-solid fa-book"></i><span>Catálogos</span>
      </a>
    </section>
  </main>

  <script>
    // Buscador global (filtra cards de resumen)
    document.getElementById('globalSearch').addEventListener('input', function () {
      const q = this.value.toLowerCase()
      document.querySelectorAll('.overview .card').forEach(card => {
        const label = card.querySelector('.label').textContent.toLowerCase()
        card.style.display = label.includes(q) ? 'flex' : 'none'
      })
    })
  </script>
</body>

</html>