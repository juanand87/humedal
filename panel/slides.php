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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_slide') {
        $title = trim($_POST['title'] ?? '');
        $subtitle = trim($_POST['subtitle'] ?? '');
        $image_url = trim($_POST['image_url'] ?? '');
        $link_url = trim($_POST['link_url'] ?? '');
        $order = (int)($_POST['order'] ?? 0);
        
        if ($title) {
            $stmt = $pdo->prepare('INSERT INTO slides (title, subtitle, image_url, link_url, `order`) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$title, $subtitle, $image_url, $link_url, $order]);
            $message = 'Slide agregado exitosamente';
            $message_type = 'success';
        }
    } elseif ($action === 'update_slide') {
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $subtitle = trim($_POST['subtitle'] ?? '');
        $image_url = trim($_POST['image_url'] ?? '');
        $link_url = trim($_POST['link_url'] ?? '');
        $order = (int)($_POST['order'] ?? 0);
        
        if ($id && $title) {
            $stmt = $pdo->prepare('UPDATE slides SET title = ?, subtitle = ?, image_url = ?, link_url = ?, `order` = ? WHERE id = ?');
            $stmt->execute([$title, $subtitle, $image_url, $link_url, $order, $id]);
            $message = 'Slide actualizado exitosamente';
            $message_type = 'success';
        }
    } elseif ($action === 'delete_slide') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $stmt = $pdo->prepare('DELETE FROM slides WHERE id = ?');
            $stmt->execute([$id]);
            $message = 'Slide eliminado exitosamente';
            $message_type = 'success';
        }
    }
}

// Obtener slides
$slides = $pdo->query('SELECT * FROM slides ORDER BY `order`, id ASC')->fetchAll(PDO::FETCH_ASSOC);

// Obtener imágenes disponibles desde la biblioteca de medios
$media_files = $pdo->query('SELECT id, filename, file_path FROM media WHERE file_type LIKE "image/%" ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Gestión de Slides - Panel Administrativo</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'>
    <style>
        :root { --primary: #01274b; --accent: #2e8b33; }
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; background: #f5f7fa; }
        .sidebar { background: var(--primary); min-height: 100vh; }
        .sidebar-brand { padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); text-align: center; }
        .sidebar-brand h2 { color: #fff; font-size: 1.25rem; font-weight: 700; margin: 0; }
        .sidebar-nav a { display: flex; align-items: center; gap: 12px; padding: 14px 24px; color: rgba(255,255,255,0.8); text-decoration: none; transition: all 0.3s ease; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background: rgba(255,255,255,0.1); color: #fff; border-left: 4px solid var(--accent); }
        .top-bar { background: #fff; }
        .content-area { padding: 24px; }
        .card-custom { background: #fff; border-radius: 12px; box-shadow: 0 4px 16px rgba(1,39,75,0.08); }
        .slide-item { border: 1px solid #e9ecef; border-radius: 8px; padding: 16px; margin-bottom: 16px; }
        .slide-item:hover { border-color: var(--primary); }
        .slide-preview { width: 120px; height: 80px; object-fit: cover; border-radius: 6px; }
        .image-selector { max-height: 200px; overflow-y: auto; border: 1px solid #e9ecef; padding: 8px; border-radius: 6px; }
        .image-thumb { width: 80px; height: 60px; object-fit: cover; border-radius: 4px; cursor: pointer; margin: 4px; }
        .image-thumb:hover { border: 2px solid var(--primary); }
    </style>
</head>
<body>
    <div class='container-fluid'>
        <div class='row'>
            <div class='col-md-2 sidebar'>
                <div class='sidebar-brand'>
                    <h2><i class='fas fa-shield-alt'></i> Humedal</h2>
                </div>
                <nav class='sidebar-nav'>
                    <a href='index.php'><i class='fas fa-home'></i> Dashboard</a>
                    <a href='pages.php'><i class='fas fa-file-alt'></i> Páginas</a>
                    <a href='menu.php'><i class='fas fa-bars'></i> Menú</a>
                    <a href='media.php'><i class='fas fa-images'></i> Medios</a>
                    <a href='slides.php' class='active'><i class='fas fa-sliders-h'></i> Slides</a>
                    <a href='social.php'><i class='fab fa-share-alt'></i> Redes Sociales</a>
                    <a href='settings.php'><i class='fas fa-cog'></i> Configuración</a>
                    <a href='/' target='_blank'><i class='fas fa-external-link-alt'></i> Ver Sitio</a>
                </nav>
            </div>

            <div class='col-md-10'>
                <div class='top-bar p-3 d-flex justify-content-between align-items-center'>
                    <h1 class='m-0'>Gestión de Slides</h1>
                    <a href='logout.php' class='btn btn-outline-danger'><i class='fas fa-sign-out-alt'></i> Cerrar Sesión</a>
                </div>

                <div class='content-area'>
                    <?php if ($message): ?>
                        <div class='alert alert-<?php echo $message_type; ?>'><i class='fas fa-check-circle'></i> <?php echo $message; ?></div>
                    <?php endif; ?>

                    <div class='card-custom p-4'>
                        <h4 class='mb-4'><i class='fas fa-plus-circle'></i> Agregar Slide</h4>
                        <form method='POST' class='row g-3'>
                            <input type='hidden' name='action' value='add_slide'>
                            <div class='col-md-6'>
                                <label class='form-label'>Título</label>
                                <input type='text' name='title' class='form-control' placeholder='Ej: Expertos en tramitaciones' required>
                            </div>
                            <div class='col-md-6'>
                                <label class='form-label'>Subtítulo</label>
                                <input type='text' name='subtitle' class='form-control' placeholder='Ej: Asesoría técnica integral'>
                            </div>
                            <div class='col-md-6'>
                                <label class='form-label'>Imagen</label>
                                <div class="input-group">
                                    <select name='image_url' id='newSlideImage' class='form-select'>
                                        <option value=''>Seleccionar imagen...</option>
                                        <?php foreach ($media_files as $media): ?>
                                            <option value='<?php echo htmlspecialchars($media['file_path']); ?>'>
                                                <?php echo htmlspecialchars($media['filename']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-outline-secondary" type="button" onclick="openMediaModal('newSlideImage')">
                                        <i class="fas fa-images"></i> Biblioteca
                                    </button>
                                </div>
                                <small class='text-muted d-block mt-2'>
                                    Selecciona de la lista o abre la biblioteca para subir una nueva.
                                </small>
                            </div>
                            <div class='col-md-6'>
                                <label class='form-label'>Enlace (opcional)</label>
                                <input type='text' name='link_url' class='form-control' placeholder='Ej: #servicios o https://...'>
                            </div>
                            <div class='col-md-4'>
                                <label class='form-label'>Orden</label>
                                <input type='number' name='order' class='form-control' value='0' min='0'>
                            </div>
                            <div class='col-md-12'>
                                <button type='submit' class='btn btn-primary'><i class='fas fa-save'></i> Guardar Slide</button>
                            </div>
                        </form>

                        <hr>

                        <h4 class='mb-3'><i class='fas fa-list'></i> Slides Existentes</h4>
                        <?php if (empty($slides)): ?>
                            <div class='alert alert-info'>No hay slides creados aún.</div>
                        <?php else: ?>
                            <div class='table-responsive'>
                                <table class='table table-hover'>
                                    <thead>
                                        <tr>
                                            <th>Orden</th>
                                            <th>Título</th>
                                            <th>Imagen</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($slides as $slide): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($slide['order']); ?></td>
                                                <td><?php echo htmlspecialchars($slide['title']); ?></td>
                                                <td><?php echo htmlspecialchars(basename($slide['image_url'] ?? 'N/A')); ?></td>
                                                <td>
                                                    <button class='btn btn-sm btn-outline-primary' data-bs-toggle='modal' data-bs-target='#editSlideModal' onclick='loadSlide(<?php echo json_encode($slide); ?>)'><i class='fas fa-edit'></i> Editar</button>
                                                    <form method='POST' style='display:inline-block;' onsubmit='return confirm("¿Eliminar este slide?")'>
                                                        <input type='hidden' name='action' value='delete_slide'>
                                                        <input type='hidden' name='id' value='<?php echo $slide['id']; ?>'>
                                                        <button type='submit' class='btn btn-sm btn-outline-danger'><i class='fas fa-trash'></i> Eliminar</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar slide -->
    <div class='modal fade' id='editSlideModal' tabindex='-1' aria-labelledby='editSlideModalLabel' aria-hidden='true'>
        <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
                <form method='POST'>
                    <input type='hidden' name='action' value='update_slide'>
                    <input type='hidden' name='id' id='editSlideId'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='editSlideModalLabel'><i class='fas fa-edit'></i> Editar Slide</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body'>
                        <div class='row g-3'>
                            <div class='col-md-6'>
                                <label class='form-label'>Título</label>
                                <input type='text' name='title' id='editSlideTitle' class='form-control' required>
                            </div>
                            <div class='col-md-6'>
                                <label class='form-label'>Subtítulo</label>
                                <input type='text' name='subtitle' id='editSlideSubtitle' class='form-control'>
                            </div>
                            <div class='col-md-6'>
                                <label class='form-label'>Imagen</label>
                                <div class="input-group">
                                    <select name='image_url' id='editSlideImage' class='form-select'>
                                        <option value=''>Seleccionar imagen...</option>
                                        <?php foreach ($media_files as $media): ?>
                                            <option value='<?php echo htmlspecialchars($media['file_path']); ?>'>
                                                <?php echo htmlspecialchars($media['filename']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-outline-secondary" type="button" onclick="openMediaModal('editSlideImage')">
                                        <i class="fas fa-images"></i> Biblioteca
                                    </button>
                                </div>
                            </div>
                            <div class='col-md-6'>
                                <label class='form-label'>Enlace (opcional)</label>
                                <input type='text' name='link_url' id='editSlideLink' class='form-control'>
                            </div>
                            <div class='col-md-4'>
                                <label class='form-label'>Orden</label>
                                <input type='number' name='order' id='editSlideOrder' class='form-control' min='0'>
                            </div>
                        </div>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
                        <button type='submit' class='btn btn-primary'><i class='fas fa-save'></i> Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class='modal fade' id='mediaModal' tabindex='-1' aria-hidden='true'>
        <div class='modal-dialog modal-xl modal-dialog-centered'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title'>Biblioteca de Medios</h5>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <div class='modal-body p-0'>
                    <iframe id='mediaIframe' src='media.php?mode=select' style='width: 100%; height: 600px; border: none;'></iframe>
                </div>
            </div>
        </div>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
    <script>
        let targetSelectorId = '';
        const mediaModal = new bootstrap.Modal(document.getElementById('mediaModal'));

        function openMediaModal(targetId) {
            targetSelectorId = targetId;
            // Recargar iframe para ver nuevos archivos
            document.getElementById('mediaIframe').src = 'media.php?mode=select';
            mediaModal.show();
        }

        function selectImage(path) {
            const selector = document.getElementById(targetSelectorId);
            
            // Verificar si la opción ya existe en el select
            let exists = false;
            for (let i = 0; i < selector.options.length; i++) {
                if (selector.options[i].value === path) {
                    exists = true;
                    selector.selectedIndex = i;
                    break;
                }
            }

            // Si no existe, crearla y seleccionarla
            if (!exists) {
                const option = document.createElement('option');
                option.value = path;
                option.text = path.split('/').pop();
                selector.add(option);
                selector.value = path;
            }

            mediaModal.hide();
        }

        function loadSlide(slide) {
            document.getElementById('editSlideId').value = slide.id;
            document.getElementById('editSlideTitle').value = slide.title;
            document.getElementById('editSlideSubtitle').value = slide.subtitle || '';
            document.getElementById('editSlideImage').value = slide.image_url || '';
            document.getElementById('editSlideLink').value = slide.link_url || '';
            document.getElementById('editSlideOrder').value = slide.order || 0;
        }
    </script>
</body>
</html>
