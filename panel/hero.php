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
    $fields = [
        'title_line1', 'title_line2', 'title_highlight', 'subtitle',
        'feature1_title', 'feature1_text', 'feature1_icon',
        'feature2_title', 'feature2_text', 'feature2_icon',
        'feature3_title', 'feature3_text', 'feature3_icon',
        'button1_text', 'button1_url', 'button1_style',
        'button2_text', 'button2_url', 'button2_style',
        'background_type', 'background_image'
    ];

    $stmt = $pdo->prepare("REPLACE INTO hero_config (field_key, field_value) VALUES (?, ?)");
    foreach ($fields as $field) {
        $value = $_POST[$field] ?? '';
        $stmt->execute([$field, $value]);
    }
    $message = 'Configuración del Hero actualizada';
    $message_type = 'success';
}

// Obtener configuración actual
$hero_config = [];
$rows = $pdo->query("SELECT field_key, field_value FROM hero_config")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
    $hero_config[$row['field_key']] = $row['field_value'];
}

// Iconos disponibles de Font Awesome
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Configuración del Hero - Panel Administrativo</title>
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
        .section-title { color: var(--primary); font-weight: 700; padding-bottom: 8px; border-bottom: 2px solid var(--accent); margin-bottom: 20px; }
        .icon-picker { display: grid; grid-template-columns: repeat(auto-fill, minmax(60px, 1fr)); gap: 8px; max-height: 200px; overflow-y: auto; padding: 8px; border: 1px solid #e9ecef; border-radius: 6px; }
        .icon-option { padding: 10px; text-align: center; border: 2px solid transparent; border-radius: 6px; cursor: pointer; transition: all 0.2s; background: #f8f9fa; }
        .icon-option:hover { background: #e9ecef; }
        .icon-option.selected { background: var(--accent); color: #fff; border-color: var(--accent); }
        .icon-option i { font-size: 1.2rem; }
        .preview-box { background: var(--primary); color: #fff; padding: 30px; border-radius: 8px; margin-top: 20px; }
        .preview-title { font-size: 1.8rem; }
        .preview-title span { color: var(--accent); }
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
                    <a href='slides.php'><i class='fas fa-sliders-h'></i> Slides</a>
                    <a href='hero.php' class='active'><i class='fas fa-image'></i> Hero (Portada)</a>
                    <a href='social.php'><i class='fab fa-share-alt'></i> Redes Sociales</a>
                    <a href='settings.php'><i class='fas fa-cog'></i> Configuración</a>
                    <a href='/' target='_blank'><i class='fas fa-external-link-alt'></i> Ver Sitio</a>
                </nav>
            </div>

            <div class='col-md-10'>
                <div class='top-bar p-3 d-flex justify-content-between align-items-center'>
                    <h1 class='m-0'>Configuración del Hero (Portada)</h1>
                    <a href='logout.php' class='btn btn-outline-danger'><i class='fas fa-sign-out-alt'></i> Cerrar Sesión</a>
                </div>

                <div class='content-area'>
                    <?php if ($message): ?>
                        <div class='alert alert-<?php echo $message_type; ?>'><i class='fas fa-check-circle'></i> <?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>

                    <form method='POST'>
                        <!-- Títulos -->
                        <div class='card-custom p-4 mb-4'>
                            <h4 class='section-title'><i class='fas fa-heading'></i> Títulos Principales</h4>
                            <div class='row g-3'>
                                <div class='col-md-4'>
                                    <label class='form-label'>Título Línea 1</label>
                                    <input type='text' name='title_line1' class='form-control' value='<?php echo htmlspecialchars($hero_config['title_line1'] ?? ''); ?>' required>
                                </div>
                                <div class='col-md-4'>
                                    <label class='form-label'>Título Línea 2</label>
                                    <input type='text' name='title_line2' class='form-control' value='<?php echo htmlspecialchars($hero_config['title_line2'] ?? ''); ?>' required>
                                </div>
                                <div class='col-md-4'>
                                    <label class='form-label'>Palabra Destacada (color verde)</label>
                                    <input type='text' name='title_highlight' class='form-control' value='<?php echo htmlspecialchars($hero_config['title_highlight'] ?? ''); ?>' required>
                                </div>
                                <div class='col-12'>
                                    <label class='form-label'>Subtítulo / Texto descriptivo</label>
                                    <textarea name='subtitle' class='form-control' rows='2' required><?php echo htmlspecialchars($hero_config['subtitle'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- 3 Características con iconos -->
                        <div class='card-custom p-4 mb-4'>
                            <h4 class='section-title'><i class='fas fa-list-check'></i> 3 Características con Iconos</h4>
                            <?php for ($i = 1; $i <= 3; $i++): ?>
                                <div class='row g-3 mb-4 pb-3' style='border-bottom: 1px solid #e9ecef;'>
                                    <h6 class='text-primary'>Característica <?php echo $i; ?></h6>
                                    <div class='col-md-4'>
                                        <label class='form-label'>Título</label>
                                        <input type='text' name='feature<?php echo $i; ?>_title' class='form-control' value='<?php echo htmlspecialchars($hero_config['feature' . $i . '_title'] ?? ''); ?>' required>
                                    </div>
                                    <div class='col-md-4'>
                                        <label class='form-label'>Texto</label>
                                        <input type='text' name='feature<?php echo $i; ?>_text' class='form-control' value='<?php echo htmlspecialchars($hero_config['feature' . $i . '_text'] ?? ''); ?>' required>
                                    </div>
                                    <div class='col-md-4'>
                                        <label class='form-label'>Icono</label>
                                        <input type='hidden' name='feature<?php echo $i; ?>_icon' id='feature<?php echo $i; ?>_icon' value='<?php echo htmlspecialchars($hero_config['feature' . $i . '_icon'] ?? ''); ?>'>
                                        <button type='button' class='btn btn-outline-primary w-100' onclick='openIconPicker(<?php echo $i; ?>)'>
                                            <i class='fas <?php echo htmlspecialchars($hero_config['feature' . $i . '_icon'] ?? 'fa-question'); ?>' id='feature<?php echo $i; ?>_preview'></i>
                                            <span id='feature<?php echo $i; ?>_name'>Seleccionar icono</span>
                                        </button>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>

                        <!-- Botones -->
                        <div class='card-custom p-4 mb-4'>
                            <h4 class='section-title'><i class='fas fa-hand-pointer'></i> 2 Botones de Acción</h4>
                            <?php for ($i = 1; $i <= 2; $i++): ?>
                                <div class='row g-3 mb-3 pb-3' style='border-bottom: 1px solid #e9ecef;'>
                                    <h6 class='text-primary'>Botón <?php echo $i; ?></h6>
                                    <div class='col-md-5'>
                                        <label class='form-label'>Texto del botón</label>
                                        <input type='text' name='button<?php echo $i; ?>_text' class='form-control' value='<?php echo htmlspecialchars($hero_config['button' . $i . '_text'] ?? ''); ?>' required>
                                    </div>
                                    <div class='col-md-4'>
                                        <label class='form-label'>Enlace URL</label>
                                        <input type='text' name='button<?php echo $i; ?>_url' class='form-control' value='<?php echo htmlspecialchars($hero_config['button' . $i . '_url'] ?? ''); ?>' required>
                                    </div>
                                    <div class='col-md-3'>
                                        <label class='form-label'>Estilo</label>
                                        <select name='button<?php echo $i; ?>_style' class='form-select'>
                                            <option value='success' <?php echo ($hero_config['button' . $i . '_style'] ?? '') === 'success' ? 'selected' : ''; ?>>Verde (relleno)</option>
                                            <option value='outline-light' <?php echo ($hero_config['button' . $i . '_style'] ?? '') === 'outline-light' ? 'selected' : ''; ?>>Blanco (borde)</option>
                                            <option value='outline-success' <?php echo ($hero_config['button' . $i . '_style'] ?? '') === 'outline-success' ? 'selected' : ''; ?>>Verde (borde)</option>
                                            <option value='primary' <?php echo ($hero_config['button' . $i . '_style'] ?? '') === 'primary' ? 'selected' : ''; ?>>Azul (relleno)</option>
                                        </select>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>

                        <!-- Fondo -->
                        <div class='card-custom p-4 mb-4'>
                            <h4 class='section-title'><i class='fas fa-palette'></i> Fondo del Hero</h4>
                            <div class='row g-3'>
                                <div class='col-md-6'>
                                    <label class='form-label'>Tipo de fondo</label>
                                    <select name='background_type' class='form-select'>
                                        <option value='color' <?php echo ($hero_config['background_type'] ?? '') === 'color' ? 'selected' : ''; ?>>Color sólido (azul corporativo)</option>
                                        <option value='image' <?php echo ($hero_config['background_type'] ?? '') === 'image' ? 'selected' : ''; ?>>Imagen personalizada</option>
                                    </select>
                                </div>
                                <div class='col-md-6'>
                                    <label class='form-label'>Imagen de fondo (ruta)</label>
                                    <input type='text' name='background_image' class='form-control' value='<?php echo htmlspecialchars($hero_config['background_image'] ?? ''); ?>' placeholder='assets/img/mi-fondo.jpg'>
                                </div>
                            </div>
                        </div>

                        <div class='d-flex gap-2 mb-4'>
                            <button type='submit' class='btn btn-primary btn-lg'><i class='fas fa-save'></i> Guardar Cambios</button>
                            <a href='/' target='_blank' class='btn btn-outline-secondary btn-lg'><i class='fas fa-eye'></i> Ver Portada</a>
                        </div>
                    </form>
                </div>
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

    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
    <script>
        let currentFeature = 0;
        const iconModal = new bootstrap.Modal(document.getElementById('iconModal'));

        function openIconPicker(featureNum) {
            currentFeature = featureNum;
            // Marcar icono actual como seleccionado
            const currentIcon = document.getElementById('feature' + featureNum + '_icon').value;
            document.querySelectorAll('.icon-option').forEach(opt => {
                opt.classList.toggle('selected', opt.dataset.icon === currentIcon);
            });
            iconModal.show();
        }

        function selectIcon(element) {
            const iconClass = element.dataset.icon;
            const iconLabel = element.dataset.label;
            const featureNum = currentFeature;

            document.getElementById('feature' + featureNum + '_icon').value = iconClass;
            document.getElementById('feature' + featureNum + '_preview').className = 'fas ' + iconClass;
            document.getElementById('feature' + featureNum + '_name').textContent = iconLabel;

            iconModal.hide();
        }
    </script>
</body>
</html>
