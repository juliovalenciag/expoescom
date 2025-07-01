<?php
// app/Views/admin/participantes.php
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Participantes · Admin ExpoESCOM</title>
  <link rel="icon" href="/expoescom/assets/images/favicon.ico" />
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css?family=Nunito:400,600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/expoescom/assets/css/admin-dashboard.css" />
  <link rel="stylesheet" href="/expoescom/assets/css/admin-participantes.css" />
</head>

<body class="admin-dashboard">
  <header class="site-header">
    <a href="/expoescom/admin"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    <div class="header-right">
      <a href="/expoescom/logout" class="btn-logout">
        <i class="fa-solid fa-sign-out-alt"></i> Salir
      </a>
    </div>
  </header>

  <main class="dashboard-container">
    <h1 class="dashboard-title">Gestión de Participantes</h1>
    <button id="nuevoPartBtn" class="btn-action">
      <i class="fa-solid fa-plus"></i> Nuevo participante
    </button>

    <table class="data-table">
      <thead>
        <tr>
          <th>Boleta</th>
          <th>Nombre</th>
          <th>Apellidos</th>
          <th>Correo</th>
          <th>Teléfono</th>
          <th>Género</th>
          <th>Semestre</th>
          <th>Carrera</th>
          <th>Equipo</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($participantes as $p): ?>
          <tr data-boleta="<?= $p['boleta'] ?>">
            <td><?= $p['boleta'] ?></td>
            <td><?= htmlspecialchars($p['nombre']) ?></td>
            <td><?= htmlspecialchars($p['apellido_paterno'] . ' ' . $p['apellido_materno']) ?></td>
            <td><?= $p['correo'] ?></td>
            <td><?= $p['telefono'] ?></td>
            <td><?= $p['genero'] ?></td>
            <td><?= $p['semestre'] ?></td>
            <td><?= $p['carrera'] ?></td>
            <td><?= htmlspecialchars($p['nombre_equipo'] ?? '') ?></td>
            <td class="actions">
              <button class="btn-edit" title="Editar"><i class="fa-solid fa-pen"></i></button>
              <button class="btn-delete" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
              <button class="btn-toggle" title="Toggle ganador">
                <i class="fa-solid <?= $p['es_ganador'] ? 'fa-trophy' : 'fa-medal' ?>"></i>
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </main>

  <?php include __DIR__ . '/participantes_edit.php'; ?>

  <script defer src="/expoescom/assets/js/admin-participantes.js"></script>
</body>

</html>