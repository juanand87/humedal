<?php
// Panel de administración - Dashboard
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

include 'config.php';
$site_settings = getSiteSettings();

// Obtener estadísticas
$stmt = executeQuery("SELECT COUNT(*) as count FROM pages");
$pages_count = $stmt->fetch()['count'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - Humedal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #01274b;
            --accent: #2e8b33;
        }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #f5f7fa;
        }
        .sidebar {
            background: var(--primary);
            min-height: 100vh;
            padding: 0;
        }
        .sidebar-brand {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        .sidebar-brand h2 {
            color: #fff;
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
        }
        .sidebar-brand small {
            color: rgba(255,255,255,0.6);
            font-size: 0.75rem;
        }
        .sidebar-nav { padding: 20px 0; }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 24px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-left: 4px solid var(--accent);
        }
        .sidebar-nav a i { width: 20px; text-align: center; }
        .main-content { padding: 0; }
        .top-bar {
            background: #fff;
            padding: 16px 32px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .top-bar h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
        }
        .btn-logout {
            background: transparent;
            border: 2px solid #dc3545;
            color: #dc3545;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-logout:hover { background: #dc3545; color: #fff; }
        .content-area { padding: 32px; }
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(1,39,75,0.08);
            border-left: 4px solid var(--accent);
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(1,39,75,0.12);
        }
        .stat-card h3 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
        }
        .stat-card p { color: #6c757d; margin: 0; font-size: 0.9rem; }
        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(46,139,51,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            font-size: 1.25rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <div class="sidebar-brand">
                    <h2><i class="fas fa-shield-alt"></i> Humedal</h2>
                    <small>Panel Administrativo</small>
                </div>
                <nav class='sidebar-nav'>
                    <a href="index.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
                    <a href="pages.php"><i class="fas fa-file-alt"></i> Páginas</a>
                    <a href="menu.php"><i class="fas fa-bars"></i> Menú</a>
                    <a href="media.php"><i class="fas fa-images"></i> Medios</a>
                    <a href="slides.php"><i class="fas fa-sliders-h"></i> Slides</a>
                    <a href="hero.php"><i class="fas fa-image"></i> Hero (Portada)</a>
                    <a href="social.php"><i class="fab fa-share-alt"></i> Redes Sociales</a>
                    <a href="settings.php"><i class="fas fa-cog"></i> Configuración</a>
                    <a href="/" target="_blank"><i class="fas fa-external-link-alt"></i> Ver Sitio</a>
                </nav>
            </div>
            <div class="col-md-10 main-content">
                <div class="top-bar">
                    <h1>Dashboard</h1>
                    <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                </div>
                <div class="content-area">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3><?php echo $pages_count; ?></h3>
                                        <p>Páginas Creadas</p>
                                    </div>
                                    <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3>1</h3>
                                        <p>Administradores</p>
                                    </div>
                                    <div class="stat-icon"><i class="fas fa-user-shield"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3><i class="fas fa-check-circle" style="color: var(--accent);"></i></h3>
                                        <p>Sitio Activo</p>
                                    </div>
                                    <div class="stat-icon"><i class="fas fa-globe"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Bienvenido al Panel Administrativo</h5>
                                    <p class="card-text">Desde aquí puedes gestionar el contenido de tu sitio web.</p>
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <a href="pages.php" class="btn btn-primary w-100"><i class="fas fa-file-alt"></i> Gestionar Páginas</a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="settings.php" class="btn btn-success w-100"><i class="fas fa-cog"></i> Configurar Sitio</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>