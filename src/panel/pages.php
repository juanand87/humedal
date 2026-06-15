<?php
// Gestión de páginas
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Incluir configuración
include '../config.php';

// Manejar acciones CRUD
$action = isset($_GET['action']) ? $_GET['action'] : '';
$page_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Eliminar página
if ($action === 'delete' && $page_id > 0) {
    executeQuery("DELETE FROM pages WHERE id = ?", [$page_id]);
    header('Location: pages.php');
    exit();
}

// Obtener todas las páginas
$stmt = executeQuery("SELECT * FROM pages ORDER BY created_at DESC");
$pages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Páginas - Panel Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <span data-feather="home"></span>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="pages.php">
                                <span data-feather="file"></span>
                                Páginas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="settings.php">
                                <span data-feather="settings"></span>
                                Configuración
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Gestión de Páginas</h1>
                    <a href="create_page.php" class="btn btn-primary">Nueva Página</a>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?php if (empty($pages)): ?>
                            <div class="alert alert-info">No hay páginas creadas aún.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Título</th>
                                            <th>Slug</th>
                                            <th>Fecha de Creación</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pages as $page): ?>
                                        <tr>
                                            <td><?php echo $page['id']; ?></td>
                                            <td><?php echo htmlspecialchars($page['title']); ?></td>
                                            <td><?php echo $page['slug']; ?></td>
                                            <td><?php echo $page['created_at']; ?></td>
                                            <td>
                                                <a href="edit_page.php?id=<?php echo $page['id']; ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                                <a href="pages.php?action=delete&id=<?php echo $page['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>