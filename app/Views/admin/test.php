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
  <link rel="icon" href="/expoescom/assets/images/favicon.ico" />

  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css?family=Nunito:400,600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/expoescom/assets/css/admin-dashboard.css" />
  <link rel="stylesheet" href="/expoescom/assets/css/admin-participantes.css" />
  <script defer src="/expoescom/assets/js/admin-participantes.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    <h1 class="dashboard-title">Participantes Registrados</h1>

    <div class="actions-bar">
      <a href="/expoescom/admin/participantes/nuevo" class="btn-action btn-new">
        <i class="fa-solid fa-plus"></i> Añadir Participante
      </a>

      <div class="filters">
        <input type="text" id="searchBox" placeholder="Buscar por boleta, nombre, equipo o proyecto..." />
      </div>
    </div>

    <div class="table-responsive">
      <table id="participantsTable">
        <thead>
          <tr>
            <th>Boleta</th>
            <th>Nombre Completo</th>
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
              <td><?= htmlspecialchars($p['boleta']) ?></td>
              <td><?= htmlspecialchars("{$p['nombre']} {$p['apellido_paterno']} {$p['apellido_materno']}") ?></td>
              <td><?= htmlspecialchars($p['nombre_proyecto']) ?></td>
              <td><?= htmlspecialchars($p['nombre_equipo']) ?></td>
              <td><?= htmlspecialchars($p['correo']) ?></td>
              <td><?= htmlspecialchars($p['carrera']) ?></td>
              <td><?= htmlspecialchars($p['semestre']) ?></td>
              <td><?= htmlspecialchars($p['telefono']) ?></td>
              <td><?= $p['es_ganador'] ? '🏆' : '—' ?></td>
              <td class="acciones">
                <button class="btn-edit" data-boleta="<?= $p['boleta'] ?>" title="Editar">
                  ✏️
                </button>
                <button class="btn-delete" data-boleta="<?= $p['boleta'] ?>" title="Eliminar">
                  🗑️
                </button>
                <button class="btn-toggle-winner <?= $p['es_ganador'] ? 'active' : '' ?>"
                  data-boleta="<?= $p['boleta'] ?>" data-ganador="<?= $p['es_ganador'] ? '1' : '0' ?>"
                  title="<?= $p['es_ganador'] ? 'Quitar como ganador' : 'Marcar como ganador' ?>">
                  <?= $p['es_ganador'] ? '❌' : '🏆' ?>
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>

  <!-- Modal Crear Participante -->
  <div class="modal-overlay" id="modalCrear">
    <div class="modal-dialog">
      <div class="modal-header">
        <h2>Nuevo Participante</h2>
        <button class="modal-close" onclick="cerrarModal('modalCrear')">&times;</button>
      </div>
      <form id="formCrear" class="modal-form">
        <div class="form-grid">
          <div class="field-group">
            <label>Boleta</label>
            <input type="text" name="boleta" required />
          </div>
          <div class="field-group">
            <label>Nombre</label>
            <input type="text" name="nombre" required />
          </div>
          <div class="field-group">
            <label>Apellido Paterno</label>
            <input type="text" name="apellido_paterno" required />
          </div>
          <div class="field-group">
            <label>Apellido Materno</label>
            <input type="text" name="apellido_materno" required />
          </div>
          <div class="field-group">
            <label>CURP</label>
            <input type="text" name="curp" required />
          </div>
          <div class="field-group">
            <label>Correo</label>
            <input type="email" name="correo" required />
          </div>
          <div class="field-group">
            <label>Género</label>
            <select name="genero" required>
              <option value="">Selecciona</option>
              <option value="F">Femenino</option>
              <option value="M">Masculino</option>
              <option value="O">Otro</option>
            </select>
          </div>
          <div class="field-group">
            <label>Contraseña (temporal)</label>
            <input type="text" name="password" required placeholder="Al menos 6 caracteres" />
          </div>
          <div class="field-group">
            <label>Teléfono</label>
            <input type="text" name="telefono" maxlength="10" />
          </div>
          <div class="field-group">
            <label>Semestre</label>
            <input type="number" name="semestre" min="1" max="8" required />
          </div>
          <div class="field-group">
            <label>Carrera</label>
            <select name="carrera" required>
              <option value="ISC">ISC</option>
              <option value="LCD">LCD</option>
              <option value="IIA">IIA</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" onclick="cerrarModal('modalCrear')">Cancelar</button>
          <button type="submit" class="btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Editar Participante -->
  <div class="modal-overlay" id="modalEditar">
    <div class="modal-dialog">
      <div class="modal-header">
        <h2>Editar Participante</h2>
        <button class="modal-close" onclick="cerrarModal('modalEditar')">&times;</button>
      </div>
      <form id="formEditar" class="modal-form">
        <input type="hidden" name="boleta_original" />
        <div class="form-grid">
          <div class="field-group">
            <label>Boleta</label>
            <input type="text" name="boleta" required />
          </div>
          <div class="field-group">
            <label>Nombre</label>
            <input type="text" name="nombre" required />
          </div>
          <div class="field-group">
            <label>Apellido Paterno</label>
            <input type="text" name="apellido_paterno" required />
          </div>
          <div class="field-group">
            <label>Apellido Materno</label>
            <input type="text" name="apellido_materno" required />
          </div>




          <div class="field-group">
            <label>Correo</label>
            <input type="email" name="correo" required />
          </div>
          <div class="field-group">
            <label>Contraseña (solo si deseas cambiarla)</label>
            <input type="text" name="password" placeholder="Déjalo vacío para no cambiar" />
          </div>
          <div class="field-group">
            <label>Teléfono</label>
            <input type="text" name="telefono" maxlength="10" />
          </div>
          <div class="field-group">
            <label>Semestre</label>
            <input type="number" name="semestre" min="1" max="8" required />
          </div>
          <div class="field-group">
            <label>Carrera</label>
            <select name="carrera" required>
              <option value="ISC">ISC</option>
              <option value="LCD">LCD</option>
              <option value="IIA">IIA</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" onclick="cerrarModal('modalEditar')">Cancelar</button>
          <button type="submit" class="btn-primary">Actualizar</button>
        </div>
      </form>
    </div>
  </div>




</body>

</html>