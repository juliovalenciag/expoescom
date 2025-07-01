<?php
// bloques.php
$errors = $_SESSION['bloque_errors'] ?? [];
$success = $_SESSION['bloque_success'] ?? '';
unset($_SESSION['bloque_errors'], $_SESSION['bloque_success']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <title>Gestión de Bloques · Admin</title>
    <link rel="stylesheet" href="/expoescom/assets/css/admin-crud.css" />
</head>

<body class="admin-crud">
    <header class="site-header">
        <a href="/expoescom/"><i class="fa-solid fa-arrow-left"></i> Inicio</a>
    </header>
    <main class="crud-container">
        <h1>Gestión de Bloques</h1>

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
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bloques as $b): ?>
                    <tr>
                        <td><?= $b['id'] ?></td>
                        <td><?= htmlspecialchars($b['tipo']) ?></td>
                        <td><?= substr($b['hora_inicio'], 0, 5) ?></td>
                        <td><?= substr($b['hora_fin'], 0, 5) ?></td>
                        <td class="actions">
                            <a href="/expoescom/admin/bloques/<?= $b['id'] ?>/edit" class="btn btn-edit">✏️</a>
                            <form action="/expoescom/admin/bloques/<?= $b['id'] ?>/delete" method="POST"
                                style="display:inline">
                                <button class="btn btn-delete" type="submit">🗑️</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Crear Nuevo Bloque</h2>
        <form action="/expoescom/admin/bloques" method="POST" class="inline-form">
            <input type="text" name="tipo" placeholder="Matutino / Vespertino" required>
            <input type="time" name="hora_inicio" value="10:30" required>
            <input type="time" name="hora_fin" value="13:30" required>
            <button type="submit" class="btn btn-edit">Crear</button>
        </form>
    </main>
    <script src="/expoescom/assets/js/admin-crud.js"></script>
</body>

</html>