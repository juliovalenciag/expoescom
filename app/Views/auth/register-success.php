<?php
// Variables necesarias: $boleta
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Registro exitoso · ExpoESCOM</title>
    <link rel="icon" href="/expoescom/assets/images/favicon.ico">
    <!-- FontAwesome / Nunito / tu CSS de login -->
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600&display=swap" rel="stylesheet" />
    <style>
        /* Copia aquí tu CSS de login, o importa registro.css si quieres */
        :root {
            --primary: #004085;
            --primary-light: #0056b3;
            --bg: #eef3f9;
            --white: #fff;
            --error: #dc3545;
            --radius: 10px;
            --transition: .3s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        body.login-page {
            background: var(--bg);
            font-family: "Nunito", sans-serif;
            color: #333;
            padding-top: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 60px);
        }

        .site-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: var(--white);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }

        .site-header a {
            text-decoration: none;
            color: var(--primary)
        }

        .site-header img {
            height: 50px
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background: var(--white);
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .login-title {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: var(--primary);
        }

        .login-message {
            margin: 1rem 0;
            font-size: 1rem;
        }

        .btn-download {
            display: inline-block;
            margin-top: 1rem;
            padding: .75rem 1.5rem;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: var(--radius);
            text-decoration: none;
            font-weight: 600;
            transition: background var(--transition);
        }

        .btn-download:hover {
            background: var(--primary-light);
        }
    </style>
</head>

<body class="login-page">
    <header class="site-header">
        <a href="/expoescom/"><i class="fa-solid fa-arrow-left"></i> Inicio</a>
        <div>
            <a href="https://www.ipn.mx" target="_blank"><img src="/expoescom/assets/images/IPN_Logo.png" alt="IPN"></a>
            <a href="https://www.escom.ipn.mx" target="_blank"><img src="/expoescom/assets/images/Escom_Logo.png"
                    alt="ESCOM"></a>
        </div>
    </header>

    <div class="login-card">
        <h1 class="login-title">¡Registro exitoso!</h1>
        <p class="login-message">
            Tu acuse de registro se descargará automáticamente.
            Si no, pulsa el botón de abajo.
        </p>
        <a id="manualDownload" href="/expoescom/pdf/acuse/<?= htmlspecialchars($boleta) ?>" class="btn-download">
            <i class="fa-solid fa-file-pdf"></i> Descargar Acuse
        </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const boleta = <?= json_encode($boleta) ?>;
            // Forzar descarga ocultando un iframe
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = `/expoescom/pdf/acuse/${boleta}`;
            document.body.appendChild(iframe);

            // Después de 3 segundos redirigir al login
            setTimeout(() => {
                window.location.href = '/expoescom/login/participante';
            }, 3000);
        });
    </script>
</body>

</html>