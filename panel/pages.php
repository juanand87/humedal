<?php
ob_start();
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
include 'config.php';

$message = '';
$message_type = '';

// Procesar acciones
if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $slug = strtolower(str_replace(' ', '-', $title));
        
        if (!empty($title)) {
            $stmt = executeQuery("INSERT INTO pages (title, content, slug) VALUES (?, ?, ?)", 
                                [$title, $content, $slug]);
            if ($stmt) {
                $message = 'Página creada exitosamente';
                $message_type = 'success';
            }
        }
    }
    
    if ($action === 'update') {
        $id = $_POST['id'];
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $slug = strtolower(str_replace(' ', '-', $title));
        
        $stmt = executeQuery("UPDATE pages SET title = ?, content = ?, slug = ? WHERE id = ?", 
                            [$title, $content, $slug, $id]);
        if ($stmt) {
            $message = 'Página actualizada exitosamente';
            $message_type = 'success';
        }
    }
    
    if ($action === 'delete') {
        $id = $_POST['id'];
        $stmt = executeQuery("DELETE FROM pages WHERE id = ?", [$id]);
        if ($stmt) {
            $message = 'Página eliminada exitosamente';
            $message_type = 'success';
        }
    }
}

// Obtener páginas
$pages = executeQuery("SELECT * FROM pages ORDER BY created_at DESC")->fetchAll();

// Obtener página para editar
$edit_page = null;
if (isset($_GET['edit'])) {
    $edit_page = executeQuery("SELECT * FROM pages WHERE id = ?", [$_GET['edit']])->fetch();
}
$php_output = ob_get_clean();
?><!DOCTYPE html>
<html lang="es">
<?php echo $php_output; ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Páginas - Panel Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.5/tinymce.min.js" referrerpolicy="origin"></script>
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
        .table-pages {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(1,39,75,0.08);
        }
        .table-pages th {
            background: var(--primary);
            color: #fff;
            font-weight: 600;
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
        .tox-tinymce {
            border-radius: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="sidebar-brand">
                    <h2><i class="fas fa-shield-alt"></i> Humedal</h2>
                </div>
                <nav class="sidebar-nav">
                    <a href="index.php">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a href="pages.php" class="active">
                        <i class="fas fa-file-alt"></i> Páginas
                    </a>
                    <a href="menu.php">
                        <i class="fas fa-bars"></i> Menú
                    </a>
                    <a href="media.php">
                        <i class="fas fa-images"></i> Medios
                    </a>
                    <a href="slides.php">
                        <i class="fas fa-sliders-h"></i> Slides
                    </a>
                    <a href="social.php">
                        <i class="fab fa-share-alt"></i> Redes Sociales
                    </a>
                    <a href="settings.php">
                        <i class="fas fa-cog"></i> Configuración
                    </a>
                    <a href="/" target="_blank">
                        <i class="fas fa-external-link-alt"></i> Ver Sitio
                    </a>
                </nav>
            </div>

            <!-- Main content -->
            <div class="col-md-10">
                <div class="top-bar">
                    <h1>Gestión de Páginas</h1>
                    <div>
                        <a href="pages.php?action=create" class="btn btn-success me-2">
                            <i class="fas fa-plus"></i> Nueva Página
                        </a>
                        <a href="logout.php" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>

                <div class="content-area">
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $message_type; ?>">
                            <i class="fas fa-check-circle"></i> <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($edit_page || (isset($_GET['action']) && $_GET['action'] === 'create')): ?>
                    <!-- Formulario para crear/editar página -->
                    <div class="card-custom">
                        <h4 class="mb-3">
                            <?php echo $edit_page ? 'Editar Página' : 'Crear Nueva Página'; ?>
                        </h4>
                        <form method="POST">
                            <input type="hidden" name="action" value="<?php echo $edit_page ? 'update' : 'create'; ?>">
                            <?php if ($edit_page): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_page['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Título</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo $edit_page ? htmlspecialchars($edit_page['title']) : ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="content" class="form-label">Contenido</label>
                                <textarea class="form-control" id="content" name="content" rows="8"><?php echo $edit_page ? htmlspecialchars($edit_page['content']) : ''; ?></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo $edit_page ? 'Actualizar' : 'Crear'; ?> Página
                            </button>
                            
                            <?php if ($edit_page): ?>
                                <a href="pages.php" class="btn btn-secondary">Cancelar</a>
                            <?php endif; ?>
                        </form>
                    </div>
                    <?php endif; ?>

                    <!-- Lista de páginas -->
                    <div class="table-pages">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Slug</th>
                                    <th>Creado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pages)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4">No hay páginas creadas</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($pages as $page): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($page['title']); ?></td>
                                            <td><code><?php echo htmlspecialchars($page['slug']); ?></code></td>
                                            <td><?php echo date('d/m/Y', strtotime($page['created_at'])); ?></td>
                                            <td>
                                                <a href="?edit=<?php echo $page['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar esta página?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $page['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // TinyMCE self-hosted (sin API key, sin dominio registrado)
        tinymce.init({
            selector: '#content',
            height: 500,
            menubar: 'file edit view insert format tools table',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount code help',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | code help',
            content_style: 'body { font-family: Inter, system-ui, -apple-system, sans-serif; font-size: 14px; }',
            branding: false,
            promotion: false,
            // Desactivar el mensaje de "registra tu dominio" usando self-hosted
            license_key: 'gpl'
        });
    </script>
</body>
</html>"