<?php
// test_insert.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/config/database.php';

// Datos de prueba
$boleta = '8888888888';
try {
    $stmt = $pdo->prepare("
        INSERT INTO alumnos
          (boleta,nombre,apellido_paterno,apellido_materno,
           genero,curp,telefono,semestre,carrera,correo,password)
        VALUES (?,?,?,?,?,?,?,?,?,?,?)
    ");
    $stmt->execute([
        $boleta,
        'Prueba',
        'Insercion',
        'Manual',
        'Otro',
        openssl_encrypt('CURPPRUEBA','AES-256-CBC','TU_LLAVE_DE_AES',0,str_repeat("\0",16)),
        '0000000000',
        1,
        'ISC',
        'prueba@alumno.ipn.mx',
        password_hash('Prueba123!', PASSWORD_BCRYPT)
    ]);
    echo "✅ Insert OK, fila afectada: " . $stmt->rowCount();
} catch (PDOException $e) {
    echo "❌ Error en INSERT: " . $e->getMessage();
}
