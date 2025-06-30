<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Mi Panel - ExpoESCOM</title>
  <link rel="stylesheet" href="/expoescom/assets/css/participante-dashboard.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:400,600&display=swap">
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="participante-dashboard">

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
    <h1 class="dashboard-title">Bienvenido, <?= htmlspecialchars($info['nombre']) ?></h1>

    <section class="details-grid">
      <div class="detail-card">
        <h4>Datos Generales</h4>
        <p><strong>Boleta:</strong> <?= htmlspecialchars($info['boleta']) ?></p>
        <p><strong>Nombre:</strong> <?= htmlspecialchars($info['nombre']) ?>
          <?= htmlspecialchars($info['apellido_paterno']) ?> <?= htmlspecialchars($info['apellido_materno']) ?></p>
      </div>

      <div class="detail-card">
        <h4>Concurso</h4>
        <p><strong>Ganador:</strong> <?= $info['es_ganador'] ? 'Sí 🏆' : 'No' ?></p>
        <?php if (!empty($info['salon_id'])): ?>
          <p><strong>Salón:</strong> <?= $info['salon_id'] ?></p>
          <p><strong>Bloque:</strong> <?= $info['bloque'] ?> (<?= $info['hora_inicio'] ?> - <?= $info['hora_fin'] ?>)</p>
        <?php else: ?>
          <p class="text-warning">Sin asignación todavía</p>
        <?php endif; ?>
      </div>
    </section>

    <div class="actions">
      <?php if (!empty($info['salon_id'])): ?>
        <a class="btn-action" href="/expoescom/pdf/acuse/<?= $info['boleta'] ?>">
          <i class="fa-solid fa-file-pdf"></i> Descargar Acuse
        </a>
        <?php if ($info['es_ganador']): ?>
          <a class="btn-action" href="/expoescom/pdf/diploma/<?= $info['boleta'] ?>">
            <i class="fa-solid fa-medal"></i> Descargar Diploma
          </a>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </main>

</body>

</html>