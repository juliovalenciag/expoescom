<?php
// salones_edit.php
$salon = $salon;  // viene del controller
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <title>Editar Salón <?= htmlspecialchars($salon['id']) ?></title>
    <link rel="stylesheet" href="/expoescom/assets/css/admin-crud.css" />
</head>

<body class="admin-crud">
    <header class="site-header">
        <a href="/expoescom/admin/salones"><i class="fa-solid fa-arrow-left"></i> Volver</a>
        <div class="header-right">
            <a href="https://www.ipn.mx" target="_blank"><img src="/expoescom/assets/images/IPN_Logo.png"
                    alt="IPN" /></a>
            <a href="https://www.escom.ipn.mx" target="_blank"><img src="/expoescom/assets/images/Escom_Logo.png"
                    alt="ESCOM" /></a>

        </div>
    </header>
    <main class="crud-container">
        <h1>Editar Salón <?= htmlspecialchars($salon['id']) ?></h1>
        <form action="/expoescom/admin/salones/<?= $salon['id'] ?>" method="POST" class="inline-form">
            <label>Capacidad:</label>
            <input type="number" name="capacidad" value="<?= $salon['capacidad'] ?>" min="1" required>
            <button type="submit" class="btn btn-edit">Guardar</button>
            <a href="/expoescom/admin/salones" class="btn btn-back">Cancelar</a>
        </form>
    </main>
</body>

</html>