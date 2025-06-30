<?php
// app/Views/admin/participantes.php
// El controlador debe haber cargado en sesión:
//   $_SESSION['academias'] = [
//     ['id'=>1,'nombre'=>'Ciencia de Datos','horarios'=>['Matutino']],
//     ...
//   ];
//   $_SESSION['unidadesPorAcademia'] = [
//     1 => [['id'=>10,'nombre'=>'Análisis de Series de Tiempo'],...],
//     ...
//   ];
$academias = $_SESSION['academias'] ?? [];
$unidadesPorAcademia = $_SESSION['unidadesPorAcademia'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Participantes · Panel Admin · ExpoEscom</title>
  
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css?family=Nunito:400,600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/expoescom/assets/css/admin-dashboard.css" />
  <link rel="stylesheet" href="/expoescom/assets/css/admin-participantes.css" />
  <script defer src="/expoescom/assets/js/admin-participantes.js"></script>
</head>

<body class="admin-participantes">

  <!-- Inyectamos datos en JS -->
  <script>
    window.academias = <?= json_encode($academias, JSON_UNESCAPED_UNICODE) ?>;
    window.unidadesPorAcademia = <?= json_encode($unidadesPorAcademia, JSON_UNESCAPED_UNICODE) ?>;
  </script>

  <header class="site-header">
    <div class="header-left">
      <a href="/expoescom/admin" class="btn-back">
        <i class="fa-solid fa-arrow-left"></i> Volver al Panel
      </a>
    </div>
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

    <h1>Participantes Registrados</h1>
    <a href="/expoescom/admin/participantes/nuevo" class="btn-action">➕ Añadir Participante</a>

    <input type="text" id="filtro" placeholder="Filtrar por nombre, boleta, equipo o proyecto" />

    <table id="tabla-participantes">
      <thead>
        <tr>
          <th>Boleta</th>
          <th>Nombre</th>
          <th>Proyecto</th>
          <th>Equipo</th>
          <th>Correo</th>
          <th>Carrera</th>
          <th>Semestre</th>
          <th>Teléfono</th>
          <th>Ganador</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($participantes as $p): ?>
          <tr>
            <td><?= $p['boleta'] ?></td>
            <td><?= "{$p['nombre']} {$p['apellido_paterno']} {$p['apellido_materno']}" ?></td>
            <td><?= $p['nombre_proyecto'] ?></td>
            <td><?= $p['nombre_equipo'] ?></td>
            <td><?= $p['correo'] ?></td>
            <td><?= $p['carrera'] ?></td>
            <td><?= $p['semestre'] ?></td>
            <td><?= $p['telefono'] ?></td>
            <td><?= $p['es_ganador'] ? '✔' : '—' ?></td>
            <td>
              <a href="/expoescom/admin/participantes/editar/<?= $p['boleta'] ?>">✏️</a>
              <a href="/expoescom/admin/participantes/eliminar/<?= $p['boleta'] ?>"
                onclick="return confirm('¿Eliminar participante?')">🗑️</a>
              <?php if (!$p['es_ganador']): ?>
                <a href="/expoescom/admin/participantes/ganador/<?= $p['boleta'] ?>">🏆</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <script src="/expoescom/assets/js/admin-participantes.js"></script>

  </main>



</body>

</html>