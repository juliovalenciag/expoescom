<?php
// index.php (colócalo en la raíz de tu proyecto, p.ej. htdocs/expoescom/index.php)

// 0) Mostrar errores para depurar (retirar en producción)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1) Definir ruta base de la aplicación
require __DIR__ . '/config/app.php'; 

// 2) Arrancar sesión una sola vez por petición
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3) Conexión a la base de datos
require __DIR__ . '/config/database.php';

// 4) Autocarga de clases en app/Controllers y app/Models bajo namespace App\
spl_autoload_register(function ($class) {
    $prefix  = 'App\\';
    $baseDir = __DIR__ . '/app/';

    // Solo cargamos clases de nuestro namespace
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    // Convertir el namespace en ruta de fichero
    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// 5) Cargar el Dispatcher (tus rutas)
require __DIR__ . '/routes.php';

// 6) Resolver la ruta actual (sin el prefijo BASE_PATH)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (strpos($uri, BASE_PATH) === 0) {
    $route = substr($uri, strlen(BASE_PATH));
} else {
    $route = $uri;
}
// Asegurarnos de que haya siempre una barra inicial
$route = $route === '' ? '/' : $route;

// 7) Despachar la petición
Dispatcher::dispatch($route);
