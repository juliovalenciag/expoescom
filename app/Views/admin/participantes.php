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
    <a href="/expoescom/admin"><i class="fa-solid fa-arrow-left"></i> Panel </a>
    <div class="header-right">
      <a href="https://www.ipn.mx" target="_blank"><img src="/expoescom/assets/images/IPN_Logo.png" alt="IPN" /></a>
      <a href="https://www.escom.ipn.mx" target="_blank"><img src="/expoescom/assets/images/Escom_Logo.png"
          alt="ESCOM" /></a>
      <a href="/expoescom/logout" class="btn-logout"><i class="fa-solid fa-sign-out-alt"></i> Cerrar sesión</a>
    </div>
  </header>
  <main class="dashboard-container">
    <h1 class="dashboard-title">Gestión de Participantes</h1>

    <div class="top-bar">
      <div class="actions-left">
        <a href="/expoescom/admin/participantes/add" class="btn-action">
          <i class="fa-solid fa-plus"></i> Nuevo participante
        </a>
      </div>
      <div class="actions-right">
        <input type="text" id="searchInput" class="search-input" placeholder="Buscar participantes…">
        <div class="columns-dropdown">
          <button id="columnsBtn" class="btn-secondary">
            Columnas <i class="fa-solid fa-caret-down"></i>
          </button>
          <div id="columnsMenu" class="columns-menu">
            <?php
            $cols = [
              'correo' => 'Correo',
              'telefono' => 'Teléfono',
              'genero' => 'Género',
              'semestre' => 'Semestre',
              'carrera' => 'Carrera',
              'equipo' => 'Equipo',
              'proyecto' => 'Proyecto',
              'academia' => 'Academia',
              'unidad' => 'Unidad',
              'es_ganador' => 'Ganador'
            ];
            foreach ($cols as $key => $label): ?>
              <label>
                <input type="checkbox" data-col="<?= $key ?>" checked>
                <?= $label ?>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="table-wrapper">
      <table class="data-table">
        <thead>
          <tr>
            <th data-col="boleta">Boleta</th>
            <th data-col="nombre">Nombre</th>
            <th data-col="apellidos">Apellidos</th>
            <?php foreach ($cols as $key => $label): ?>
              <th data-col="<?= $key ?>"><?= $label ?></th>
            <?php endforeach; ?>
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
              <td data-col="es_ganador" class="winner-cell">
                <?php if ($p['es_ganador']): ?>
                  <i class="fa-solid fa-trophy"></i>
                <?php endif; ?>
              </td>
              <td data-col="acciones" class="actions">
                <a href="/expoescom/admin/participantes/edit/<?= $p['boleta'] ?>" class="btn-circle btn-edit"
                  title="Editar">
                  <i class="fa-solid fa-pen"></i>
                </a>
                <button class="btn-circle btn-delete" title="Eliminar">
                  <i class="fa-solid fa-trash"></i>
                </button>
                <button class="btn-circle btn-toggle-winner" title="Toggle ganador">
                  <i class="fa-solid <?= $p['es_ganador'] ? 'fa-arrow-rotate-left' : 'fa-trophy' ?>"></i>
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>

  <script src="/expoescom/assets/js/admin-participantes.js"></script>
</body>

</html>