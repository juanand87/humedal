<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
include 'config.php';

$message = '';
$message_type = '';

// Iconos disponibles
$available_icons = [
    'fa-check' => '✓ Check',
    'fa-file-contract' => '📄 Contrato',
    'fa-user-friends' => '👥 Usuarios',
    'fa-tint' => '💧 Gota',
    'fa-recycle' => '♻️ Reciclar',
    'fa-file-medical' => '🏥 Médico',
    'fa-file-lines' => '📋 Documento',
    'fa-box-open' => '📦 Caja',
    'fa-warehouse' => '🏭 Bodega',
    'fa-hands-helping' => '🤝 Ayuda',
    'fa-leaf' => '🍃 Hoja',
    'fa-seedling' => '🌱 Brote',
    'fa-water' => '🌊 Agua',
    'fa-shield-alt' => '🛡️ Escudo',
    'fa-cogs' => '⚙️ Engranajes',
    'fa-hard-hat' => '⛑️ Casco',
    'fa-drafting-compass' => '📐 Compás',
    'fa-ruler-combined' => '📏 Regla',
    'fa-building' => '🏢 Edificio',
    'fa-industry' => '🏭 Industria',
    'fa-award' => '🏆 Premio',
    'fa-star' => '⭐ Estrella',
    'fa-bolt' => '⚡ Rayo',
    'fa-comments' => '💬 Chat',
    'fa-phone' => '📞 Teléfono',
    'fa-envelope' => '✉️ Email',
    'fa-clock' => '🕐 Reloj',
    'fa-map-marker-alt' => '📍 Ubicación',
    'fa-handshake' => '🤝 Acuerdo',
    'fa-lightbulb' => '💡 Idea',
];

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    $fields = [
        'title', 'title_line2', 'title_highlight', 'subtitle',
        'image_url', 'link_url', 'order',
        'feature1_title', 'feature1_text', 'feature1_icon',
        'feature2_title', 'feature2_text', 'feature2_icon',
        'feature3_title', 'feature3_text', 'feature3_icon',
        'button1_text', 'button1_url', 'button1_style',
        'button2_text', 'button2_url', 'button2_style',
        'background_type'
    ];

    if ($action === 'add_slide') {
        $data = [];
        foreach ($fields as $f) {
            $data[$f] = $_POST[$f] ?? '';
        }
        $data['order'] = (int)$data['order'];

        $columns = implode(',', array_map(fn($f) => "`$f`", $fields));
        $placeholders = implode(',', array_fill(0, count($fields), '?'));
        $sql = "INSERT INTO slides ($columns) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));

        $message = 'Slide agregado exitosamente';
        $message_type = 'success';
    } elseif ($action === 'update_slide') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $data = [];
            foreach ($fields as $f) {
                $data[$f] = $_POST[$f] ?? '';
            }
            $data['order'] = (int)$data['order'];

            $set_clause = implode(',', array_map(fn($f) => "`$f` = ?", $fields));
            $sql = "UPDATE slides SET $set_clause WHERE id = ?";
            $values = array_values($data);
            $values[] = $id;
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);

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

$slides = $pdo->query('SELECT * FROM slides ORDER BY `order`, id ASC')->fetchAll(PDO::FETCH_ASSOC);
$media_files = $pdo->query("SELECT id, filename, file_path FROM media WHERE file_type LIKE 'image/%' ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Función para renderizar campos
function renderSlideFormFields($media_files, $available_icons, $prefix) {
    $pid = $prefix === 'add' ? '' : 'edit';
?>
<h6 class='section-title'><i class='fas fa-heading'></i> Títulos del Slide</h6>
<div class='row g-3'>
    <div class='col-md-4'>
        <label class='form-label'>Título Línea 1</label>
        <input type='text' name='title' id='<?php echo $pid; ?>title' class='form-control' placeholder='Expertos en tramitaciones' required>
    </div>
    <div class='col-md-4'>
        <label class='form-label'>Título Línea 2</label>
        <input type='text' name='title_line2' id='<?php echo $pid; ?>title_line2' class='form-control' placeholder='sanitarias ante'>
    </div>
    <div class='col-md-4'>
        <label class='form-label'>Palabra Destacada (verde)</label>
        <input type='text' name='title_highlight' id='<?php echo $pid; ?>title_highlight' class='form-control' placeholder='SEREMI Salud'>
    </div>
    <div class='col-12'>
        <label class='form-label'>Subtítulo</label>
        <textarea name='subtitle' id='<?php echo $pid; ?>subtitle' class='form-control' rows='2'></textarea>
    </div>
</div>

<h6 class='section-title'><i class='fas fa-image'></i> Imagen de Fondo</h6>
<div class='row g-3'>
    <div class='col-md-8'>
        <label class='form-label'>Imagen</label>
        <div class='input-group'>
            <select name='image_url' id='<?php echo $pid; ?>image_url' class='form-select'>
                <option value=''>Sin imagen (usar color sólido)</option>
                <?php foreach ($media_files as $media): ?>
                    <option value='<?php echo htmlspecialchars($media['file_path']); ?>'><?php echo htmlspecialchars($media['filename']); ?></option>
                <?php endforeach; ?>
            </select>
            <button class='btn btn-outline-secondary' type='button' onclick="openMediaModal('<?php echo $pid; ?>image_url')">
                <i class='fas fa-images'></i> Biblioteca
            </button>
        </div>
    </div>
    <div class='col-md-4'>
        <label class='form-label'>Tipo de fondo</label>
        <select name='background_type' id='<?php echo $pid; ?>background_type' class='form-select'>
            <option value='color'>Color sólido (azul)</option>
            <option value='image'>Imagen personalizada</option>
        </select>
    </div>
</div>

<h6 class='section-title'><i class='fas fa-list-check'></i> 3 Características con Iconos</h6>
<?php for ($i = 1; $i <= 3; $i++): ?>
<div class='row g-3 mb-3 pb-3' style='border-bottom: 1px solid #e9ecef;'>
    <h6 class='text-primary'>Característica <?php echo $i; ?></h6>
    <div class='col-md-4'>
        <label class='form-label'>Título</label>
        <input type='text' name='feature<?php echo $i; ?>_title' id='<?php echo $pid; ?>feature<?php echo $i; ?>_title' class='form-control' placeholder='Experiencia'>
    </div>
    <div class='col-md-4'>
        <label class='form-label'>Texto</label>
        <input type='text' name='feature<?php echo $i; ?>_text' id='<?php echo $pid; ?>feature<?php echo $i; ?>_text' class='form-control' placeholder='en proyectos sanitarios'>
    </div>
    <div class='col-md-4'>
        <label class='form-label'>Icono</label>
        <input type='hidden' name='feature<?php echo $i; ?>_icon' id='<?php echo $pid; ?>feature<?php echo $i; ?>_icon' value='fa-check'>
        <button type='button' class='btn btn-outline-primary w-100' onclick="openIconPicker(<?php echo $i; ?>, '<?php echo $pid; ?>')">
            <i class='fas fa-check' id='<?php echo $pid; ?>feature<?php echo $i; ?>_preview'></i>
            <span id='<?php echo $pid; ?>feature<?php echo $i; ?>_name'>Seleccionar</span>
        </button>
    </div>
</div>
<?php endfor; ?>

<h6 class='section-title'><i class='fas fa-hand-pointer'></i> 2 Botones de Acción</h6>
<?php for ($i = 1; $i <= 2; $i++): ?>
<div class='row g-3 mb-3 pb-3' style='border-bottom: 1px solid #e9ecef;'>
    <h6 class='text-primary'>Botón <?php echo $i; ?></h6>
    <div class='col-md-5'>
        <label class='form-label'>Texto del botón</label>
        <input type='text' name='button<?php echo $i; ?>_text' id='<?php echo $pid; ?>button<?php echo $i; ?>_text' class='form-control' placeholder='NUESTROS SERVICIOS'>
    </div>
    <div class='col-md-4'>
        <label class='form-label'>Enlace URL</label>
        <input type='text' name='button<?php echo $i; ?>_url' id='<?php echo $pid; ?>button<?php echo $i; ?>_url' class='form-control' placeholder='#servicios'>
    </div>
    <div class='col-md-3'>
        <label class='form-label'>Estilo</label>
        <select name='button<?php echo $i; ?>_style' id='<?php echo $pid; ?>button<?php echo $i; ?>_style' class='form-select'>
            <option value='success'>Verde relleno</option>
            <option value='outline-light'>Blanco con borde</option>
            <option value='outline-success'>Verde con borde</option>
            <option value='primary'>Azul relleno</option>
        </select>
    </div>
</div>
<?php endfor; ?>

<h6 class='section-title'><i class='fas fa-sort'></i> Orden y Enlace</h6>
<div class='row g-3'>
    <div class='col-md-3'>
        <label class='form-label'>Orden</label>
        <input type='number' name='order' id='<?php echo $pid; ?>order' class='form-control' value='0' min='0'>
        <small class='text-muted'>Menor número primero</small>
    </div>
    <div class='col-md-9'>
        <label class='form-label'>Enlace general (opcional)</label>
        <input type='text' name='link_url' id='<?php echo $pid; ?>link_url' class='form-control' placeholder='https://...'>
    </div>
</div>
<?php
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Gestión de Slides - Panel Administrativo</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
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
        .section-title { color: var(--primary); font-weight: 700; padding-bottom: 8px; border-bottom: 2px solid var(--accent); margin: 20px 0 15px 0; }
        .icon-picker { display: grid; grid-template-columns: repeat(auto-fill, minmax(60px, 1fr)); gap: 8px; max-height: 250px; overflow-y: auto; padding: 8px; border: 1px solid #e9ecef; border-radius: 6px; }
        .icon-option { padding: 10px; text-align: center; border: 2px solid transparent; border-radius: 6px; cursor: pointer; transition: all 0.2s; background: #f8f9fa; }
        .icon-option:hover { background: #e9ecef; }
        .icon-option.selected { background: var(--accent); color: #fff; border-color: var(--accent); }
        .slide-summary { display: flex; gap: 12px; align-items: center; padding: 12px; border: 1px solid #e9ecef; border-radius: 8px; margin-bottom: 8px; }
        .slide-summary:hover { border-color: var(--primary); background: #f8f9fa; }
        .slide-thumb { width: 80px; height: 50px; object-fit: cover; border-radius: 4px; background: var(--primary); }
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
                    <h1 class='m-0'>Gestión de Slides (Portada)</h1>
                    <a href='logout.php' class='btn btn-outline-danger'><i class='fas fa-sign-out-alt'></i> Cerrar Sesión</a>
                </div>

                <div class='content-area'>
                    <?php if ($message): ?>
                        <div class='alert alert-<?php echo $message_type; ?>'><i class='fas fa-check-circle'></i> <?php echo $message; ?></div>
                    <?php endif; ?>

                    <div class='card-custom p-4 mb-4'>
                        <h4 class='mb-3'><i class='fas fa-plus-circle'></i> Crear Nuevo Slide</h4>
                        <button class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#addSlideModal'>
                            <i class='fas fa-plus'></i> Crear Nuevo Slide
                        </button>
                    </div>

                    <div class='card-custom p-4'>
                        <h4 class='mb-3'><i class='fas fa-list'></i> Slides Existentes (<?php echo count($slides); ?>)</h4>
                        <?php if (empty($slides)): ?>
                            <div class='alert alert-info'>No hay slides creados aún.</div>
                        <?php else: ?>
                            <?php foreach ($slides as $slide): ?>
                                <div class='slide-summary'>
                                    <?php if (!empty($slide['image_url'])): ?>
                                        <img src='../<?php echo htmlspecialchars($slide['image_url']); ?>' class='slide-thumb' alt='Slide'>
                                    <?php else: ?>
                                        <div class='slide-thumb' style='display:flex; align-items:center; justify-content:center; color:#fff;'>
                                            <i class='fas fa-image'></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class='flex-grow-1'>
                                        <strong><?php echo htmlspecialchars($slide['title']); ?></strong>
                                        <?php if (!empty($slide['title_line2'])): ?>
                                            <br><small class='text-muted'><?php echo htmlspecialchars($slide['title_line2']); ?> <span class='text-success'><?php echo htmlspecialchars($slide['title_highlight'] ?? ''); ?></span></small>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <span class='badge bg-secondary'>Orden: <?php echo (int)$slide['order']; ?></span>
                                    </div>
                                    <div>
                                        <button class='btn btn-sm btn-outline-primary' data-bs-toggle='modal' data-bs-target='#editSlideModal' onclick='loadSlide(<?php echo json_encode($slide, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                            <i class='fas fa-edit'></i> Editar
                                        </button>
                                        <form method='POST' style='display:inline-block;' onsubmit='return confirm("¿Eliminar este slide?")'>
                                            <input type='hidden' name='action' value='delete_slide'>
                                            <input type='hidden' name='id' value='<?php echo $slide['id']; ?>'>
                                            <button type='submit' class='btn btn-sm btn-outline-danger'><i class='fas fa-trash'></i></button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Agregar Slide -->
    <div class='modal fade' id='addSlideModal' tabindex='-1'>
        <div class='modal-dialog modal-xl'>
            <div class='modal-content'>
                <form method='POST'>
                    <input type='hidden' name='action' value='add_slide'>
                    <div class='modal-header'>
                        <h5 class='modal-title'><i class='fas fa-plus-circle'></i> Crear Nuevo Slide de Portada</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                    </div>
                    <div class='modal-body'>
                        <?php renderSlideFormFields($media_files, $available_icons, 'add'); ?>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
                        <button type='submit' class='btn btn-primary'><i class='fas fa-save'></i> Guardar Slide</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Slide -->
    <div class='modal fade' id='editSlideModal' tabindex='-1'>
        <div class='modal-dialog modal-xl'>
            <div class='modal-content'>
                <form method='POST'>
                    <input type='hidden' name='action' value='update_slide'>
                    <input type='hidden' name='id' id='editSlideId'>
                    <div class='modal-header'>
                        <h5 class='modal-title'><i class='fas fa-edit'></i> Editar Slide</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                    </div>
                    <div class='modal-body'>
                        <?php renderSlideFormFields($media_files, $available_icons, 'edit'); ?>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
                        <button type='submit' class='btn btn-primary'><i class='fas fa-save'></i> Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de selección de icono -->
    <div class='modal fade' id='iconModal' tabindex='-1'>
        <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title'>Seleccionar Icono</h5>
                    <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                </div>
                <div class='modal-body'>
                    <div class='icon-picker' id='iconPicker'>
                        <?php foreach ($available_icons as $class => $label): ?>
                            <div class='icon-option' data-icon='<?php echo $class; ?>' data-label='<?php echo htmlspecialchars($label); ?>' onclick='selectIcon(this)'>
                                <i class='fas <?php echo $class; ?>'></i>
                                <div class='small mt-1'><?php echo htmlspecialchars($label); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Medios -->
    <div class='modal fade' id='mediaModal' tabindex='-1' aria-hidden='true'>
        <div class='modal-dialog modal-xl modal-dialog-centered'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title'>Biblioteca de Medios</h5>
                    <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                </div>
                <div class='modal-body p-0'>
                    <iframe id='mediaIframe' src='media.php?mode=select' style='width: 100%; height: 600px; border: none;'></iframe>
                </div>
            </div>
        </div>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
    <script>
        let currentFeature = 0;
        let currentFormPrefix = '';
        const iconModal = new bootstrap.Modal(document.getElementById('iconModal'));
        const mediaModal = new bootstrap.Modal(document.getElementById('mediaModal'));

        function openIconPicker(featureNum, formPrefix) {
            currentFeature = featureNum;
            currentFormPrefix = formPrefix;
            const currentIcon = document.getElementById(formPrefix + 'feature' + featureNum + '_icon').value;
            document.querySelectorAll('.icon-option').forEach(opt => {
                opt.classList.toggle('selected', opt.dataset.icon === currentIcon);
            });
            iconModal.show();
        }

        function selectIcon(element) {
            const iconClass = element.dataset.icon;
            const iconLabel = element.dataset.label;
            document.getElementById(currentFormPrefix + 'feature' + currentFeature + '_icon').value = iconClass;
            document.getElementById(currentFormPrefix + 'feature' + currentFeature + '_preview').className = 'fas ' + iconClass;
            document.getElementById(currentFormPrefix + 'feature' + currentFeature + '_name').textContent = iconLabel;
            iconModal.hide();
        }

        let targetSelectorId = '';
        function openMediaModal(targetId) {
            targetSelectorId = targetId;
            document.getElementById('mediaIframe').src = 'media.php?mode=select&target_id=' + targetId;
            mediaModal.show();
        }

        function registerNewMedia(data) {
            if (!data || !data.file_path) return;
            addOptionToSelector(targetSelectorId, data.file_path, data.filename);
        }

        function addOptionToSelector(selectorId, value, label) {
            const selector = document.getElementById(selectorId);
            if (!selector) return;
            let exists = false;
            for (let i = 0; i < selector.options.length; i++) {
                if (selector.options[i].value === value) { exists = true; selector.selectedIndex = i; break; }
            }
            if (!exists) {
                const option = document.createElement('option');
                option.value = value;
                option.text = label || value.split('/').pop();
                option.selected = true;
                selector.add(option);
                selector.value = value;
            }
        }

        function selectImage(path) {
            const selector = document.getElementById(targetSelectorId);
            if (!selector) { mediaModal.hide(); return; }
            let exists = false;
            for (let i = 0; i < selector.options.length; i++) {
                if (selector.options[i].value === path) { exists = true; selector.selectedIndex = i; break; }
            }
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
            const fields = ['title','title_line2','title_highlight','subtitle','image_url','link_url','order',
                'feature1_title','feature1_text','feature1_icon',
                'feature2_title','feature2_text','feature2_icon',
                'feature3_title','feature3_text','feature3_icon',
                'button1_text','button1_url','button1_style',
                'button2_text','button2_url','button2_style',
                'background_type'];
            fields.forEach(f => {
                const el = document.getElementById('edit' + f);
                if (el) el.value = slide[f] || '';
            });

            for (let i = 1; i <= 3; i++) {
                const iconField = slide['feature' + i + '_icon'] || 'fa-check';
                const previewEl = document.getElementById('editfeature' + i + '_preview');
                if (previewEl) previewEl.className = 'fas ' + iconField;
            }
        }
    </script>
</body>
</html>
