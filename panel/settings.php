"<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
include 'config.php';

$message = '';
$message_type = '';

// Procesar formulario
if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_logo') {
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../assets/img/';
            $file_extension = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
            
            if (in_array($file_extension, $allowed_extensions)) {
                $new_filename = 'logo_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path)) {
                    $stmt = executeQuery('UPDATE site_settings SET logo = ? WHERE id = 1', [$new_filename]);
                    if ($stmt) {
                        $message = 'Logo actualizado exitosamente';
                        $message_type = 'success';
                    }
                } else {
                    $message = 'Error al subir el archivo';
                    $message_type = 'danger';
                }
            } else {
                $message = 'Formato de archivo no permitido';
                $message_type = 'danger';
            }
        }
    }
    
    if ($action === 'update_site') {
        $site_name = trim($_POST['site_name']);
        $site_description = trim($_POST['site_description']);
        $stmt = executeQuery('UPDATE site_settings SET site_name = ?, site_description = ? WHERE id = 1', 
                            [$site_name, $site_description]);
        if ($stmt) {
            $message = 'Configuración del sitio actualizada';
            $message_type = 'success';
        }
    }
}

// Obtener configuración actual
$settings = getSiteSettings();
?>
<!DOCTYPE html>
<html lang=\"es\">
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Configuración - Panel Administrativo</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'>
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
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 24px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-left: 4px solid var(--accent);
        }
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
        .content-area {
            padding: 32px;
        }
        .card-custom {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(1,39,75,0.08);
            margin-bottom: 24px;
        }
        .btn-primary {
            background: var(--accent);
            border-color: var(--accent);
        }
        .btn-primary:hover {
            background: #247028;
            border-color: #247028;
        }
        .alert {
            border-radius: 8px;
            border-left: 4px solid;
        }
        .alert-success {
            background: rgba(46,139,51,0.1);
            border-color: var(--accent);
            color: var(--accent);
        }
        .alert-danger {
            background: rgba(220,53,69,0.1);
            border-color: #dc3545;
            color: #dc3545;
        }
        .logo-preview {
            max-width: 200px;
            max-height: 100px;
            border: 2px dashed #e9ecef;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
        }
        .logo-preview img {
            max-width: 100%;
            max-height: 80px;
        }
        .menu-item-row {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 12px;
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .menu-item-row input {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class='container-fluid'>
        <div class='row'>
            <!-- Sidebar -->
            <div class='col-md-2 sidebar'>
                <div class='sidebar-brand'>
                    <h2><i class='fas fa-shield-alt'></i> Humedal</h2>
                </div>
                <nav class='sidebar-nav'>
                    <a href='index.php'>
                        <i class='fas fa-home'></i> Dashboard
                    </a>
                    <a href='pages.php'>
                        <i class='fas fa-file-alt'></i> Páginas
                    </a>
                    <a href='menu.php'>
                        <i class='fas fa-bars'></i> Menú
                    </a>
                    <a href='media.php'>
                        <i class='fas fa-images'></i> Medios
                    </a>
                    <a href='slides.php'>
                        <i class='fas fa-sliders-h'></i> Slides
                    </a>
                    <a href='hero.php'>
                        <i class='fas fa-image'></i> Hero (Portada)
                    </a>
                    <a href='social.php'>
                        <i class='fab fa-share-alt'></i> Redes Sociales
                    </a>
                    <a href='settings.php' class='active'>
                        <i class='fas fa-cog'></i> Configuración
                    </a>
                    <a href='/' target='_blank'>
                        <i class='fas fa-external-link-alt'></i> Ver Sitio
                    </a>
                </nav>
            </div>

            <!-- Main content -->
            <div class='col-md-10'>
                <div class='top-bar'>
                    <h1>Configuración del Sitio</h1>
                    <a href='logout.php' class='btn btn-outline-danger'>
                        <i class='fas fa-sign-out-alt'></i> Cerrar Sesión
                    </a>
                </div>

                <div class='content-area'>
                    <?php if ($message): ?>
                        <div class='alert alert-<?php echo $message_type; ?>'>
                            <i class='fas fa-check-circle'></i> <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Configuración general del sitio -->
                    <div class='card-custom'>
                        <h4 class='mb-3'><i class='fas fa-info-circle'></i> Información del Sitio</h4>
                        <form method='POST'>
                            <input type='hidden' name='action' value='update_site'>
                            <div class='mb-3'>
                                <label for='site_name' class='form-label'>Nombre del Sitio</label>
<input type='text' class='form-control' id='site_name' name='site_name' 
       value='<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>' required>
                            </div>
                            <div class='mb-3'>
                                <label for='site_description' class='form-label'>Descripción</label>
                                <textarea class='form-control' id='site_description' name='site_description' rows='3'><?php echo htmlspecialchars($settings['site_description'] ?? ''); ?></textarea>
                            </div>
                            <button type='submit' class='btn btn-primary'>
                                <i class='fas fa-save'></i> Guardar Cambios
                            </button>
                        </form>
                    </div>

                    <!-- Logo del sitio -->
                    <div class='card-custom'>
                        <h4 class='mb-3'><i class='fas fa-image'></i> Logo del Sitio</h4>
                        <div class='row'>
                            <div class='col-md-6'>
                                <div class='logo-preview mb-3'>
                                    <?php if (!empty($settings['logo'])): ?>
                                        <img src='../assets/img/<?php echo htmlspecialchars($settings['logo']); ?>' alt='Logo actual'>
                                    <?php else: ?>
                                        <p class='text-muted'>Sin logo</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class='col-md-6'>
                                <form method='POST' enctype='multipart/form-data'>
                                    <input type='hidden' name='action' value='update_logo'>
                                    <div class='mb-3'>
                                        <label for='logo' class='form-label'>Subir Nuevo Logo</label>
<input type='file' class='form-control' id='logo' name='logo' 
       accept='image/*' required>
                                        <small class='text-muted'>Formatos permitidos: JPG, PNG, GIF, SVG</small>
                                    </div>
                                    <button type='submit' class='btn btn-primary'>
                                        <i class='fas fa-upload'></i> Subir Logo
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>"