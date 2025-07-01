<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="/expoescom/assets/images/favicon.ico" />
  <title>Participante · ExpoESCOM</title>
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css?family=Nunito:400,600&display=swap" rel="stylesheet" />
  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

  <link rel="stylesheet" href="/expoescom/assets/css/participante-dashboard.css" />
</head>

<body class="participante-dashboard">

  <!-- HEADER -->
  <header class="site-header">
    <a href="/expoescom/"><i class="fa-solid fa-arrow-left"></i> Inicio</a>
    <div class="header-right">
      <a href="https://www.ipn.mx" target="_blank"><img src="/expoescom/assets/images/IPN_Logo.png" alt="IPN" /></a>
      <a href="https://www.escom.ipn.mx" target="_blank"><img src="/expoescom/assets/images/Escom_Logo.png"
          alt="ESCOM" /></a>
      <a href="/expoescom/logout" class="btn-logout"><i class="fa-solid fa-sign-out-alt"></i> Cerrar sesión</a>
    </div>
  </header>

  <main class="dashboard">
    <h2 class="title">Bienvenid@, <?= htmlspecialchars($info['nombre']) ?>
      <?= htmlspecialchars($info['apellido_paterno']) ?></h1>

      <!-- BOTONES PDF -->
      <div class="actions">
        <?php if ($info['salon_id']): ?>
          <a href="/expoescom/pdf/acuse/<?= $info['boleta'] ?>" class="btn-pdf"><i class="fa-solid fa-file-pdf"></i>
            Descargar Acuse</a>
          <?php if ($info['es_ganador']): ?>
            <a href="/expoescom/pdf/diploma/<?= $info['boleta'] ?>" class="btn-action"><i class="fa-solid fa-award"></i>
              Descargar Diploma</a>
          <?php endif; ?>
        <?php endif; ?>
      </div>
      <?php if (!empty($_SESSION['errors_profile'])): ?>
        <div class="alert alert-error">
          <ul>
            <?php foreach ($_SESSION['errors_profile'] as $e): ?>
              <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach;
            unset($_SESSION['errors_profile']); ?>
          </ul>
        </div>
      <?php endif; ?>

      <?php if (!empty($_SESSION['success_profile'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success_profile']) ?></div>
        <?php unset($_SESSION['success_profile']); ?>
      <?php endif; ?>

      <div class="grid-dashboard">
        <!-- PERFIL: fila completa -->
        <section class="card perfil-card">
          <div class="card-header">
            <h2><i class="fa-solid fa-user"></i> Mi Perfil</h2>
            <button id="editProfileBtn" class="icon-btn"><i class="fa-solid fa-pen"></i></button>
          </div>
          <form id="profileForm" action="/expoescom/participante/editar" method="POST">
            <div class="perfil-grid">
              <div class="field"><label>Boleta</label><input name="boleta"
                  value="<?= htmlspecialchars($info['boleta']) ?>" disabled></div>
              <div class="field"><label>Nombre</label><input name="nombre"
                  value="<?= htmlspecialchars($info['nombre']) ?>" disabled></div>
              <div class="field"><label>Ap. Paterno</label><input name="apellido_paterno"
                  value="<?= htmlspecialchars($info['apellido_paterno']) ?>" disabled></div>
              <div class="field"><label>Ap. Materno</label><input name="apellido_materno"
                  value="<?= htmlspecialchars($info['apellido_materno']) ?>" disabled></div>
              <div class="field"><label>Género</label>
                <select name="genero" disabled>
                  <?php foreach (['Mujer', 'Hombre', 'Otro'] as $g): ?>
                    <option <?= $info['genero'] === $g ? 'selected' : '' ?>><?= $g ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="field"><label>Teléfono</label><input name="telefono"
                  value="<?= htmlspecialchars($info['telefono']) ?>" disabled></div>
              <div class="field full"><label>Correo</label><input type="email" name="correo"
                  value="<?= htmlspecialchars($info['correo']) ?>" disabled></div>

              <!-- Contraseñas sólo en edición -->
              <div id="pwd1" class="field full hidden">
                <label>Contraseña Actual</label>
                <input type="password" name="current_password" placeholder="••••••">
              </div>
              <div id="pwd2" class="field full hidden">
                <label>Nueva Contraseña</label>
                <input type="password" name="new_password" placeholder="••••••">
              </div>
              <div id="pwd3" class="field full hidden">
                <label>Confirmar Contraseña</label>
                <input type="password" name="confirm_password" placeholder="••••••">
              </div>
            </div>
            <div class="form-actions hidden">
              <button type="button" id="cancelEditBtn" class="btn-secondary">Cancelar</button>
              <button type="button" id="confirmEditBtn" class="btn-primary">Guardar</button>
            </div>
          </form>
        </section>

        <!-- ACADÉMICA -->
        <section class="card info-card">
          <h2><i class="fa-solid fa-graduation-cap"></i> Académica</h2>
          <p><strong>Carrera:</strong> <?= htmlspecialchars($info['carrera']) ?></p>
          <p><strong>Semestre:</strong> <?= htmlspecialchars($info['semestre']) ?></p>
        </section>

        <!-- CONCURSO -->
        <section class="card info-card">
          <h2><i class="fa-solid fa-lightbulb"></i> Concurso</h2>
          <p><strong>Equipo:</strong> <?= htmlspecialchars($info['nombre_equipo']) ?></p>
          <p><strong>Proyecto:</strong> <?= htmlspecialchars($info['nombre_proyecto']) ?></p>
          <p><strong>Horario:</strong> <?= date('H:i', strtotime($info['hora_inicio'])) ?> –
            <?= date('H:i', strtotime($info['hora_fin'])) ?>
          </p>
          <p><strong>Ganador:</strong> <?= $info['es_ganador'] ? 'Sí' : 'No' ?></p>
        </section>

        <!-- ASIGNACIÓN -->
        <section class="card info-card">
          <h2><i class="fa-solid fa-door-open"></i> Asignación</h2>
          <?php if ($info['salon_id']): ?>
            <p><strong>Salón:</strong> <?= htmlspecialchars($info['salon_id']) ?></p>
            <p><strong>Bloque:</strong> <?= htmlspecialchars($info['bloque']) ?></p>
            <p><strong>Horario:</strong> <?= date('H:i', strtotime($info['hora_inicio'])) ?> –
              <?= date('H:i', strtotime($info['hora_fin'])) ?>
            </p>
          <?php else: ?>
            <p class="text-warning">Aún no se ha asignado salón.</p>
          <?php endif; ?>
        </section>

        <!-- MIEMBROS DE EQUIPO -->
        <section class="card members-card">
          <h2><i class="fa-solid fa-users"></i> Miembros de mi equipo</h2>
          <?php if (empty($miembros)): ?>
            <p class="text-warning">No hay otros miembros asignados aún.</p>
          <?php else: ?>
            <ul class="members-list">
              <?php foreach ($miembros as $m): ?>
                <li>
                  <strong><?= htmlspecialchars($m['nombre'] . ' ' . $m['apellido_paterno']) ?></strong>
                  <span><?= htmlspecialchars($m['boleta']) ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </section>

        <!-- CALENDARIO -->
        <section class="card calendar-card">
          <h2><i class="fa-solid fa-calendar-days"></i> Mi Calendario</h2>
          <div id="calendar"></div>
        </section>
      </div>

      <!-- MODAL CONFIRMACIÓN -->
      <div id="confirmModal" class="modal-overlay hidden">
        <div class="modal">
          <h3>¿Guardar cambios?</h3>
          <p>¿Seguro que quieres actualizar tu perfil?</p>
          <div class="modal-footer">
            <button id="modalCancel" class="btn-secondary">Cancelar</button>
            <button id="modalConfirm" class="btn-primary">Sí, guardar</button>
          </div>
        </div>
      </div>
  </main>

  <script>
    const editBtn = document.getElementById('editProfileBtn'),
      cancelBtn = document.getElementById('cancelEditBtn'),
      confirmBtn = document.getElementById('confirmEditBtn'),
      form = document.getElementById('profileForm'),
      fields = form.querySelectorAll('input,select'),
      pwd1 = document.getElementById('pwd1'),
      pwd2 = document.getElementById('pwd2'),
      pwd3 = document.getElementById('pwd3'),
      actions = form.querySelector('.form-actions'),
      modal = document.getElementById('confirmModal'),
      mCancel = document.getElementById('modalCancel'),
      mConfirm = document.getElementById('modalConfirm'),
      logout = document.getElementById('logoutBtn');

    editBtn.addEventListener('click', () => {
      fields.forEach(f => f.disabled = false);
      pwd1.classList.remove('hidden');
      pwd2.classList.remove('hidden');
      pwd3.classList.remove('hidden');
      actions.classList.remove('hidden');
      editBtn.classList.add('hidden');
    });
    cancelBtn.addEventListener('click', () => location.reload());
    confirmBtn.addEventListener('click', () => modal.classList.remove('hidden'));
    mCancel.addEventListener('click', () => modal.classList.add('hidden'));
    mConfirm.addEventListener('click', () => form.submit());
    logout.addEventListener('click', () => location.href = '/expoescom/logout');
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var calendarEl = document.getElementById('calendar');
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: ''
        },
        events: '/expoescom/api/calendar'
      });
      calendar.render();
    });
  </script>
</body>

</html>