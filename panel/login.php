<?php
session_start();
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit();
}
include 'config.php';
$error = '';
if ($_POST) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
$stmt = executeQuery("SELECT * FROM admins WHERE username = ?", [$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Stored Password: " . htmlspecialchars($user['password']) . "<br>";
echo "Stored Password Length: " . strlen($user['password']) . "<br>";
echo "Stored Password Hex: " . bin2hex($user['password']) . "<br>";
echo "Provided Password: " . htmlspecialchars($password) . "<br>";
echo "Hash to Store: $2y$10$8yMA7e6eov0a.6EbqQjKOOj59JRbBDDuNJy76kKcKA3LK5GzVU7rS<br>";
if ($user && password_verify($password, $user['password'])) {
    echo "Password verified successfully.";
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_user'] = $user;
    header('Location: index.php');
    exit();
} else {
    echo "Password verification failed.";
    $error = 'Usuario o contraseña incorrectos';
}
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Panel Administrativo | Humedal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #01274b;
            --accent: #2e8b33;
        }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, var(--primary) 0%, #0a2f5c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .login-container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(1, 39, 75, 0.3);
            overflow: hidden;
            max-width: 420px;
            width: 100%;
        }
        .login-header {
            background: var(--primary);
            padding: 32px 40px;
            text-align: center;
            position: relative;
        }
        .login-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--accent);
        }
        .login-header h1 {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 0 8px 0;
        }
        .login-header p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            margin: 0;
        }
        .login-body { padding: 40px; }
        .form-group { margin-bottom: 24px; }
        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 8px;
        }
        .input-wrapper { position: relative; }
        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        .form-control {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(46, 139, 51, 0.1);
        }
        .btn-login {
            width: 100%;
            padding: 16px;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn-login:hover {
            background: #247028;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(46, 139, 51, 0.3);
        }
        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            padding: 12px 16px;
            border-radius: 8px;
            border-left: 4px solid #dc3545;
            margin-bottom: 24px;
            font-size: 0.9rem;
        }
        .brand-footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        .brand-footer a {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .brand-footer a:hover { color: var(--accent); }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-shield-alt"></i> Panel Administrativo</h1>
            <p>Humedal - Gestión del Sitio Web</p>
        </div>
        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Ingresa tu usuario" required autofocus>
                    </div>
                        </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Ingresa tu contraseña" required>
                    </div>
                </div>
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
            </form>
        </div>
        <div class="brand-footer">
            <a href="/"><i class="fas fa-arrow-left"></i> Volver al sitio web</a>
        </div>
    </div>
</body>
</html>