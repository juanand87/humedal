<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
include 'config.php';

$message = '';
$message_type = '';
$upload_dir = __DIR__ . '/../assets/uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Procesar subida de archivos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media_file'])) {
    $file = $_FILES['media_file'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
    
    if (in_array($file['type'], $allowed_types) && $file['size'] <= 5242880) { // 5MB
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($file['name']));
        $target_path = $upload_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            $file_path = 'assets/uploads/' . $filename;
            $stmt = $pdo->prepare('INSERT INTO media (filename, file_path, file_type, file_size) VALUES (?, ?, ?, ?)');
            $stmt->execute([$file['name'], $file_path, $file['type'], $file['size']]);
            $message = 'Archivo subido exitosamente';
            $message_type = 'success';
        } else {
            $message = 'Error al mover el archivo';
            $message_type = 'danger';
        }
    } else {
        $message = 'Archivo no permitido o demasiado grande';
        $message_type = 'danger';
    }
}

// Eliminar archivo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
        $media = $pdo->prepare('SELECT file_path FROM media WHERE id = ?')->fetch(PDO::FETCH_ASSOC, [$id]);
        if ($media && file_exists(__DIR__ . '/../' . $media['file_path'])) {
            unlink(__DIR__ . '/../' . $media['file_path']);
        }
        $pdo->prepare('DELETE FROM media WHERE id = ?')->execute([$id]);
        $message = 'Archivo eliminado';
        $message_type = 'success';
    }
}

// Obtener todos los archivos de media
$media_files = $pdo->query('SELECT * FROM media ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);

// Detectar modo selección para modal
$mode = $_GET['mode'] ?? 'full';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Biblioteca de Medios - Panel Administrativo</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'>
    <style>
        :root { --primary: #01274b; --accent: #2e8b33; }
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; background: <?php echo $mode === 'select' ? '#fff' : '#f5f7fa'; ?>; }
        .sidebar { background: var(--primary); min-height: 100vh; }
        .sidebar-brand { padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); text-align: center; }
        .sidebar-brand h2 { color: #fff; font-size: 1.25rem; font-weight: 700; margin: 0; }
        .sidebar-nav a { display: flex; align-items: center; gap: 12px; padding: 14px 24px; color: rgba(255,255,255,0.8); text-decoration: none; transition: all 0.3s ease; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background: rgba(255,255,255,0.1); color: #fff; border-left: 4px solid var(--accent); }
        .top-bar { background: #fff; }
        .content-area { padding: <?php echo $mode === 'select' ? '0' : '24px'; ?>; }
        .card-custom { background: #fff; border-radius: 12px; box-shadow: <?php echo $mode === 'select' ? 'none' : '0 4px 16px rgba(1,39,75,0.08)'; ?>; }
        .media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 12px; }
        .media-item { border: 1px solid #e9ecef; border-radius: 8px; padding: 10px; text-align: center; position: relative; cursor: pointer; transition: all 0.2s; }
        .media-item img { max-width: 100%; height: 100px; object-fit: cover; border-radius: 6px; margin-bottom: 8px; }
        .media-item-name { font-size: 0.8rem; word-break: break-word; margin-bottom: 5px; height: 1.2em; overflow: hidden; }
        .media-item:hover { border-color: var(--primary); transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .upload-zone { border: 2px dashed var(--primary); border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; background: rgba(1,39,75,0.02); }
        .upload-zone:hover { background: rgba(1,39,75,0.05); }
        .upload-zone.dragover { background: rgba(46,139,51,0.1); border-color: var(--accent); }
    </style>
</head>
<body>
    <div class='container-fluid'>
        <div class='row'>
            <?php if ($mode !== 'select'): ?>
            <div class='col-md-2 sidebar'>
                <div class='sidebar-brand'>
                    <h2><i class='fas fa-shield-alt'></i> Humedal</h2>
                </div>
                <nav class='sidebar-nav'>
                    <a href='index.php'><i class='fas fa-home'></i> Dashboard</a>
                    <a href='pages.php'><i class='fas fa-file-alt'></i> Páginas</a>
                    <a href='menu.php'><i class='fas fa-bars'></i> Menú</a>
                    <a href='media.php' class='active'><i class='fas fa-images'></i> Medios</a>
                    <a href='slides.php'><i class='fas fa-sliders-h'></i> Slides</a>
                    <a href='social.php'><i class='fab fa-share-alt'></i> Redes Sociales</a>
                    <a href='settings.php'><i class='fas fa-cog'></i> Configuración</a>
                    <a href='/' target='_blank'><i class='fas fa-external-link-alt'></i> Ver Sitio</a>
                </nav>
            </div>
            <?php endif; ?>

            <div class='<?php echo $mode === 'select' ? 'col-12' : 'col-md-10'; ?>'>
                <?php if ($mode !== 'select'): ?>
                <div class='top-bar p-3 d-flex justify-content-between align-items-center'>
                    <h1 class='m-0'><i class='fas fa-images'></i> Biblioteca de Medios</h1>
                    <a href='logout.php' class='btn btn-outline-danger'><i class='fas fa-sign-out-alt'></i> Cerrar Sesión</a>
                </div>
                <?php endif; ?>

                <div class='content-area mt-<?php echo $mode === 'select' ? '0' : '3'; ?>'>
                    <?php if ($message): ?>
                        <div class='alert alert-<?php echo $message_type; ?> small'><i class='fas fa-check-circle'></i> <?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>

                    <div class='card-custom <?php echo $mode === 'select' ? '' : 'p-4'; ?>'>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class='m-0'><i class='fas fa-cloud-upload-alt'></i> <?php echo $mode === 'select' ? 'Subir o Seleccionar' : 'Biblioteca'; ?></h5>
                            <?php if ($mode === 'select'): ?>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.location.reload()"><i class="fas fa-sync"></i> Actualizar</button>
                            <?php endif; ?>
                        </div>

                        <form method='POST' enctype='multipart/form-data' class="mb-4">
                            <div class='upload-zone' id='uploadZone'>
                                <input type='file' name='media_file' id='mediaFile' class='d-none' accept='image/*,.pdf'>
                                <p class='mb-0 small'><strong>Arrastra archivos aquí o haz clic</strong></p>
                            </div>
                            <button type='submit' class='btn btn-primary btn-sm mt-2 w-100' style='display:none;' id='submitBtn'><i class='fas fa-upload'></i> Subir Archivo</button>
                        </form>

                        <div class='media-grid'>
                            <?php foreach ($media_files as $media): ?>
                                <div class='media-item' <?php if ($mode === 'select'): ?>onclick='parent.selectImage("<?php echo htmlspecialchars($media['file_path']); ?>")'<?php endif; ?>>
                                    <?php
                                    $is_image = in_array($media['file_type'], ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
                                    ?>
                                    <?php if ($is_image): ?>
                                        <img src='../<?php echo htmlspecialchars($media['file_path']); ?>' alt='<?php echo htmlspecialchars($media['filename']); ?>'>
                                    <?php else: ?>
                                        <div style='height:100px; display:flex; align-items:center; justify-content:center; background:#f0f0f0; border-radius:6px;'>
                                            <i class='fas fa-file-pdf' style='font-size:1.5rem; color:#dc3545;'></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class='media-item-name' title='<?php echo htmlspecialchars($media['filename']); ?>'>
                                        <?php echo htmlspecialchars(substr($media['filename'], 0, 15)); ?>
                                    </div>
                                    
                                    <?php if ($mode !== 'select'): ?>
                                    <div class='btn-group btn-group-sm' role='group'>
                                        <button type='button' class='btn btn-outline-primary' onclick='event.stopPropagation(); copyPath("<?php echo htmlspecialchars($media['file_path']); ?>")'><i class='fas fa-link'></i></button>
                                        <form method='POST' style='display:inline;' onsubmit='return confirm("¿Eliminar?")'>
                                            <input type='hidden' name='action' value='delete'>
                                            <input type='hidden' name='id' value='<?php echo $media['id']; ?>'>
                                            <button type='submit' class='btn btn-outline-danger' onclick='event.stopPropagation();'><i class='fas fa-trash'></i></button>
                                        </form>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
    <script>
        const uploadZone = document.getElementById('uploadZone');
        const mediaFile = document.getElementById('mediaFile');
        const submitBtn = document.getElementById('submitBtn');

        uploadZone.addEventListener('click', () => mediaFile.click());
        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadZone.classList.add('dragover');
        });
        uploadZone.addEventListener('dragleave', () => {
            uploadZone.classList.remove('dragover');
        });
        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('dragover');
            mediaFile.files = e.dataTransfer.files;
            submitBtn.style.display = 'block';
        });
        mediaFile.addEventListener('change', () => {
            submitBtn.style.display = 'block';
        });

        function copyPath(path) {
            navigator.clipboard.writeText(path).then(() => {
                alert('Ruta copiada: ' + path);
            });
        }
    </script>
</body>
</html>
