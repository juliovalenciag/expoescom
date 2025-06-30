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
  k
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
    <h1 class="dashboard-title">Participantes Registrados</h1>

    <div class="actions-bar">
      <button id="btnNew" class="btn-action btn-new">
        <i class="fa-solid fa-plus"></i> Nuevo Participante
      </button>
      <div class="filters">
        <input type="text" id="searchBox" placeholder="🔍 Buscar…" />
        <label class="toggle-winners">
          <input type="checkbox" id="winnerFilter" /> Sólo ganadores
        </label>
      </div>
    </div>

    <div class="table-responsive">
      <table id="participantsTable">
        <thead>
          <tr>
            <th>Boleta</th>
            <th>Nombre</th>
            <th>Carrera</th>
            <th>Correo</th>
            <th>Equipo</th>
            <th>Proyecto</th>
            <th>Salón</th>
            <th>Bloque</th>
            <th>Ganador</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="10" class="loading">—</td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>

  <!-- —— MODAL CREAR —— -->
  <div id="modalCreate" class="modal-overlay">
    <div class="modal-dialog">
      <header class="modal-header">
        <h2>Nuevo Participante</h2>
        <button class="modal-close" data-close="modalCreate">&times;</button>
      </header>
      <form id="formCreate" class="modal-form" novalidate>

        <fieldset>
          <legend>Datos Personales</legend>
          <div class="form-grid">
            <!-- Boleta -->
            <div class="field-group">
              <label for="createBoleta">Boleta</label>
              <input id="createBoleta" name="boleta" type="text" required pattern="^(?:\d{10}|(?:PE|PP)\d{8})$"
                title="10 dígitos o PE/PP + 8 dígitos" />
            </div>
            <!-- Nombre(s) -->
            <div class="field-group">
              <label for="createNombre">Nombre(s)</label>
              <input id="createNombre" name="nombre" type="text" required pattern="^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]{1,40}$"
                title="Solo letras y espacios" />
            </div>
            <!-- Apellidos -->
            <div class="field-group">
              <label for="createApellidoP">Apellido Paterno</label>
              <input id="createApellidoP" name="apellido_paterno" type="text" required
                pattern="^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]{1,40}$" />
            </div>
            <div class="field-group">
              <label for="createApellidoM">Apellido Materno</label>
              <input id="createApellidoM" name="apellido_materno" type="text" required
                pattern="^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]{1,40}$" />
            </div>
            <!-- Género -->
            <div class="field-group">
              <label for="createGenero">Género</label>
              <select id="createGenero" name="genero" required>
                <option value="">--</option>
                <option value="Mujer">Mujer</option>
                <option value="Hombre">Hombre</option>
                <option value="Otro">Otro</option>
              </select>
            </div>
            <!-- Teléfono / CURP -->
            <div class="field-group">
              <label for="createTelefono">Teléfono</label>
              <input id="createTelefono" name="telefono" type="tel" required maxlength="10" pattern="^\d{10}$"
                title="10 dígitos" />
            </div>
            <div class="field-group">
              <label for="createCURP">CURP</label>
              <input id="createCURP" name="curp" type="text" required maxlength="18"
                pattern="^[A-ZÑ][AEIOUÑ][A-ZÑ]{2}\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[HM](AS|BC|BS|CC|CS|CH|CL|CM|DF|DG|GT|GR|HG|JC|MC|MN|MS|NT|NL|OC|PL|QT|QR|SP|SL|SR|TC|TS|TL|VZ|YN|ZS)[B-DF-HJ-NP-TV-ZÑ]{3}[A-Z\d]\d$"
                title="Formato CURP" />
            </div>
            <!-- Semestre / Carrera -->
            <div class="field-group">
              <label for="createSemestre">Semestre</label>
              <select id="createSemestre" name="semestre" required>
                <option value="">--</option>
                <?php for ($i = 1; $i <= 8; $i++): ?>
                  <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
              </select>
            </div>
            <div class="field-group">
              <label for="createCarrera">Carrera</label>
              <select id="createCarrera" name="carrera" required>
                <option value="">--</option>
                <option value="ISC">ISC</option>
                <option value="IIA">IIA</option>
                <option value="LCD">LCD</option>
              </select>
            </div>
          </div>
        </fieldset>

        <fieldset>
          <legend>Datos de Cuenta</legend>
          <div class="form-grid">
            <div class="field-group">
              <label for="createCorreo">Correo</label>
              <input id="createCorreo" name="correo" type="email" required pattern="^[\w.+-]+@alumno\.ipn\.mx$"
                title="Termina en @alumno.ipn.mx" />
            </div>
            <div class="field-group">
              <label for="createPassword">Contraseña</label>
              <input id="createPassword" name="password" type="password" required minlength="6"
                title="Mín. 6 car., 1 may., 1 dig., 1 espec." />
            </div>
            <div class="field-group">
              <label for="createPassword2">Confirmar contraseña</label>
              <input id="createPassword2" name="password2" type="password" required />
            </div>
          </div>
        </fieldset>

        <fieldset>
          <legend>Datos de Concurso</legend>
          <div class="form-grid">
            <!-- <div class="field-group">
              <label for="createHorario">Horario preferido</label>
              <select id="createHorario" name="horario_preferencia" required>
                <option value="">--</option>
                <option value="Matutino">Matutino</option>
                <option value="Vespertino">Vespertino</option>
              </select>
            </div>
            <div class="field-group">
              <label for="createAcademia">Academia</label>
              <select id="createAcademia" name="academia_id" disabled required>
                <option value="">--</option>
              </select>
            </div>
            <div class="field-group">
              <label for="createUnidad">Unidad</label>
              <select id="createUnidad" name="unidad_id" disabled required>
                <option value="">--</option>
              </select>
            </div> -->
            <div class="field-group">
              <label for="createProyecto">Proyecto</label>
              <input id="createProyecto" name="nombre_proyecto" type="text" required minlength="3" />
            </div>
            <div class="field-group">
              <label for="createEquipo">Equipo</label>
              <input id="createEquipo" name="nombre_equipo" type="text" required minlength="3" />
            </div>
          </div>
        </fieldset>

        <footer class="modal-footer">
          <button type="button" class="btn-secondary modal-close" data-close="modalCreate">Cancelar</button>
          <button type="submit" class="btn-primary">Crear</button>
        </footer>
      </form>
    </div>
  </div>

  <!-- —— MODAL EDITAR —— (igual, quita password y boleta readonly) -->
  <div id="modalEdit" class="modal-overlay">
    <div class="modal-dialog">
      <header class="modal-header">
        <h2>Editar Participante</h2>
        <button class="modal-close" data-close="modalEdit">&times;</button>
      </header>
      <form id="formEdit" class="modal-form" novalidate>
        <!-- repite exactamente los mismos fieldsets -->
        <!-- Boleta readonly -->
        <div class="field-group">
          <label>Boleta</label>
          <input name="boleta" readonly />
        </div>
        <!-- resto igual salvo password -->
        <footer class="modal-footer">
          <button type="button" class="btn-secondary modal-close" data-close="modalEdit">Cancelar</button>
          <button type="submit" class="btn-primary">Guardar</button>
          <button type="button" id="btnDelete" class="btn-danger">Eliminar</button>
        </footer>
      </form>
    </div>
  </div>

</body>

</html>