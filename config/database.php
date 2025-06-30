<?php
// config/database.php
// Configuración de la base de datos para ExpoESCOM 2025
$host    = '127.0.0.1';
$dbname  = 'expoescom';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

// DSN y opciones PDO
$dsn     = "mysql:host={$host};dbname={$dbname};charset={$charset}";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    /**
     * Clase de depuración: muestra cada SQL antes de ejecutarlo.
     * La declaración de tipo de retorno en prepare()
     * evita la advertencia de PHP 8.1+.
     */
    class PDODebug extends PDO
    {
        public function prepare(string $query, array $options = []): \PDOStatement|false
        {
            // Descomenta la siguiente línea para ver cada consulta SQL:
            // echo "<pre style='background:#fee;padding:8px;'>SQL> {$query}</pre>";
            return parent::prepare($query, $options);
        }
    }

    // Sustituye PDO por PDODebug para habilitar el debug de consultas
    $pdo = new PDODebug($dsn, $user, $pass, $options);

} catch (PDOException $e) {
    // Si falla, mostramos el error (solo en desarrollo)
    die('Error de conexión a la base de datos: ' . $e->getMessage());
}
