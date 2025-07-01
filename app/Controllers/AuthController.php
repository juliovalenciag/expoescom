<?php
namespace App\Controllers;

class AuthController
{
    protected $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    // ---------------- Landing ----------------

    public function showLanding()
    {
        include __DIR__ . '/../Views/landing.php';
    }

    // ---------------- Registro ----------------

    public function showRegister()
    {
        // Si el participante ya está logueado, no mostrar registro
        if (!empty($_SESSION['alumno_boleta'])) {
            header('Location: ' . BASE_PATH . '/participante');
            exit;
        }

        $stmt = $this->pdo->query("SELECT id, nombre FROM academias ORDER BY nombre");
        $academias = $stmt->fetchAll();

        // defino cuáles son Matutino (por ejemplo id 3,4,6,10) y el resto Vespertino
        $idsMatutino = [3, 4, 6, 10];
        foreach ($academias as &$a) {
            if (in_array($a['id'], $idsMatutino, true)) {
                $a['horarios'] = ['Matutino'];
            } else {
                $a['horarios'] = ['Vespertino'];
            }
        }
        unset($a);

        // traigo unidades para el JS
        $stmt = $this->pdo->query("SELECT id, nombre, academia_id FROM unidades_aprendizaje ORDER BY nombre");
        $unidades = $stmt->fetchAll();

        // agrupo unidades por academia
        $unidadesPorAcademia = [];
        foreach ($unidades as $u) {
            $unidadesPorAcademia[$u['academia_id']][] = [
                'id' => $u['id'],
                'nombre' => $u['nombre'],
            ];
        }

        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        include __DIR__ . '/../Views/auth/register.php';
    }

    // ------------- Login Participante -------------

    public function showLoginParticipante()
    {
        // Si ya hay sesión de participante, redirigir al panel
        if (!empty($_SESSION['alumno_boleta'])) {
            header('Location: ' . BASE_PATH . '/participante');
            exit;
        }

        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        include __DIR__ . '/../Views/auth/login_participante.php';
    }

    public function loginParticipante()
    {
        $identifier = trim($_POST['identifier'] ?? '');
        $pass = $_POST['password'] ?? '';

        $stmt = $this->pdo->prepare("
            SELECT boleta, password
              FROM alumnos
             WHERE boleta = ? OR correo = ?
        ");
        $stmt->execute([$identifier, $identifier]);
        $alumno = $stmt->fetch();

        if ($alumno && password_verify($pass, $alumno['password'])) {
            $_SESSION['alumno_boleta'] = $alumno['boleta'];
            header('Location: /expoescom/participante');
            exit;
        }

        $_SESSION['errors'] = ['Boleta/Correo o contraseña incorrectos'];
        $_SESSION['old'] = ['identifier' => $identifier];
        header('Location: ' . BASE_PATH . '/login/participante');
        exit;
    }

    // ------------- Login Administrador -------------

    /**
     * GET /login/admin
     */
    public function showLoginAdmin()
    {
        // Si ya está logueado como admin, redirige
        if (!empty($_SESSION['is_admin'])) {
            header('Location: ' . BASE_PATH . '/admin');
            exit;
        }

        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        include __DIR__ . '/../Views/auth/login_admin.php';
    }

    /**
     * POST /login/admin
     */
    public function loginAdmin()
    {
        $usuario = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';
        $errors = [];
        $old = ['usuario' => $usuario];

        // Validaciones server-side
        if ($usuario === '' || $password === '') {
            $errors[] = 'Ambos campos son obligatorios.';
        }
        if (!preg_match('/^[a-zA-Z0-9_]{4,30}$/', $usuario)) {
            $errors[] = 'Usuario inválido.';
        }

        if (empty($errors)) {
            // Buscar al admin
            $stmt = $this->pdo->prepare("SELECT id, password FROM administradores WHERE usuario = ?");
            $stmt->execute([$usuario]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                session_regenerate_id(true);
                $_SESSION['is_admin'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                header('Location:' . BASE_PATH . '/admin');
                exit;
            } else {
                $errors[] = 'Usuario o contraseña incorrectos.';
            }
        }

        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = $old;
        header('Location:' . BASE_PATH . '/login/admin');
        exit;
    }

    public function logout()
    {
        // si no está arrancada, la iniciamos
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // limpia todo lo de sesión
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            // destruye la cookie de sesión
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();

        // redirige al landing
        header('Location: ' . BASE_PATH . '/');
        exit;
    }
}
