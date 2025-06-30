<?php
// test_db.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/config/database.php';

try {
    // 1) ¿Se creó el objeto PDO?
    if (!$pdo instanceof PDO) {
        throw new Exception('La variable $pdo no es una instancia de PDO');
    }
    echo "✔️ Conexión a PDO OK<br>";

    // 2) Hacemos un SELECT sencillo
    $stmt = $pdo->query("SELECT NOW() AS hora_servidor");
    $row  = $stmt->fetch();
    echo "✔️ SELECT NOW(): " . htmlspecialchars($row['hora_servidor']) . "<br>";

    // 3) Contamos filas en tu tabla de alumnos (o cualquier otra)
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM alumnos");
    $row  = $stmt->fetch();
    echo "✔️ Total de alumnos en BD: " . (int)$row['total'] . "<br>";

    // 4) Intentamos insertar un registro de prueba (sin afectar tus datos reales):
    $pdo->beginTransaction();
    $testBoleta = '9999999999';
    $testNombre = 'Prueba conexion';
    $pdo->exec("
      INSERT INTO alumnos (boleta,nombre,apellido_paterno,apellido_materno,genero,curp,telefono,semestre,carrera,correo,password)
      VALUES (
        '$testBoleta',
        'Prueba',
        'Conexion',
        'BD',
        'Otro',
        AES_ENCRYPT('CURPPROBA','TU_LLAVE_DE_AES'),
        '0000000000',
        1,
        'ISC',
        'prueba@alumno.ipn.mx',
        '".password_hash('Prueba123!', PASSWORD_BCRYPT)."'
      )
    ");
    $pdo->rollBack(); // Deshacemos la inserción
    echo "✔️ Prueba de INSERT (luego deshecha) OK<br>";

} catch (Exception $e) {
    echo "❌ ERROR: " . htmlspecialchars($e->getMessage());
}
