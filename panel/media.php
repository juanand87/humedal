<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
include 'config.php';

$message = '';
$message_type = '';

// Detectar modo de operación PRIMERO (antes de cualquier lógica POST)
$mode = $_GET['mode'] ?? 'full';
$is_modal = ($mode === 'select');

// Configurar directorio de uploads
$upload_dir = __DIR__ . '/../assets/uploads/';
if (!is_dir($upload_dir)) {
    @mkdir($upload_dir, 0755, true);
}

// Procesar subida de archivos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media_file'])) {
    $is_ajax_upload = $is_modal && isset($_POST['from_modal']);

    // Si es AJAX desde modal, suprimir cualquier output no JSON
    if ($is_ajax_upload) {
        // Limpiar cualquier output buffer accidental
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();
    }

    $file = $_FILES['media_file'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];

    $response = ['success' => false, 'error' => ''];

    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño permitido por el servidor',
            UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño permitido',
            UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
            UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta directorio temporal',
            UPLOAD_ERR_CANT_WRITE => 'Error al escribir en disco',
            UPLOAD_ERR_EXTENSION => 'Subida detenida por extensión',
        ];
        $response['error'] = $error_messages[$file['error']] ?? 'Error desconocido en la subida';
    } elseif (!in_array($file['type'], $allowed_types)) {
        $response['error'] = 'Tipo de archivo no permitido: ' . htmlspecialchars($file['type']);
    } elseif ($file['size'] > 5242880) {
        $response['error'] = 'Archivo demasiado grande (máx 5MB)';
    } else {
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($file['name']));
        $target_path = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            $file_path = 'assets/uploads/' . $filename;
            try {
                $stmt = $pdo->prepare('INSERT INTO media (filename, file_path, file_type, file_size) VALUES (?, ?, ?, ?)');
                $stmt->execute([$file['name'], $file_path, $file['type'], $file['size']]);
                $new_id = $pdo->lastInsertId();
                $message = 'Archivo subido exitosamente';
                $message_type = 'success';
                $response = [
                    'success' => true,
                    'id' => $new_id,
                    'filename' => $file['name'],
                    'file_path' => $file_path,
                    'file_type' => $file['type'],
                    'file_size' => $file['size']
                ];
            } catch (Exception $e) {
                $response['error'] = 'Error al guardar en BD: ' . $e->getMessage();
            }
        } else {
            $response['error'] = 'Error al mover el archivo. Verifica permisos de la carpeta assets/uploads/';
        }
    }

    // Si es AJAX desde modal, devolver JSON y terminar
    if ($is_ajax_upload) {
        ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        echo json_encode($response);
        exit;
    }

    // Si NO es AJAX, usar el flujo normal
    if (!$response['success']) {
        $message = $response['error'];
        $message_type = 'danger';
    }
}

// Eliminar archivo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
        try {
            $stmt_sel = $pdo->prepare('SELECT file_path FROM media WHERE id = ?');
            $stmt_sel->execute([$id]);
            $media = $stmt_sel->fetch(PDO::FETCH_ASSOC);
            if ($media && file_exists(__DIR__ . '/../' . $media['file_path'])) {
                @unlink(__DIR__ . '/../' . $media['file_path']);
            }
            $pdo->prepare('DELETE FROM media WHERE id = ?')->execute([$id]);
            $message = 'Archivo eliminado';
            $message_type = 'success';
        } catch (Exception $e) {
            $message = 'Error al eliminar: ' . $e->getMessage();
            $message_type = 'danger';
        }
    }
}

// Obtener todos los archivos de media
try {
    $media_files = $pdo->query('SELECT * FROM media ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $media_files = [];
    $message = 'Error al cargar archivos: ' . $e->getMessage();
    $message_type = 'danger';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Biblioteca de Medios - Panel Administrativo</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
    <style>
        :root { --primary: #01274b; --accent: #2e8b33; }
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; background: <?php echo $is_modal ? '#fff' : '#f5f7fa'; ?>; }
        .sidebar { background: var(--primary); min-height: 100vh; }
        .sidebar-brand { padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); text-align: center; }
        .sidebar-brand h2 { color: #fff; font-size: 1.25rem; font-weight: 700; margin: 0; }
        .sidebar-nav a { display: flex; align-items: center; gap: 12px; padding: 14px 24px; color: rgba(255,255,255,0.8); text-decoration: none; transition: all 0.3s ease; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background: rgba(255,255,255,0.1); color: #fff; border-left: 4px solid var(--accent); }
        .top-bar { background: #fff; }
        .content-area { padding: <?php echo $is_modal ? '16px' : '24px'; ?>; }
        .card-custom { background: #fff; border-radius: 12px; box-shadow: <?php echo $is_modal ? 'none' : '0 4px 16px rgba(1,39,75,0.08)'; ?>; }
        .media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 12px; }
        .media-item { border: 1px solid #e9ecef; border-radius: 8px; padding: 10px; text-align: center; position: relative; cursor: pointer; transition: all 0.2s; background: #fff; }
        .media-item img { max-width: 100%; height: 100px; object-fit: cover; border-radius: 6px; margin-bottom: 8px; }
        .media-item-name { font-size: 0.8rem; word-break: break-word; margin-bottom: 5px; height: 1.2em; overflow: hidden; }
        .media-item:hover { border-color: var(--primary); transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .upload-zone { border: 2px dashed var(--primary); border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; background: rgba(1,39,75,0.02); }
        .upload-zone:hover { background: rgba(1,39,75,0.05); }
        .upload-zone.dragover { background: rgba(46,139,51,0.1); border-color: var(--accent); }
        .upload-status { margin-top: 8px; padding: 8px; border-radius: 4px; font-size: 0.85rem; }
        .upload-status.success { background: #d4edda; color: #155724; }
        .upload-status.error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class='container-fluid'>
        <div class='row'>
            <?php if (!$is_modal): ?>
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

            <div class='<?php echo $is_modal ? 'col-12' : 'col-md-10'; ?>'>
                <?php if (!$is_modal): ?>
                <div class='top-bar p-3 d-flex justify-content-between align-items-center'>
                    <h1 class='m-0'><i class='fas fa-images'></i> Biblioteca de Medios</h1>
                    <a href='logout.php' class='btn btn-outline-danger'><i class='fas fa-sign-out-alt'></i> Cerrar Sesión</a>
                </div>
                <?php endif; ?>

                <div class='content-area'>
                    <?php if ($message): ?>
                        <div class='alert alert-<?php echo $message_type; ?> small'><i class='fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>'></i> <?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>

                    <div class='card-custom <?php echo $is_modal ? '' : 'p-4'; ?>'>
                        <?php if ($is_modal): ?>
                            <h6 class='m-0 mb-3'><i class='fas fa-cloud-upload-alt'></i> Subir nueva imagen</h6>
                        <?php endif; ?>

                        <form method='POST' enctype='multipart/form-data' class="mb-4" id='uploadForm' <?php echo $is_modal ? 'data-modal-mode="1"' : ''; ?>>
                            <div class='upload-zone' id='uploadZone'>
                                <input type='file' name='media_file' id='mediaFile' class='d-none' accept='image/*,.pdf'>
                                <p class='mb-0 small'><strong>Arrastra archivos aquí o haz clic</strong></p>
                                <small class='text-muted d-block'>JPG, PNG, GIF, WebP o PDF - Máx 5MB</small>
                            </div>
                            <div id='uploadStatus'></div>
                            <?php if ($is_modal): ?>
                                <input type='hidden' name='from_modal' value='1'>
                                <button type='button' class='btn btn-primary btn-sm mt-2 w-100' id='submitBtn' style='display:none;'>
                                    <i class='fas fa-upload'></i> Subir Archivo
                                </button>
                            <?php else: ?>
                                <button type='submit' class='btn btn-primary mt-3' style='display:none;' id='submitBtn'><i class='fas fa-upload'></i> Subir Archivo</button>
                            <?php endif; ?>
                        </form>

                        <?php if (!$is_modal): ?>
                            <h5 class='mb-3 mt-4'><i class='fas fa-th-large'></i> Archivos (<?php echo count($media_files); ?>)</h5>
                        <?php else: ?>
                            <h6 class='mb-3 mt-3'><i class='fas fa-images'></i> O selecciona uno existente:</h6>
                        <?php endif; ?>

                        <?php if (empty($media_files)): ?>
                            <div class='alert alert-info small'>No hay archivos aún. Sube tu primer archivo.</div>
                        <?php else: ?>
                            <div class='media-grid'>
                                <?php foreach ($media_files as $media): ?>
                                    <div class='media-item' <?php if ($is_modal): ?>onclick='parent.selectImage("<?php echo htmlspecialchars($media['file_path']); ?>")'<?php endif; ?>>
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

                                        <?php if (!$is_modal): ?>
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
                        <?php endif; ?>
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
        const uploadForm = document.getElementById('uploadForm');
        const uploadStatus = document.getElementById('uploadStatus');
        const isModal = uploadForm && uploadForm.dataset.modalMode === '1';

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
            if (e.dataTransfer.files.length > 0) {
                mediaFile.files = e.dataTransfer.files;
                if (isModal) {
                    uploadFile();
                } else {
                    submitBtn.style.display = 'block';
                }
            }
        });
        mediaFile.addEventListener('change', () => {
            if (isModal) {
                uploadFile();
            } else {
                submitBtn.style.display = 'block';
            }
        });

        // Subida AJAX desde modal
        function uploadFile() {
            if (!mediaFile.files[0]) {
                showStatus('Selecciona un archivo primero', 'error');
                return;
            }

            const formData = new FormData(uploadForm);
            submitBtn.disabled = true;
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subiendo...';
            submitBtn.style.display = 'block';

            fetch('media.php?mode=select', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // Verificar status HTTP
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status + ' - ' + response.statusText);
                }
                return response.text();
            })
            .then(text => {
                // Intentar parsear como JSON
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Respuesta no es JSON:', text);
                    throw new Error('La respuesta del servidor no es JSON válido. Primeros 200 caracteres: ' + text.substring(0, 200));
                }

                if (data.success) {
                    showStatus('✓ Archivo subido: ' + (data.filename || ''), 'success');
                    // Llamar a la función del padre
                    if (window.parent && window.parent.registerNewMedia) {
                        window.parent.registerNewMedia(data);
                    }
                    // Limpiar
                    mediaFile.value = '';
                    // Recargar iframe después de un breve delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showStatus('✗ Error: ' + (data.error || 'Desconocido'), 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            })
            .catch(err => {
                console.error('Error en fetch:', err);
                showStatus('✗ Error: ' + err.message, 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        }

        function showStatus(msg, type) {
            if (uploadStatus) {
                uploadStatus.className = 'upload-status ' + type;
                uploadStatus.innerHTML = msg;
                uploadStatus.style.display = 'block';
            }
        }

        // En modo modal, interceptar el submit para usar AJAX
        if (isModal && submitBtn) {
            submitBtn.addEventListener('click', (e) => {
                e.preventDefault();
                uploadFile();
            });
        }

        function copyPath(path) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(path).then(() => {
                    alert('Ruta copiada: ' + path);
                });
            } else {
                alert('Ruta: ' + path);
            }
        }
    </script>
</body>
</html>
