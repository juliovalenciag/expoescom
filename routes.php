<?php
use App\Controllers\AuthController;
use App\Controllers\AlumnoController;
use App\Controllers\AdminController;
use App\Controllers\PDFController;
use App\Controllers\UnidadController;
use App\Controllers\AdminParticipantesController;

require_once __DIR__ . '/config/app.php';
class Dispatcher
{
    public static function dispatch(string $route)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $method = $_SERVER['REQUEST_METHOD'];

        // 1) Landing
        if ($route === '/' && $method === 'GET') {
            return (new AuthController)->showLanding();
        }

        // 2) Registro participante
        if ($route === '/register') {
            return $method === 'GET'
                ? (new AuthController)->showRegister()
                : (new AlumnoController)->register();
        }

        // 3) Login participante
        if ($route === '/login/participante') {
            return $method === 'GET'
                ? (new AuthController)->showLoginParticipante()
                : (new AuthController)->loginParticipante();
        }

        if ($route === '/participante' && $method === 'GET') {
            return (new AlumnoController)->dashboard();
        }
        // 4) Login admin
        if ($route === '/login/admin') {
            return $method === 'GET'
                ? (new AuthController)->showLoginAdmin()
                : (new AuthController)->loginAdmin();
        }

        // 5) Logout (ambos)
        if ($route === '/logout' && $method === 'GET') {
            return (new AuthController)->logout();
        }

        // Desde aquí, sólo admin
        if (str_starts_with($route, '/admin') && empty($_SESSION['is_admin'])) {
            header('Location: ' . BASE_PATH . '/login/admin');
            exit;
        }

        // 6) Panel admin
        if ($route === '/admin' && $method === 'GET') {
            return (new AdminController)->dashboard();

        }

        if ($route === '/admin/participantes' && $method === 'GET') {
            return (new AdminParticipantesController)->index();
        }

        // 6.2) Marcar ganador
        if (preg_match('#^/admin/participantes/ganador/([\w\d]+)$#', $route, $m) && $method === 'GET') {
            return (new AdminParticipantesController)->marcarGanador($m[1]);
        }

        // // 7) Listado de participantes
        // if ($route === '/admin/participantes' && $method === 'GET') {
        //     return (new AdminController)->list();
        // }

        // 8) API participantes
        if ($route === '/admin/api/participantes' && $method === 'POST') {
            return (new AdminParticipantesController)->apiCreate();
        }
        if (preg_match('#^/admin/api/participantes/([\w\d]+)$#', $route, $m) && $method === 'PUT') {
            return (new AdminParticipantesController)->apiUpdate($m[1]);
        }
        if (preg_match('#^/admin/api/participantes/([\w\d]+)$#', $route, $m) && $method === 'DELETE') {
            return (new AdminParticipantesController)->apiDelete($m[1]);
        }
        if (preg_match('#^/admin/api/participantes/([\w\d]+)/ganador$#', $route, $m) && $method === 'POST') {
            return (new AdminParticipantesController)->marcarGanador($m[1]);
        }


        // 9) Asignar salón
        if (preg_match('#^/admin/asignar/(\d+)$#', $route, $m) && $method === 'GET') {
            return (new AdminController)->asignar((int) $m[1]);
        }

        // 10) PDF
        if (preg_match('#^/pdf/acuse/([\w\d]+)$#', $route, $m)) {
            return (new PDFController)->acuse($m[1]);
        }
        if (preg_match('#^/pdf/diploma/([\w\d]+)$#', $route, $m)) {
            return (new PDFController)->diploma($m[1]);
        }

        // 11) API unidades
        if ($route === '/api/unidades' && $method === 'GET') {
            return (new UnidadController)->getByAcademia();
        }

        // 404
        header("HTTP/1.0 404 Not Found");
        echo "Página no encontrada";
    }
}
