<?php
// app/Views/auth/register.php
// Variables: $errors (array), $old (array), $academias (array of ['id','nombre'])
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registro · ExpoEscom</title>
    <link rel="icon" href="/expoescom/assets/images/favicon.ico" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/expoescom/assets/css/registro.css" />

    <script>
  window.academias            = <?= json_encode($academias,           JSON_UNESCAPED_UNICODE) ?>;
  window.unidadesPorAcademia  = <?= json_encode($unidadesPorAcademia, JSON_UNESCAPED_UNICODE) ?>;
  window.oldAcademiaId        = <?= json_encode($old['academia'] ?? '') ?>;
  window.oldUnidadId          = <?= json_encode($old['unidad']   ?? '') ?>;
</script>
    <script defer src="/expoescom/assets/js/registro.js"></script>


  </head>
  <body>
    <header class="site-header">
      <a href="/expoescom/"><i class="fa-solid fa-arrow-left"></i> Inicio</a>
      <div>
        <a href="https://www.ipn.mx" target="_blank"><img src="/expoescom/assets/images/IPN_Logo.png" alt="IPN" /></a>
        <a href="https://www.escom.ipn.mx" target="_blank"><img src="/expoescom/assets/images/Escom_Logo.png" alt="ESCOM" /></a>
      </div>
    </header>

    <main class="wrapper">
      <h1 class="form-title">Registro · ExpoEscom</h1>
      <div class="form-card">
        <!-- STEP INDICATOR -->
        <div class="stepper">
          <div class="bar-fill"></div>
          <div class="step active" data-step="1">
            <div class="circle">1</div>
            <div class="label">Personales</div>
          </div>
          <div class="step" data-step="2">
            <div class="circle">2</div>
            <div class="label">Cuenta</div>
          </div>
          <div class="step" data-step="3">
            <div class="circle">3</div>
            <div class="label">Concurso</div>
          </div>
        </div>

       <?php if (!empty($errors)): ?>
  <div class="form-errors">
    <ul>
      <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

        <form id="registroForm" action="/expoescom/register" method="POST" novalidate>
          <!-- STEP 1: DATOS PERSONALES -->
          <div class="step-content active" data-step="1">
            <!-- Boleta -->
            <div class="field-group">
              <label for="boleta"><i class="fa-solid fa-id-card"></i> Boleta</label>
              <input
                type="text" id="boleta" name="boleta" maxlength="10"
                placeholder="2012345678"
                value="<?= htmlspecialchars($old['boleta'] ?? '') ?>"
                required
              />
              <small class="error">Debe ser 10 dígitos o PE/PP + 8 dígitos</small>
            </div>
            <!-- Nombre(s) -->
            <div class="field-group">
              <label for="nombre"><i class="fa-solid fa-user"></i> Nombre(s)</label>
              <input
                type="text" id="nombre" name="nombre"
                placeholder="Tu nombre"
                value="<?= htmlspecialchars($old['nombre'] ?? '') ?>"
                required
              />
              <small class="error">Solo letras y espacios</small>
            </div>
            <!-- Apellidos -->
            <div class="field-group">
              <label for="apellido_paterno">Apellido Paterno</label>
              <input
                type="text" id="apellido_paterno" name="apellido_paterno"
                placeholder="Apellido paterno"
                value="<?= htmlspecialchars($old['apellido_paterno'] ?? '') ?>"
                required
              />
              <small class="error">Solo letras y espacios</small>
            </div>
            <div class="field-group">
              <label for="apellido_materno">Apellido Materno</label>
              <input
                type="text" id="apellido_materno" name="apellido_materno"
                placeholder="Apellido materno"
                value="<?= htmlspecialchars($old['apellido_materno'] ?? '') ?>"
                required
              />
              <small class="error">Solo letras y espacios</small>
            </div>
            <!-- Género -->
            <div class="field-group sexo-group">
  <label><i class="fa-solid fa-venus-mars"></i> Género</label>
  <div class="radio-list">
    <label class="radio-wrapper">
      <input
        type="radio"
        name="genero"
        value="Mujer"
        required
        <?= (isset($old['genero']) && $old['genero']==='Mujer')?'checked':'' ?>
      />
      <span class="radio-custom"></span>
      <span class="radio-label">Mujer</span>
    </label>
    <label class="radio-wrapper">
      <input
        type="radio"
        name="genero"
        value="Hombre"
        required
        <?= (isset($old['genero']) && $old['genero']==='Hombre')?'checked':'' ?>
      />
      <span class="radio-custom"></span>
      <span class="radio-label">Hombre</span>
    </label>
    <label class="radio-wrapper">
      <input
        type="radio"
        name="genero"
        value="Otro"
        required
        <?= (isset($old['genero']) && $old['genero']==='Otro')?'checked':'' ?>
      />
      <span class="radio-custom"></span>
      <span class="radio-label">Otro</span>
    </label>
  </div>
  <small class="error">Debes elegir un sexo</small>
</div>

            <!-- Teléfono y CURP -->
            <div class="field-group">
              <label for="telefono"><i class="fa-solid fa-phone"></i> Teléfono</label>
              <input
                type="tel" id="telefono" name="telefono" maxlength="10"
                placeholder="10 dígitos"
                value="<?= htmlspecialchars($old['telefono'] ?? '') ?>"
                required
              />
              <small class="error">Exactamente 10 dígitos</small>
            </div>
            <div class="field-group">
              <label for="curp"><i class="fa-solid fa-address-card"></i> CURP</label>
              <input
                type="text" id="curp" name="curp" maxlength="18"
                placeholder="Tu CURP"
                value="<?= htmlspecialchars($old['curp'] ?? '') ?>"
                required
              />
              <small class="error">Formato inválido</small>
            </div>
            <!-- Semestre y Carrera -->
            <div class="field-group inline">
              <div>
                <label for="semestre"><i class="fa-solid fa-graduation-cap"></i> Semestre</label>
                <select id="semestre" name="semestre" required>
                  <option value="">Selecciona</option>
                  <?php for($i=1;$i<=8;$i++): ?>
                    <option
                      value="<?=$i?>"
                      <?= (isset($old['semestre']) && $old['semestre']===$i)?'selected':'' ?>
                    ><?=$i?></option>
                  <?php endfor; ?>
                </select>
                <small class="error">Elige un semestre</small>
              </div>
              <div>
                <label for="carrera"><i class="fa-solid fa-book"></i> Carrera</label>
                <select id="carrera" name="carrera" required>
                  <option value="">Selecciona</option>
                  <option value="ISC" <?= (isset($old['carrera']) && $old['carrera']==='ISC')?'selected':'' ?>>ISC</option>
                  <option value="IIA" <?= (isset($old['carrera']) && $old['carrera']==='IIA')?'selected':'' ?>>IIA</option>
                  <option value="LCD" <?= (isset($old['carrera']) && $old['carrera']==='LCD')?'selected':'' ?>>LCD</option>
                </select>
                <small class="error">Elige una carrera</small>
              </div>
            </div>
            <div class="buttons">
              <button type="button" class="btn-next">Siguiente</button>
            </div>
          </div>

          <!-- STEP 2: DATOS DE CUENTA -->
          <div class="step-content" data-step="2">
            <div class="field-group">
              <label for="correo"><i class="fa-solid fa-envelope"></i> Correo</label>
              <input
                type="email" id="correo" name="correo"
                placeholder="usuario@alumno.ipn.mx"
                value="<?= htmlspecialchars($old['correo'] ?? '') ?>"
                required
              />
              <small class="error">Debe terminar en @alumno.ipn.mx</small>
            </div>
            <div class="field-group pwd-group">
              <label for="contrasena"><i class="fa-solid fa-lock"></i> Contraseña</label>
              <input
                type="password" id="contrasena" name="password1"
                placeholder="Mín. 8 car., 1 may., 1 díg., 1 esp."
                required
              />
              <button type="button" class="eye-btn" data-target="contrasena">
                <i class="fa-solid fa-eye"></i>
              </button>
              <small class="error">Mín. 8 car., 1 may., 1 díg., 1 esp.</small>
            </div>
            <div class="field-group pwd-group">
              <label for="contrasena2"><i class="fa-solid fa-lock"></i> Confirmar Contraseña</label>
              <input
                type="password" id="contrasena2" name="password2"
                placeholder="Repite tu contraseña"
                required
              />
              <button type="button" class="eye-btn" data-target="contrasena2">
                <i class="fa-solid fa-eye"></i>
              </button>
              <small class="error">Las contraseñas deben coincidir</small>
            </div>
            <div class="buttons">
              <button type="button" class="btn-prev">Atrás</button>
              <button type="button" class="btn-next">Siguiente</button>
            </div>
          </div>

          <!-- STEP 3: DATOS CONCURSO -->
          <div class="step-content" data-step="3">
            <!-- horario -->
            <div class="field-group">
              <label for="horario"><i class="fa-solid fa-clock"></i> Horario</label>
              <select id="horario" name="horario" required>
                <option value="">Selecciona</option>
                <option value="Matutino" <?= (isset($old['horario']) && $old['horario']==='Matutino')?'selected':'' ?>>Matutino</option>
                <option value="Vespertino" <?= (isset($old['horario']) && $old['horario']==='Vespertino')?'selected':'' ?>>Vespertino</option>
              </select>
              <small class="error">Debes seleccionar horario</small>
            </div>
            <div class="field-group">
              <!-- academia -->
        <label for="academia"><i class="fa-solid fa-graduation-cap"></i> Academia</label>
        <select id="academia" name="academia" required>
          <option value="">Selecciona</option>
          <?php foreach($academias as $a): ?>
            <option value="<?= $a['id'] ?>"
              <?= (isset($old['academia']) && $old['academia']===$a['id'])?'selected':'' ?>>
              <?= htmlspecialchars($a['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <small class="error">Debes seleccionar academia</small>

      </div>
      <!-- unidaddes de aprendizaje -->
             <div class="field-group">
        <label for="unidad"><i class="fa-solid fa-chalkboard"></i> Unidad Aprendizaje</label>
        <select id="unidad" name="unidad" required>
    <option value="">Selecciona unidad</option>
    <!-- Se llenará dinámicamente desde JS -->
  </select>
        <small class="error">Debes elegir una unidad válida</small>
      </div>
            <div class="field-group">
              <label for="nombre_proyecto"><i class="fa-solid fa-lightbulb"></i> Proyecto</label>
              <input
                type="text" id="nombre_proyecto" name="nombre_proyecto"
                placeholder="Nombre proyecto"
                value="<?= htmlspecialchars($old['nombre_proyecto'] ?? '') ?>"
                required
              />
              <small class="error">Min 3 car.</small>
            </div>
            <div class="field-group">
              <label for="nombre_equipo"><i class="fa-solid fa-users"></i> Equipo</label>
              <input
                type="text" id="nombre_equipo" name="nombre_equipo"
                placeholder="Nombre equipo"
                value="<?= htmlspecialchars($old['nombre_equipo'] ?? '') ?>"
                required
              />
              <small class="error">Min 3 car.</small>
            </div>
            <div class="buttons">
              <button type="button" class="btn-prev">Atrás</button>
              <button type="submit" class="btn-submit">Enviar</button>
            </div>
            <small id="form-error" class="form-error"></small>
            <small id="form-success" class="form-success"></small>
            <button id="modifyBtn" class="btn-prev" style="display: none; margin-top: 10px">
              Modificar datos
            </button>
          </div>
        </form>
      </div>
    </main>

    <!-- MODAL DE CONFIRMACIÓN -->
  <div id="confirmModal" class="modal-overlay">
      <div class="modal">
        <h2 class="modal-title">Verifica tus datos</h2>
        <div id="confirmDetails" class="modal-body">
          <!-- Aquí inyectamos la tabla de resumen -->
        </div>
        <div class="modal-footer">
          <button id="editBtn" class="btn-prev">Editar</button>
          <button id="confirmBtn" class="btn-submit">Confirmar</button>
        </div>
      </div>
    </div>
  </body>
</html>
