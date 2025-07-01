<?php
// app/Views/admin/participantes.php
// recibe $participantes (array de arrays)
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Participantes · Admin ExpoESCOM</title>
  <link rel="icon" href="/expoescom/assets/images/favicon.ico">
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css?family=Nunito:400,600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/expoescom/assets/css/admin-participantes.css">
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

    <div class="top-controls">
      <a href="/expoescom/admin/participantes/add" class="btn-action">
        <i class="fa-solid fa-plus"></i> Nuevo participante
      </a>

      <div class="column-selector">
        <!-- siempre obligatorias: boleta, nombre, apellidos, acciones -->
        <label><input type="checkbox" data-col="correo" checked> Correo</label>
        <label><input type="checkbox" data-col="telefono" checked> Teléfono</label>
        <label><input type="checkbox" data-col="genero" checked> Género</label>
        <label><input type="checkbox" data-col="semestre" checked> Semestre</label>
        <label><input type="checkbox" data-col="carrera" checked> Carrera</label>
        <label><input type="checkbox" data-col="equipo" checked> Equipo</label>
        <label><input type="checkbox" data-col="proyecto" checked> Proyecto</label>
        <label><input type="checkbox" data-col="academia" checked> Academia</label>
        <label><input type="checkbox" data-col="unidad" checked> Unidad</label>
        <label><input type="checkbox" data-col="es_ganador" checked> Ganador</label>
      </div>
    </div>

    <table class="data-table">
      <thead>
        <tr>
          <th data-col="boleta">Boleta</th>
          <th data-col="nombre">Nombre</th>
          <th data-col="apellidos">Apellidos</th>
          <th data-col="correo">Correo</th>
          <th data-col="telefono">Teléfono</th>
          <th data-col="genero">Género</th>
          <th data-col="semestre">Semestre</th>
          <th data-col="carrera">Carrera</th>
          <th data-col="equipo">Equipo</th>
          <th data-col="proyecto">Proyecto</th>
          <th data-col="academia">Academia</th>
          <th data-col="unidad">Unidad</th>
          <th data-col="es_ganador">Ganador</th>
          <th data-col="acciones">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($participantes as $p): ?>
          <tr data-boleta="<?= $p['boleta'] ?>">
            <td data-col="boleta"><?= htmlspecialchars($p['boleta']) ?></td>
            <td data-col="nombre"><?= htmlspecialchars($p['nombre']) ?></td>
            <td data-col="apellidos">
              <?= htmlspecialchars("{$p['apellido_paterno']} {$p['apellido_materno']}") ?>
            </td>
            <td data-col="correo"><?= htmlspecialchars($p['correo']) ?></td>
            <td data-col="telefono"><?= htmlspecialchars($p['telefono']) ?></td>
            <td data-col="genero"><?= htmlspecialchars($p['genero']) ?></td>
            <td data-col="semestre"><?= htmlspecialchars($p['semestre']) ?></td>
            <td data-col="carrera"><?= htmlspecialchars($p['carrera']) ?></td>
            <td data-col="equipo"><?= htmlspecialchars($p['nombre_equipo']) ?></td>
            <td data-col="proyecto"><?= htmlspecialchars($p['nombre_proyecto']) ?></td>
            <td data-col="academia"><?= htmlspecialchars($p['academia']) ?></td>
            <td data-col="unidad"><?= htmlspecialchars($p['unidad']) ?></td>
            <td data-col="es_ganador"><?= $p['es_ganador'] ? 'Sí' : 'No' ?></td>
            <td data-col="acciones" class="actions">
              <a href="/expoescom/admin/participantes/edit/<?= $p['boleta'] ?>" class="btn-edit" title="Editar">
                <i class="fa-solid fa-pen"></i>
              </a>
              <button class="btn-delete" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
              <button class="btn-toggle-winner" title="Alternar ganador">
                <i class="fa-solid <?= $p['es_ganador'] ? 'fa-trophy' : 'fa-medal' ?>"></i>
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </main>

  <script src="/expoescom/assets/js/admin-participantes-table.js"></script>
</body>

</html>