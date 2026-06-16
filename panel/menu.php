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
    
    if ($action === 'update_menu') {
        // Obtener datos actuales
        $settings = getSiteSettings();
        
        // Determinar qué pestaña está activa
        $activeTab = $_POST['active_tab'] ?? 'main-menu';
        
        // Helper: reindexar array secuencialmente para evitar colisiones de índices
        $reindex = function($arr) {
            if (!is_array($arr)) return [];
            return array_values(array_filter($arr, function($item) {
                return !empty($item['label']) || !empty($item['url']);
            }));
        };
        
        // Procesar según la pestaña activa
        if ($activeTab === 'main-menu') {
            $menu_items = json_encode($reindex($_POST['menu_items'] ?? []));
            $header_menu = $settings['header_menu'];
            $header_menu_1 = $settings['header_menu_1'];
            $header_menu_2 = $settings['header_menu_2'];
        } elseif ($activeTab === 'header-menu') {
            $menu_items = $settings['menu_items'];
            $header_menu = json_encode($reindex($_POST['header_menu'] ?? []));
            $header_menu_1 = $settings['header_menu_1'];
            $header_menu_2 = $settings['header_menu_2'];
        } elseif ($activeTab === 'header-menu1') {
            $menu_items = $settings['menu_items'];
            $header_menu = $settings['header_menu'];
            $header_menu_1 = json_encode($reindex($_POST['header_menu_1'] ?? []));
            $header_menu_2 = $settings['header_menu_2'];
        } elseif ($activeTab === 'header-menu2') {
            $menu_items = $settings['menu_items'];
            $header_menu = $settings['header_menu'];
            $header_menu_1 = $settings['header_menu_1'];
            $header_menu_2 = json_encode($reindex($_POST['header_menu_2'] ?? []));
        } else {
            // Si es social-links o cualquier otra cosa, mantener todo
            $menu_items = $settings['menu_items'];
            $header_menu = $settings['header_menu'];
            $header_menu_1 = $settings['header_menu_1'];
            $header_menu_2 = $settings['header_menu_2'];
        }
        
        $social_links_html = $_POST['social_links_html'] ?? '';
        
        $stmt = executeQuery(
            'UPDATE site_settings SET 
             menu_items = ?, 
             header_menu = ?, 
             header_menu_1 = ?, 
             header_menu_2 = ?, 
             social_links_html = ? 
             WHERE id = 1', 
            [$menu_items, $header_menu, $header_menu_1, $header_menu_2, $social_links_html]
        );
        if ($stmt) {
            $message = 'Menús actualizados exitosamente';
            $message_type = 'success';
        }
    }
}

// Obtener configuración actual
$settings = getSiteSettings();
$menu_items = normalizeMenuItems($settings['menu_items'] ?? '[]');
$header_menu = normalizeMenuItems($settings['header_menu'] ?? '[]');
$header_menu_1 = normalizeMenuItems($settings['header_menu_1'] ?? '[]');
$header_menu_2 = normalizeMenuItems($settings['header_menu_2'] ?? '[]');
$social_links_html = $settings['social_links_html'] ?? '';

// Obtener páginas disponibles
$pages = executeQuery("SELECT id, title, slug FROM pages ORDER BY title ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Gestión de Menú - Panel Administrativo</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'>
    <script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
    <script src='https://code.jquery.com/ui/1.13.2/jquery-ui.min.js'></script>
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
        .menu-item-row {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: flex;
            gap: 15px;
            align-items: center;
            border: 1px solid #dee2e6;
        }
        .menu-item-row .handle {
            cursor: move;
            color: #ccc;
        }
        .menu-item-row input {
            flex: 2;
        }
        .menu-item-row select {
            flex: 1;
        }
        .ui-state-highlight {
            background: #e9ecef;
            border: 2px dashed #dee2e6;
            min-height: 60px;
        }
        .is-invalid {
            border-color: #dc3545 !important;
        }
        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
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
                    <a href='menu.php' class='active'>
                        <i class='fas fa-bars'></i> Menú
                    </a>
                    <a href='media.php'>
                        <i class='fas fa-images'></i> Medios
                    </a>
                    <a href='slides.php'>
                        <i class='fas fa-sliders-h'></i> Slides
                    </a>
                    <a href='social.php'>
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
                    <h1>Gestión del Menú</h1>
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
                        <form method='POST' id='menuForm'>
                            <input type='hidden' name='action' value='update_menu'>
                            <input type='hidden' name='active_tab' id='activeTab' value='main-menu'>

                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs mb-3" id="menuTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="main-menu-tab" data-bs-toggle="tab" data-bs-target="#main-menu" type="button" role="tab" aria-controls="main-menu" aria-selected="true">Menú Principal</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="header-menu-tab" data-bs-toggle="tab" data-bs-target="#header-menu" type="button" role="tab" aria-controls="header-menu" aria-selected="false">Menú Header</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="header-menu1-tab" data-bs-toggle="tab" data-bs-target="#header-menu1" type="button" role="tab" aria-controls="header-menu1" aria-selected="false">Header 1</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="header-menu2-tab" data-bs-toggle="tab" data-bs-target="#header-menu2" type="button" role="tab" aria-controls="header-menu2" aria-selected="false">Header 2</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="social-links-tab" data-bs-toggle="tab" data-bs-target="#social-links" type="button" role="tab" aria-controls="social-links" aria-selected="false">Redes Sociales</button>
                                </li>
                            </ul>

                            <!-- Tab content -->
                            <div class="tab-content" id="menuTabsContent">
                                <!-- Menú Principal Tab Pane -->
                                <div class="tab-pane fade show active" id="main-menu" role="tabpanel" aria-labelledby="main-menu-tab">
                                    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                                        <h4 class='m-0'><i class='fas fa-bars'></i> Estructura del Menú Principal</h4>
                                        <div class="btn-group">
                                            <button type='button' class='btn btn-outline-primary btn-sm' onclick='addMenuItemToContainer("menuItems", "menuIndex", "menu_items")'>
                                                <i class='fas fa-plus'></i> Enlace Personalizado
                                            </button>
                                            <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#pagesModal" onclick="currentMenuContainer='menuItems'; currentMenuIndexRef='menuIndex'; currentMenuNamePrefix='menu_items';">
                                                <i class="fas fa-file-alt"></i> Agregar desde Páginas
                                            </button>
                                        </div>
                                    </div>
                                    <div id='menuItems' class='menuItems'>
                                        <?php if (!empty($menu_items)): ?>
                                            <?php foreach ($menu_items as $index => $item): ?>
                                                <div class='menu-item-row' data-id='<?php echo uniqid('menu_'); ?>'>
                                                    <div class="handle"><i class="fas fa-grip-vertical"></i></div>
                                                    <input type='text' name='menu_items[<?php echo $index; ?>][label]' 
                                                           class='form-control' placeholder='Texto del menú' 
                                                           value='<?php echo htmlspecialchars($item['label'] ?? ''); ?>' required>
                                                    <input type='text' name='menu_items[<?php echo $index; ?>][url]' 
                                                           class='form-control' placeholder='URL (ej: #servicios o page.php?slug=contacto)' 
                                                           value='<?php echo htmlspecialchars($item['url'] ?? ''); ?>' required>
                                                    <button type='button' class='btn btn-danger' onclick='this.parentElement.remove()'>
                                                        <i class='fas fa-trash'></i>
                                                    </button>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Menú Header Tab Pane -->
                                <div class="tab-pane fade" id="header-menu" role="tabpanel" aria-labelledby="header-menu-tab">
                                    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                                        <h4 class='m-0'><i class='fas fa-layer-group'></i> Menú Header</h4>
                                        <div class="btn-group">
                                            <button type='button' class='btn btn-outline-primary btn-sm' onclick='addMenuItemToContainer("headerMenu", "headerMenuIndex", "header_menu")'>
                                                <i class='fas fa-plus'></i> Enlace Personalizado
                                            </button>
                                            <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#pagesModal" onclick="currentMenuContainer='headerMenu'; currentMenuIndexRef='headerMenuIndex'; currentMenuNamePrefix='header_menu';">
                                                <i class="fas fa-file-alt"></i> Agregar desde Páginas
                                            </button>
                                        </div>
                                    </div>
                                    <div id='headerMenu' class='headerMenu'>
                                        <?php if (!empty($header_menu)): ?>
                                            <?php foreach ($header_menu as $index => $item): ?>
                                                <div class='menu-item-row' data-id='<?php echo uniqid('menu_'); ?>'>
                                                    <div class="handle"><i class="fas fa-grip-vertical"></i></div>
                                                    <input type='text' name='header_menu[<?php echo $index; ?>][label]' 
                                                           class='form-control' placeholder='Texto del menú' 
                                                           value='<?php echo htmlspecialchars($item['label'] ?? ''); ?>' required>
                                                    <input type='text' name='header_menu[<?php echo $index; ?>][url]' 
                                                           class='form-control' placeholder='URL (ej: #servicios o page.php?slug=contacto)' 
                                                           value='<?php echo htmlspecialchars($item['url'] ?? ''); ?>' required>
                                                    <button type='button' class='btn btn-danger' onclick='this.parentElement.remove()'>
                                                        <i class='fas fa-trash'></i>
                                                    </button>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Header 1 Tab Pane -->
                                <div class="tab-pane fade" id="header-menu1" role="tabpanel" aria-labelledby="header-menu1-tab">
                                    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                                        <h4 class='m-0'><i class='fas fa-layer-group'></i> Header 1</h4>
                                        <div class="btn-group">
                                            <button type='button' class='btn btn-outline-primary btn-sm' onclick='addMenuItemToContainer("headerMenu1", "headerMenu1Index", "header_menu_1")'>
                                                <i class='fas fa-plus'></i> Enlace Personalizado
                                            </button>
                                            <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#pagesModal" onclick="currentMenuContainer='headerMenu1'; currentMenuIndexRef='headerMenu1Index'; currentMenuNamePrefix='header_menu_1';">
                                                <i class="fas fa-file-alt"></i> Agregar desde Páginas
                                            </button>
                                        </div>
                                    </div>
                                    <div id='headerMenu1' class='headerMenu1'>
                                        <?php if (!empty($header_menu_1)): ?>
                                            <?php foreach ($header_menu_1 as $index => $item): ?>
                                                <div class='menu-item-row' data-id='<?php echo uniqid('menu_'); ?>'>
                                                    <div class="handle"><i class="fas fa-grip-vertical"></i></div>
                                                    <input type='text' name='header_menu_1[<?php echo $index; ?>][label]' 
                                                           class='form-control' placeholder='Texto del menú' 
                                                           value='<?php echo htmlspecialchars($item['label'] ?? ''); ?>' required>
                                                    <input type='text' name='header_menu_1[<?php echo $index; ?>][url]' 
                                                           class='form-control' placeholder='URL (ej: #servicios o page.php?slug=contacto)' 
                                                           value='<?php echo htmlspecialchars($item['url'] ?? ''); ?>' required>
                                                    <button type='button' class='btn btn-danger' onclick='this.parentElement.remove()'>
                                                        <i class='fas fa-trash'></i>
                                                    </button>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Header 2 Tab Pane -->
                                <div class="tab-pane fade" id="header-menu2" role="tabpanel" aria-labelledby="header-menu2-tab">
                                    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                                        <h4 class='m-0'><i class='fas fa-layer-group'></i> Header 2</h4>
                                        <div class="btn-group">
                                            <button type='button' class='btn btn-outline-primary btn-sm' onclick='addMenuItemToContainer("headerMenu2", "headerMenu2Index", "header_menu_2")'>
                                                <i class='fas fa-plus'></i> Enlace Personalizado
                                            </button>
                                            <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#pagesModal" onclick="currentMenuContainer='headerMenu2'; currentMenuIndexRef='headerMenu2Index'; currentMenuNamePrefix='header_menu_2';">
                                                <i class="fas fa-file-alt"></i> Agregar desde Páginas
                                            </button>
                                        </div>
                                    </div>
                                    <div id='headerMenu2' class='headerMenu2'>
                                        <?php if (!empty($header_menu_2)): ?>
                                            <?php foreach ($header_menu_2 as $index => $item): ?>
                                                <div class='menu-item-row' data-id='<?php echo uniqid('menu_'); ?>'>
                                                    <div class="handle"><i class="fas fa-grip-vertical"></i></div>
                                                    <input type='text' name='header_menu_2[<?php echo $index; ?>][label]' 
                                                           class='form-control' placeholder='Texto del menú' 
                                                           value='<?php echo htmlspecialchars($item['label'] ?? ''); ?>' required>
                                                    <input type='text' name='header_menu_2[<?php echo $index; ?>][url]' 
                                                           class='form-control' placeholder='URL (ej: #servicios o page.php?slug=contacto)' 
                                                           value='<?php echo htmlspecialchars($item['url'] ?? ''); ?>' required>
                                                    <button type='button' class='btn btn-danger' onclick='this.parentElement.remove()'>
                                                        <i class='fas fa-trash'></i>
                                                    </button>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Redes Sociales Tab Pane -->
                                <div class="tab-pane fade" id="social-links" role="tabpanel" aria-labelledby="social-links-tab">
                                    <div class="mt-3">
                                        <h4 class='m-0'><i class='fas fa-share-alt'></i> Redes Sociales</h4>
                                        <p class="text-muted">Ingrese el HTML para los iconos de redes sociales:</p>
                                        <textarea name='social_links_html' class='form-control' rows='4'><?php echo htmlspecialchars($social_links_html); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            <button type='submit' class='btn btn-primary btn-lg mt-3'>
                                <i class='fas fa-save'></i> Guardar Menús
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para páginas -->
    <div class="modal fade" id="pagesModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Seleccionar Páginas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="list-group">
                        <?php foreach ($pages as $page): ?>
                            <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" 
                                    onclick="addPageToMenu('<?php echo addslashes($page['title']); ?>', 'page.php?slug=<?php echo $page['slug']; ?>')">
                                <?php echo htmlspecialchars($page['title']); ?>
                                <i class="fas fa-plus text-success"></i>
                            </button>
                        <?php endforeach; ?>
                        <?php if (empty($pages)): ?>
                            <p class="text-muted text-center">No hay páginas creadas.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let menuIndex = <?php echo empty($menu_items) ? 0 : count($menu_items); ?>;
        let headerMenuIndex = <?php echo empty($header_menu) ? 0 : count($header_menu); ?>;
        let headerMenu1Index = <?php echo empty($header_menu_1) ? 0 : count($header_menu_1); ?>;
        let headerMenu2Index = <?php echo empty($header_menu_2) ? 0 : count($header_menu_2); ?>;

        // Calcular el índice máximo real en el DOM para evitar colisiones
        function getMaxIndex(containerId, namePrefix) {
            let max = 0;
            document.querySelectorAll('#' + containerId + ' input[name*="[label]"]').forEach(function(el) {
                const match = el.name.match(/\[(\d+)\]/);
                if (match) {
                    const idx = parseInt(match[1]);
                    if (idx >= max) max = idx + 1;
                }
            });
            return max;
        }
        
        let currentMenuContainer = 'menuItems'; // Default to main menu
        let currentMenuIndexRef = 'menuIndex';
        let currentMenuNamePrefix = 'menu_items';

        // Initialize jQuery UI Sortable for all menu containers
        $(document).ready(function() {
            $('.menuItems, .headerMenu, .headerMenu1, .headerMenu2').each(function() {
                $(this).sortable({
                    handle: '.handle',
                    placeholder: 'ui-state-highlight',
                    start: function(event, ui) {
                        // Store original names before sorting
                        ui.item.find('input').each(function() {
                            $(this).attr('data-original-name', $(this).attr('name'));
                        });
                    },
                    stop: function(event, ui) {
                        // Restore original names after sorting - DO NOT CHANGE NAMES
                        ui.item.find('input').each(function() {
                            const originalName = $(this).attr('data-original-name');
                            if (originalName) {
                                $(this).attr('name', originalName);
                            }
                        });
                    }
                }).disableSelection();
            });

            // Handle form submission - only validate visible fields
            $('#menuForm').on('submit', function(e) {
                let hasError = false;
                
                // Get active tab
                const activeTab = $('#activeTab').val();
                
                // Validate only visible fields
                $('.tab-pane.show input, .tab-pane.active input').each(function() {
                    const input = $(this);
                    if (input.val().trim()) {
                        // Field has content, make it required
                        input.attr('required', 'required');
                        if (!input.val().trim()) {
                            input.addClass('is-invalid');
                            hasError = true;
                        } else {
                            input.removeClass('is-invalid');
                        }
                    } else {
                        // Field is empty, remove required
                        input.removeAttr('required');
                    }
                });
                
                // Remove required from hidden fields
                $('.tab-pane:not(.show):not(.active) input').each(function() {
                    $(this).removeAttr('required');
                });
                
                if (hasError) {
                    e.preventDefault();
                    alert('Por favor completa todos los campos requeridos en las pestañas activas.');
                    return false;
                }
            });

            // Update active tab when switching tabs
            $('[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                const targetTab = $(e.target.getAttribute('data-bs-target'));
                $('#activeTab').val(targetTab.attr('id'));
            });

            // Remove is-invalid class when user starts typing
            $('.tab-pane.show input, .tab-pane.active input').on('input', function() {
                $(this).removeClass('is-invalid');
            });
        });

        function addMenuItemToContainer(containerId, currentIndexRef, namePrefix, label = '', url = '') {
            const container = document.getElementById(containerId);
            // Always use the real max index from the DOM to avoid collisions
            const newIndex = getMaxIndex(containerId, namePrefix);
            
            const newRow = document.createElement('div');
            newRow.className = 'menu-item-row';
            newRow.setAttribute('data-id', 'menu_new_' + Date.now());
            
            newRow.innerHTML = `
                <div class="handle"><i class="fas fa-grip-vertical"></i></div>
                <input type='text' name='${namePrefix}[${newIndex}][label]' 
                       class='form-control' placeholder='Texto del menú' value="${label}">
                <input type='text' name='${namePrefix}[${newIndex}][url]' 
                       class='form-control' placeholder='URL (ej: #servicios)' value="${url}">
                <button type='button' class='btn btn-danger' onclick='this.parentElement.remove()'>
                    <i class='fas fa-trash'></i>
                </button>
            `;
            container.appendChild(newRow);
        }

        function addPageToMenu(title, url) {
            addMenuItemToContainer(currentMenuContainer, currentMenuIndexRef, currentMenuNamePrefix, title, url);
            bootstrap.Modal.getInstance(document.getElementById('pagesModal')).hide();
        }
    </script>
</body>
</html>