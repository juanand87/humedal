<?php
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
    
    if ($action === 'update_social') {
        $social_links = json_encode($_POST['social'] ?? []);
        $stmt = executeQuery('UPDATE site_settings SET social_links = ? WHERE id = 1', [$social_links]);
        if ($stmt) {
            $message = 'Redes sociales actualizadas exitosamente';
            $message_type = 'success';
        }
    }
}

// Obtener configuración actual
$settings = getSiteSettings();
$social_links = json_decode($settings['social_links'] ?? '[]', true);

// Redes sociales predefinidas para facilitar la selección
$available_platforms = [
    'facebook' => ['name' => 'Facebook', 'icon' => 'fab fa-facebook'],
    'instagram' => ['name' => 'Instagram', 'icon' => 'fab fa-instagram'],
    'twitter' => ['name' => 'Twitter (X)', 'icon' => 'fab fa-twitter'],
    'linkedin' => ['name' => 'LinkedIn', 'icon' => 'fab fa-linkedin'],
    'youtube' => ['name' => 'YouTube', 'icon' => 'fab fa-youtube'],
    'whatsapp' => ['name' => 'WhatsApp', 'icon' => 'fab fa-whatsapp'],
];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Redes Sociales - Panel Administrativo</title>
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
        .social-item-row {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: flex;
            gap: 15px;
            align-items: center;
            border: 1px solid #dee2e6;
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
                    <a href='social.php' class='active'>
                        <i class='fab fa-share-alt'></i> Redes Sociales
                    </a>
                    <a href='settings.php'>
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
                    <h1>Redes Sociales</h1>
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

                    <div class='card-custom'>
                        <h4 class='mb-4'><i class='fab fa-share-alt'></i> Configurar Enlaces</h4>
                        
                        <form method='POST'>
                            <input type='hidden' name='action' value='update_social'>
                            <div id='socialItems'>
                                <?php if (!empty($social_links)): ?>
                                    <?php foreach ($social_links as $index => $item): ?>
                                        <div class='social-item-row'>
                                            <select name='social[<?php echo $index; ?>][platform]' class='form-select' style='width: 200px;'>
                                                <?php foreach ($available_platforms as $key => $p): ?>
                                                    <option value='<?php echo $key; ?>' <?php echo ($item['platform'] === $key) ? 'selected' : ''; ?>>
                                                        <?php echo $p['name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <input type='url' name='social[<?php echo $index; ?>][url]' 
                                                   class='form-control' placeholder='URL del perfil (https://...)' 
                                                   value='<?php echo htmlspecialchars($item['url'] ?? ''); ?>' required>
                                            <button type='button' class='btn btn-danger' onclick='this.parentElement.remove()'>
                                                <i class='fas fa-trash'></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            
                            <button type='button' class='btn btn-outline-primary mb-4' onclick='addSocialItem()'>
                                <i class='fas fa-plus'></i> Agregar Red Social
                            </button>
                            
                            <hr>
                            <button type='submit' class='btn btn-primary btn-lg'>
                                <i class='fas fa-save'></i> Guardar Redes Sociales
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let socialIndex = <?php echo empty($social_links) ? 0 : count($social_links); ?>;
        const platforms = <?php echo json_encode($available_platforms); ?>;

        function addSocialItem() {
            const container = document.getElementById('socialItems');
            const newRow = document.createElement('div');
            newRow.className = 'social-item-row';
            
            let options = '';
            for (const key in platforms) {
                options += `<option value='${key}'>${platforms[key].name}</option>`;
            }

            newRow.innerHTML = `
                <select name='social[${socialIndex}][platform]' class='form-select' style='width: 200px;'>
                    ${options}
                </select>
                <input type='url' name='social[${socialIndex}][url]' 
                       class='form-control' placeholder='URL del perfil (https://...)' required>
                <button type='button' class='btn btn-danger' onclick='this.parentElement.remove()'>
                    <i class='fas fa-trash'></i>
                </button>
            `;
            container.appendChild(newRow);
            socialIndex++;
        }
    </script>
</body>
</html>