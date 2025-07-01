<?php
// Variables esperadas en este scope:
//   $participant        // array con datos del participante cuando es edición, o null para creación
//   $academias          // [ ['id'=>..,'nombre'=>..], … ]
//   $unidadesPorAcademia// [ academia_id => [ ['id'=>..,'nombre'=>..], … ], … ]
?>
<div id="participantModal" class="modal-overlay" hidden>
    <div class="modal">
        <h2><?= isset($participant) ? 'Editar Participante' : 'Agregar Participante' ?></h2>
        <form id="participantForm">
            <input type="hidden" name="boleta_original" value="<?= htmlspecialchars($participant['boleta'] ?? '') ?>">

            <div class="field-group">
                <label for="partBoleta">Boleta</label>
                <input type="text" id="partBoleta" name="boleta"
                    value="<?= htmlspecialchars($participant['boleta'] ?? '') ?>" <?= isset($participant) ? 'disabled' : 'required' ?> />
                <small class="error" id="errorBoleta"></small>
            </div>

            <div class="field-group">
                <label for="partNombre">Nombre</label>
                <input type="text" id="partNombre" name="nombre"
                    value="<?= htmlspecialchars($participant['nombre'] ?? '') ?>" required />
                <small class="error" id="errorNombre"></small>
            </div>

            <div class="field-group">
                <label for="partApellidoP">Apellido Paterno</label>
                <input type="text" id="partApellidoP" name="apellido_paterno"
                    value="<?= htmlspecialchars($participant['apellido_paterno'] ?? '') ?>" required />
                <small class="error" id="errorApP"></small>
            </div>

            <div class="field-group">
                <label for="partApellidoM">Apellido Materno</label>
                <input type="text" id="partApellidoM" name="apellido_materno"
                    value="<?= htmlspecialchars($participant['apellido_materno'] ?? '') ?>" required />
                <small class="error" id="errorApM"></small>
            </div>

            <div class="field-group">
                <label for="partGenero">Género</label>
                <select id="partGenero" name="genero" required>
                    <option value="">Selecciona…</option>
                    <?php foreach (['Mujer', 'Hombre', 'Otro'] as $g): ?>
                        <option value="<?= $g ?>" <?= isset($participant) && $participant['genero'] == $g ? 'selected' : '' ?>>
                            <?= $g ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="error" id="errorGenero"></small>
            </div>

            <div class="field-group">
                <label for="partCurp">CURP</label>
                <input type="text" id="partCurp" name="curp" pattern="[A-ZÑ]{4}\d{6}[HM][A-ZÑ]{5}[A-Z0-9]\d"
                    value="<?= htmlspecialchars($participant['curp_plain'] ?? '') ?>" required />
                <small class="error" id="errorCurp"></small>
            </div>

            <div class="field-group">
                <label for="partTelefono">Teléfono</label>
                <input type="text" id="partTelefono" name="telefono" pattern="\d{10}"
                    value="<?= htmlspecialchars($participant['telefono'] ?? '') ?>" required />
                <small class="error" id="errorTelefono"></small>
            </div>

            <div class="field-group">
                <label for="partCorreoLocal">Correo</label>
                <div class="input-group">
                    <input type="text" id="partCorreoLocal" name="correo_local"
                        value="<?= isset($participant) ? explode('@', $participant['correo'])[0] : '' ?>" required />
                    <span class="input-group-addon">@alumno.ipn.mx</span>
                </div>
                <small class="error" id="errorCorreo"></small>
            </div>

            <div class="field-group">
                <label for="partSemestre">Semestre</label>
                <input type="number" id="partSemestre" name="semestre" min="1" max="8"
                    value="<?= htmlspecialchars($participant['semestre'] ?? '') ?>" required />
                <small class="error" id="errorSemestre"></small>
            </div>

            <div class="field-group">
                <label for="partCarrera">Carrera</label>
                <select id="partCarrera" name="carrera" required>
                    <option value="">Selecciona…</option>
                    <?php foreach (['ISC', 'LCD', 'IIA'] as $c): ?>
                        <option value="<?= $c ?>" <?= isset($participant) && $participant['carrera'] == $c ? 'selected' : '' ?>>
                            <?= $c ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="error" id="errorCarrera"></small>
            </div>

            <div class="field-group">
                <label for="partAcademia">Academia</label>
                <select id="partAcademia" name="academia_id" required>
                    <option value="">Selecciona…</option>
                    <?php foreach ($academias as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= isset($participant) && $participant['academia_id'] == $a['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($a['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="error" id="errorAcademia"></small>
            </div>

            <div class="field-group">
                <label for="partUnidad">Unidad aprendizaje</label>
                <select id="partUnidad" name="unidad_id" required>
                    <option value="">Selecciona academia primero</option>
                </select>
                <small class="error" id="errorUnidad"></small>
            </div>

            <div class="field-group">
                <label for="partHorario">Horario preferido</label>
                <select id="partHorario" name="horario_preferencia" required>
                    <option value="">Selecciona…</option>
                    <option value="Matutino" <?= isset($participant) && $participant['horario_preferencia'] == 'Matutino' ? 'selected' : '' ?>>
                        Matutino (10:30–13:30)
                    </option>
                    <option value="Vespertino" <?= isset($participant) && $participant['horario_preferencia'] == 'Vespertino' ? 'selected' : '' ?>>
                        Vespertino (15:00–18:00)
                    </option>
                </select>
                <small class="error" id="errorHorario"></small>
            </div>

            <div class="field-group">
                <label for="partEquipo">Nombre de equipo</label>
                <input type="text" id="partEquipo" name="nombre_equipo"
                    value="<?= htmlspecialchars($participant['nombre_equipo'] ?? '') ?>" required />
                <small class="error" id="errorEquipo"></small>
            </div>

            <div class="field-group">
                <label for="partProyecto">Nombre de proyecto</label>
                <input type="text" id="partProyecto" name="nombre_proyecto"
                    value="<?= htmlspecialchars($participant['nombre_proyecto'] ?? '') ?>" required />
                <small class="error" id="errorProyecto"></small>
            </div>

            <div class="form-actions">
                <button type="button" id="cancelParticipantBtn" class="btn-cancel">Cancelar</button>
                <button type="submit" id="saveParticipantBtn" class="btn-save">
                    <?= isset($participant) ? 'Actualizar' : 'Crear' ?>
                </button>
            </div>
        </form>
    </div>
</div>