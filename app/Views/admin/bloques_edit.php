<?php
// bloques_edit.php
$bloque = $bloque;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <title>Editar Bloque <?= $bloque['tipo'] ?></title>
    <link rel="stylesheet" href="/expoescom/assets/css/admin-crud.css" />
</head>

<body class="admin-crud">
    <header class="site-header">
        <a href="/expoescom/admin/bloques"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </header>
    <main class="crud-container">
        <h1>Editar Bloque <?= htmlspecialchars($bloque['tipo']) ?></h1>
        <form action="/expoescom/admin/bloques/<?= $bloque['id'] ?>" method="POST" class="inline-form">
            <input type="text" name="tipo" value="<?= htmlspecialchars($bloque['tipo']) ?>" required>
            <input type="time" name="hora_inicio" value="<?= substr($bloque['hora_inicio'], 0, 5) ?>" required>
            <input type="time" name="hora_fin" value="<?= substr($bloque['hora_fin'], 0, 5) ?>" required>
            <button type="submit" class="btn btn-edit">Guardar</button>
            <a href="/expoescom/admin/bloques" class="btn btn-back">Cancelar</a>
        </form>
    </main>
</body>

</html>