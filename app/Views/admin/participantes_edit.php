<?php
// app/Views/admin/participantes_edit.php
// Recibe $participant, $academias, $unidadesPorAcademia, opcional $_SESSION['errors_part'], $_SESSION['success_part']
$errors = $_SESSION['errors_part'] ?? [];
$success = $_SESSION['success_part'] ?? '';
unset($_SESSION['errors_part'], $_SESSION['success_part']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <title>Editar Participante · Admin ExpoESCOM</title>
    <link rel="stylesheet" href="/expoescom/assets/css/admin-participantes-form.css" />
    <script defer src="/expoescom/assets/js/admin-participantes-edit.js"></script>
</head>

<body class="admin-dashboard">
    <header class="site-header">
        <a href="/expoescom/admin/participantes">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </header>
    <main class="dashboard-container form-container">
        <h1>Editar Participante</h1>

        <?php if ($errors): ?>
            <div class="form-errors">
                <ul><?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="form-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form action="/expoescom/admin/participantes/edit/<?= $participant['boleta'] ?>" method="POST" novalidate>
            <div class="field-group">
                <label>Boleta (no editable)</label>
                <input value="<?= htmlspecialchars($participant['boleta']) ?>" disabled>
            </div>

            <div class="field-group">
                <label>Nombre</label>
                <input name="nombre" value="<?= htmlspecialchars($participant['nombre']) ?>" required>
                <small class="error">El nombre es obligatorio.</small>
            </div>

            <div class="field-group">
                <label>Apellido Paterno</label>
                <input name="apellido_paterno" value="<?= htmlspecialchars($participant['apellido_paterno']) ?>"
                    required>
                <small class="error">Obligatorio.</small>
            </div>

            <div class="field-group">
                <label>Apellido Materno</label>
                <input name="apellido_materno" value="<?= htmlspecialchars($participant['apellido_materno']) ?>"
                    required>
                <small class="error">Obligatorio.</small>
            </div>

            <div class="field-group">
                <label>Género</label>
                <select name="genero" required>
                    <?php foreach (['Mujer', 'Hombre', 'Otro'] as $g): ?>
                        <option value="<?= $g ?>" <?= $participant['genero'] === $g ? 'selected' : '' ?>>
                            <?= $g ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="error">Selecciona género.</small>
            </div>

            <div class="field-group">
                <label>Teléfono</label>
                <input name="telefono" value="<?= htmlspecialchars($participant['telefono']) ?>" required
                    pattern="\d{10}">
                <small class="error">Debe tener 10 dígitos.</small>
            </div>

            <div class="field-group label-group">
                <label>Correo institucional</label>
                <div class="input-group">
                    <input name="correo_local" value="<?= explode('@', $participant['correo'])[0] ?>" required>
                    <span class="input-addon">@alumno.ipn.mx</span>
                </div>
                <small class="error">Formato incorrecto.</small>
            </div>

            <div class="field-group">
                <label>Semestre</label>
                <input type="number" name="semestre" min="1" max="8" value="<?= $participant['semestre'] ?>" required>
                <small class="error">1–8.</small>
            </div>

            <div class="field-group">
                <label>Carrera</label>
                <select name="carrera" required>
                    <?php foreach (['ISC', 'LCD', 'IIA'] as $c): ?>
                        <option value="<?= $c ?>" <?= $participant['carrera'] === $c ? 'selected' : '' ?>>
                            <?= $c ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="error">Selecciona carrera.</small>
            </div>

            <div class="field-group">
                <label>Academia</label>
                <select id="editAcademia" name="academia_id" required>
                    <?php foreach ($academias as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= $participant['academia_id'] == $a['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($a['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="error">Elige academia.</small>
            </div>

            <div class="field-group">
                <label>Unidad aprendizaje</label>
                <select id="editUnidad" name="unidad_id" required>
                    <!-- cargado por JS -->
                </select>
                <small class="error">Elige unidad.</small>
            </div>

            <div class="field-group">
                <label>Nombre de equipo</label>
                <input name="nombre_equipo" value="<?= htmlspecialchars($participant['nombre_equipo']) ?>" required>
                <small class="error">Mín. 3 caracteres.</small>
            </div>

            <div class="field-group">
                <label>Nombre de proyecto</label>
                <input name="nombre_proyecto" value="<?= htmlspecialchars($participant['nombre_proyecto']) ?>" required>
                <small class="error">Mín. 3 caracteres.</small>
            </div>

            <button type="submit" class="btn-save">Actualizar Participante</button>
        </form>
    </main>

    <script>
        // carga dinámica de unidades según academia
        const unidadesPorAcademia = <?= json_encode($unidadesPorAcademia) ?>;
        const selA = document.getElementById('editAcademia'),
            selU = document.getElementById('editUnidad'),
            pre = <?= $participant['unidad_id'] ?>;
        function recarga() {
            selU.innerHTML = '';
            (unidadesPorAcademia[selA.value] || []).forEach(u => {
                const o = document.createElement('option');
                o.value = u.id;
                o.textContent = u.nombre;
                if (u.id == pre) o.selected = true;
                selU.appendChild(o);
            });
        }
        selA.addEventListener('change', recarga);
        window.addEventListener('DOMContentLoaded', recarga);
    </script>
</body>

</html>