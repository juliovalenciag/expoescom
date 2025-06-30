<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="/expoescom/assets/images/favicon.ico" />
  <title>Participante · ExpoESCOM</title>
  <link rel="icon" href="/expoescom/assets/images/favicon.ico" />
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css?family=Nunito:400,600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/expoescom/assets/css/participante-dashboard.css" />
</head>
<body class="participante-dashboard">
  <header class="site-header">
    <a href="/expoescom/"><i class="fa-solid fa-arrow-left"></i> Inicio</a>
    <div class="header-right">
      <a href="https://www.ipn.mx" target="_blank">
        <img src="/expoescom/assets/images/IPN_Logo.png" alt="IPN" />
      </a>
      <a href="https://www.escom.ipn.mx" target="_blank">
        <img src="/expoescom/assets/images/Escom_Logo.png" alt="ESCOM" />
      </a>
      <a href="/expoescom/logout" class="btn-logout">
        <i class="fa-solid fa-sign-out-alt"></i> Cerrar sesión
      </a>
    </div>
  </header>

  <main class="dashboard-container">
    <h1 class="dashboard-title">Bienvenido, <?= htmlspecialchars($info['nombre']) ?></h1>

    <section class="details-grid">
      <div class="detail-card">
        <h4>Datos Personales</h4>
        <p><strong>Boleta:</strong> <?= htmlspecialchars($info['boleta']) ?></p>
        <p><strong>Correo:</strong> <?= htmlspecialchars($info['correo']) ?></p>
        <p><strong>Teléfono:</strong> <?= htmlspecialchars($info['telefono']) ?></p>
        
        <p><strong>Género:</strong> <?= htmlspecialchars($info['genero']) ?></p>
      </div>

      <div class="detail-card">
        <h4>Información Académica</h4>
        <p><strong>Carrera:</strong> <?= htmlspecialchars($info['carrera']) ?></p>
        <p><strong>Semestre:</strong> <?= htmlspecialchars($info['semestre']) ?></p>
      </div>

      <div class="detail-card">
        <h4>Información del Concurso</h4>
        <p><strong>Equipo:</strong> <?= htmlspecialchars($info['nombre_equipo']) ?></p>
        <p><strong>Proyecto:</strong> <?= htmlspecialchars($info['nombre_proyecto']) ?></p>
        <p><strong>Horario:</strong> <?= date('H:i', strtotime($info['hora_inicio'])) ?> – <?= date('H:i', strtotime($info['hora_fin'])) ?></p>

        <p><strong>Ganador:</strong> <?= $info['es_ganador'] ? 'Sí' : 'No' ?></p>
      </div>

      <div class="detail-card">
        <h4>Asignación</h4>
        <?php if (!empty($info['salon_id'])): ?>
          <p><strong>Salón:</strong> <?= htmlspecialchars($info['salon_id']) ?></p>
          <p><strong>Bloque:</strong> <?= htmlspecialchars($info['bloque']) ?></p>
          <p><strong>Horario:</strong> <?= date('H:i', strtotime($info['hora_inicio'])) ?> – <?= date('H:i', strtotime($info['hora_fin'])) ?></p>

        <?php else: ?>
          <p class="text-warning">Aún no se te ha asignado un salón.</p>
        <?php endif; ?>
      </div>
    </section>

    <section class="actions">
      <?php if (!empty($info['salon_id'])): ?>
        <a href="/expoescom/pdf/acuse/<?= $info['boleta'] ?>" class="btn-action">
          <i class="fa-solid fa-file-pdf"></i> Descargar Acuse
        </a>
        <?php if ($info['es_ganador']): ?>
          <a href="/expoescom/pdf/diploma/<?= $info['boleta'] ?>" class="btn-action">
            <i class="fa-solid fa-award"></i> Descargar Diploma
          </a>
        <?php endif; ?>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>
