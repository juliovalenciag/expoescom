<!DOCTYPE html>
<html lang="es">

<head>
    <!-- … tus meta, enlaces y CSS existentes … -->
    <link rel="stylesheet" href="/expoescom/assets/css/participante-dashboard.css" />
</head>

<body class="participante-dashboard">
    <header class="site-header">…</header>

    <main class="dashboard-container">
        <h1 class="dashboard-title">Bienvenid@, <?= htmlspecialchars($info['nombre']) ?></h1>

        <!-- ERRORES / ÉXITO -->
        <?php if (!empty($_SESSION['errors_profile'])): ?>
            <div class="form-errors">
                <ul>
                    <?php foreach ($_SESSION['errors_profile'] as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach;
                    unset($_SESSION['errors_profile']); ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['success_profile'])): ?>
            <div class="form-success"><?= htmlspecialchars($_SESSION['success_profile']) ?></div>
            <?php unset($_SESSION['success_profile']); ?>
        <?php endif; ?>

        <div class="profile-card detail-card">
            <div class="card-header">
                <h4>Mi Perfil</h4>
                <button id="editProfileBtn" class="btn-action small">Editar</button>
            </div>
            <form id="profileForm" action="/expoescom/participante/editar" method="POST">
                <div class="field-group">
                    <label>Boleta</label>
                    <input type="text" name="boleta" value="<?= htmlspecialchars($info['boleta']) ?>" disabled />
                </div>
                <div class="field-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($info['nombre']) ?>" disabled />
                </div>
                <div class="field-group">
                    <label>Apellido Paterno</label>
                    <input type="text" name="apellido_paterno"
                        value="<?= htmlspecialchars($info['apellido_paterno']) ?>" disabled />
                </div>
                <div class="field-group">
                    <label>Apellido Materno</label>
                    <input type="text" name="apellido_materno"
                        value="<?= htmlspecialchars($info['apellido_materno']) ?>" disabled />
                </div>
                <div class="field-group">
                    <label>Género</label>
                    <select name="genero" disabled>
                        <?php foreach (['Mujer', 'Hombre', 'Otro'] as $g): ?>
                            <option <?= $info['genero'] === $g ? 'selected' : '' ?>><?= $g ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field-group">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($info['telefono']) ?>" disabled />
                </div>
                <div class="field-group">
                    <label>Correo</label>
                    <input type="email" name="correo" value="<?= htmlspecialchars($info['correo']) ?>" disabled />
                </div>
                <div class="field-group pwd-group">
                    <label>Contraseña actual</label>
                    <input type="password" name="current_password" placeholder="*****" disabled />
                </div>
                <div class="field-group pwd-group">
                    <label>Nueva contraseña</label>
                    <input type="password" name="new_password" placeholder="*****" disabled />
                </div>
                <div class="field-group pwd-group">
                    <label>Confirmar nueva contraseña</label>
                    <input type="password" name="confirm_password" placeholder="*****" disabled />
                </div>

                <div class="form-buttons" hidden>
                    <button type="button" id="cancelEditBtn" class="btn-prev small">Cancelar</button>
                    <button type="button" id="confirmEditBtn" class="btn-submit small">Guardar cambios</button>
                </div>
            </form>
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
            <p><strong>Horario:</strong> <?= date('H:i', strtotime($info['hora_inicio'])) ?> –
                <?= date('H:i', strtotime($info['hora_fin'])) ?>
            </p>

            <p><strong>Ganador:</strong> <?= $info['es_ganador'] ? 'Sí' : 'No' ?></p>
        </div>

        <div class="detail-card">
            <h4>Asignación</h4>
            <?php if (!empty($info['salon_id'])): ?>
                <p><strong>Salón:</strong> <?= htmlspecialchars($info['salon_id']) ?></p>
                <p><strong>Bloque:</strong> <?= htmlspecialchars($info['bloque']) ?></p>
                <p><strong>Horario:</strong> <?= date('H:i', strtotime($info['hora_inicio'])) ?> –
                    <?= date('H:i', strtotime($info['hora_fin'])) ?>
                </p>

            <?php else: ?>
                <p class="text-warning">Aún no se te ha asignado un salón.</p>
            <?php endif; ?>


        </div>
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

        <!-- MODAL DE CONFIRMACIÓN -->
        <div id="confirmModal" class="modal-overlay" hidden>
            <div class="modal">
                <h2>¿Guardar cambios?</h2>
                <p>¿Seguro que deseas actualizar tu perfil con estos datos?</p>
                <div class="modal-footer">
                    <button id="modalCancel" class="btn-prev">No, volver</button>
                    <button id="modalConfirm" class="btn-submit">Sí, guardar</button>
                </div>
            </div>
        </div>

    </main>

    <script>
        // Habilita edición
        const editBtn = document.getElementById('editProfileBtn');
        const cancelBtn = document.getElementById('cancelEditBtn');
        const confirmBtn = document.getElementById('confirmEditBtn');
        const form = document.getElementById('profileForm');
        const inputs = form.querySelectorAll('input, select');
        const buttonsDiv = form.querySelector('.form-buttons');
        const modal = document.getElementById('confirmModal');
        const modalCancel = document.getElementById('modalCancel');
        const modalConfirm = document.getElementById('modalConfirm');

        editBtn.addEventListener('click', () => {
            inputs.forEach(i => i.disabled = false);
            buttonsDiv.hidden = false;
            editBtn.hidden = true;
        });
        cancelBtn.addEventListener('click', () => {
            window.location.reload(); // recarga para descartar cambios
        });
        confirmBtn.addEventListener('click', () => {
            modal.hidden = false;
        });
        modalCancel.addEventListener('click', () => modal.hidden = true);
        modalConfirm.addEventListener('click', () => form.submit());
    </script>
</body>

</html>