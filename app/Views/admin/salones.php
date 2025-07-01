<?php
$errors = $_SESSION['salon_errors'] ?? [];
$success = $_SESSION['salon_success'] ?? '';
unset($_SESSION['salon_errors'], $_SESSION['salon_success']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <title>Gestión de Salones · Admin</title>
    <link rel="stylesheet" href="/expoescom/assets/css/admin-crud.css" />
</head>

<body class="admin-crud">
    <header class="site-header">
        <a href="/expoescom/"><i class="fa-solid fa-arrow-left"></i> Inicio</a>
        <div class="header-right">
            <a href="https://www.ipn.mx" target="_blank"><img src="/expoescom/assets/images/IPN_Logo.png" /></a>
            <a href="https://www.escom.ipn.mx" target="_blank"><img src="/expoescom/assets/images/Escom_Logo.png" /></a>
            <a href="/expoescom/logout" class="btn-back"><i class="fa-solid fa-sign-out-alt"></i> Cerrar sesión</a>
        </div>
    </header>

    <main class="crud-container">
        <h1>Gestión de Salones</h1>

        <?php if ($success): ?>
            <div class="flash success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($errors): ?>
            <div class="flash error">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <table class="crud-table">
            <thead>
                <tr>
                    <th>Salón</th>
                    <th>Matutino (ocup/total)</th>
                    <th>Vespertino (ocup/total)</th>
                    <th>Capacidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($salones as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['salon']) ?></td>
                        <td><?= $s['ocup_mat'] ?>/<?= $s['capacidad'] ?></td>
                        <td><?= $s['ocup_vesp'] ?>/<?= $s['capacidad'] ?></td>
                        <td><?= $s['capacidad'] ?></td>
                        <td class="actions">
                            <a href="/expoescom/admin/salones/<?= $s['salon'] ?>/edit" class="btn btn-edit">✏️</a>
                            <form action="/expoescom/admin/salones/<?= $s['salon'] ?>/delete" method="POST"
                                style="display:inline">
                                <button class="btn btn-delete" type="submit">🗑️</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Crear Nuevo Salón</h2>
        <form action="/expoescom/admin/salones" method="POST" class="inline-form">
            <input type="text" name="id" placeholder="ID (4010)" required pattern="\d{4}">
            <input type="number" name="capacidad" placeholder="Capacidad" min="1" value="5" required>
            <button type="submit" class="btn btn-edit">Crear</button>
        </form>
    </main>

    <script src="/expoescom/assets/js/admin-crud.js"></script>
</body>

</html>