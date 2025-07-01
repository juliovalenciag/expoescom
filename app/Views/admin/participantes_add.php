<?php
// app/Views/admin/participantes_add.php
// Recibe $academias y $unidadesPorAcademia
$errors = $_SESSION['errors_add'] ?? [];
unset($_SESSION['errors_add']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Nuevo Participante · Admin ExpoESCOM</title>
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/expoescom/assets/css/admin-participantes-form.css">
</head>

<body class="admin-dashboard">
    <header class="site-header">
        <a href="/expoescom/admin/participantes"><i class="fa-solid fa-arrow-left"></i> Volver</a>
        <div class="header-right">
            <a href="https://www.ipn.mx" target="_blank"><img src="/expoescom/assets/images/IPN_Logo.png"
                    alt="IPN" /></a>
            <a href="https://www.escom.ipn.mx" target="_blank"><img src="/expoescom/assets/images/Escom_Logo.png"
                    alt="ESCOM" /></a>
            
        </div>
    </header>
    <main class="dashboard-container form-container">
        <h1>Agregar Participante</h1>

        <?php if ($errors): ?>
            <div class="form-errors">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form id="addForm" action="/expoescom/admin/participantes/add" method="POST" novalidate>
            <div class="field-group" data-field="boleta">
                <label for="boleta">Boleta</label>
                <input id="boleta" name="boleta" required pattern="(?:\d{10}|(?:PE|PP)\d{8})" />
                <small class="error">10 dígitos o PE/PP+8 dígitos</small>
            </div>

            <div class="field-group" data-field="nombre">
                <label for="nombre">Nombre</label>
                <input id="nombre" name="nombre" required />
                <small class="error">Solo letras y espacios (máx. 40)</small>
            </div>

            <div class="field-group" data-field="apellido_paterno">
                <label for="apellido_paterno">Apellido Paterno</label>
                <input id="apellido_paterno" name="apellido_paterno" required />
                <small class="error">Solo letras y espacios</small>
            </div>

            <div class="field-group" data-field="apellido_materno">
                <label for="apellido_materno">Apellido Materno</label>
                <input id="apellido_materno" name="apellido_materno" required />
                <small class="error">Solo letras y espacios</small>
            </div>

            <div class="field-group" data-field="genero">
                <label for="genero">Género</label>
                <select id="genero" name="genero" required>
                    <option value="">Selecciona…</option>
                    <?php foreach (['Mujer', 'Hombre', 'Otro'] as $g): ?>
                        <option value="<?= $g ?>"><?= $g ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="error">Obligatorio</small>
            </div>

            <div class="field-group" data-field="curp">
                <label for="curp">CURP</label>
                <input id="curp" name="curp" required
                    pattern="[A-ZÑ][AEIOUÑ][A-ZÑ]{2}\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[HM][A-ZÑ]{5}[A-Z0-9]\d"
                    maxlength="18" />
                <small class="error">Formato inválido</small>
            </div>

            <div class="field-group" data-field="telefono">
                <label for="telefono">Teléfono</label>
                <input id="telefono" name="telefono" required pattern="\d{10}" maxlength="10" />
                <small class="error">10 dígitos</small>
            </div>

            <div class="field-group label-group" data-field="correo_local">
                <label for="correo_local">Correo institucional</label>
                <div class="input-group">
                    <input id="correo_local" name="correo_local" required pattern="[\w.+-]+" />
                    <span class="input-addon">@alumno.ipn.mx</span>
                </div>
                <small class="error">Solo la parte local (sin @…)</small>
            </div>

            <div class="field-group" data-field="password">
                <label for="password">Contraseña inicial</label>
                <input id="password" name="password" type="password" required minlength="8" />
                <button type="button" class="eye-btn" data-target="password">
                    <i class="fa-solid fa-eye"></i>
                </button>
                <small class="error">8+ car., 1 may., 1 díg., 1 esp.</small>
            </div>

            <div class="field-group" data-field="semestre">
                <label for="semestre">Semestre</label>
                <input id="semestre" name="semestre" type="number" min="1" max="8" required />
                <small class="error">Entre 1 y 8</small>
            </div>

            <div class="field-group" data-field="carrera">
                <label for="carrera">Carrera</label>
                <select id="carrera" name="carrera" required>
                    <option value="">Selecciona…</option>
                    <?php foreach (['ISC', 'LCD', 'IIA'] as $c): ?>
                        <option value="<?= $c ?>"><?= $c ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="error">Obligatorio</small>
            </div>

            <div class="field-group" data-field="academia_id">
                <label for="addAcademia">Academia</label>
                <select id="addAcademia" name="academia_id" required>
                    <option value="">Selecciona…</option>
                    <?php foreach ($academias as $a): ?>
                        <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="error">Obligatorio</small>
            </div>

            <div class="field-group" data-field="unidad_id">
                <label for="addUnidad">Unidad aprendizaje</label>
                <select id="addUnidad" name="unidad_id" required>
                    <option value="">Selecciona academia primero</option>
                </select>
                <small class="error">Obligatorio</small>
            </div>

            <div class="field-group" data-field="horario_preferencia">
                <label for="horario_preferencia">Horario preferido</label>
                <select id="horario_preferencia" name="horario_preferencia" required>
                    <option value="">Selecciona…</option>
                    <option value="Matutino">Matutino (10:30–13:30)</option>
                    <option value="Vespertino">Vespertino (15:00–18:00)</option>
                </select>
                <small class="error">Obligatorio</small>
            </div>

            <div class="field-group" data-field="nombre_equipo">
                <label for="nombre_equipo">Nombre de equipo</label>
                <input id="nombre_equipo" name="nombre_equipo" required />
                <small class="error">Mín. 3 car.</small>
            </div>

            <div class="field-group" data-field="nombre_proyecto">
                <label for="nombre_proyecto">Nombre de proyecto</label>
                <input id="nombre_proyecto" name="nombre_proyecto" required />
                <small class="error">Mín. 3 car.</small>
            </div>

            <button type="submit" class="btn-save">Crear Participante</button>
            <small id="form-error"></small>
        </form>
    </main>

    <script>

        window.unidadesPorAcademia = <?= json_encode($unidadesPorAcademia, JSON_UNESCAPED_UNICODE) ?>;
    </script>
    <script defer src="/expoescom/assets/js/admin-participantes-add.js"></script>
</body>

</html>