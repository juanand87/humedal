<?php
// Incluir configuración si no está incluida
if (!function_exists('getSiteSettings')) {
    include_once __DIR__ . '/../panel/config.php';
}
$settings = getSiteSettings();
$menu_items = normalizeMenuItems($settings['menu_items'] ?? '[]');
$social_links = json_decode($settings['social_links'] ?? '[]', true);
$header_menu = normalizeMenuItems($settings['header_menu'] ?? '[]');
$header_menu_1 = normalizeMenuItems($settings['header_menu_1'] ?? '[]');
$header_menu_2 = normalizeMenuItems($settings['header_menu_2'] ?? '[]');

// Fallback: use header_menu_1 if header_menu is empty
if (empty($header_menu)) {
    $header_menu = $header_menu_1;
}
$logo = !empty($settings['logo']) ? 'assets/img/' . $settings['logo'] : 'assets/img/logo.svg';

$whatsapp_link = '#contacto'; // Valor por defecto
if (!empty($social_links)) {
    foreach ($social_links as $link) {
        if ($link['platform'] === 'whatsapp' && !empty($link['url'])) {
            $whatsapp_link = $link['url'];
            break;
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($settings['site_name'] ?? 'Humedal'); ?> - Ingeniería y Gestión Sanitaria</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
  <header class="site-header">
    <!-- Nuevo Menú Header -->
    <?php if (!empty($header_menu)): ?>
    <div class="bg-light py-2">
      <div class="container">
        <ul class="nav justify-content-center">
          <?php foreach ($header_menu as $item): ?>
              <li class="nav-item">
                  <a class="nav-link" href="<?php echo htmlspecialchars($item['url']); ?>">
                      <?php echo htmlspecialchars($item['label']); ?>
                  </a>
              </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
    <?php endif; ?>
    <!-- Fin Nuevo Menú Header -->
    
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
      <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="/">
          <img src="<?php echo $logo; ?>" alt="Humedal" height="48" />
          <span class="ms-2 brand-text"><?php echo htmlspecialchars($settings['site_name'] ?? 'HUMEDAL'); ?></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
          <ul class="navbar-nav mx-auto">
            <?php if (!empty($menu_items)): ?>
                <?php foreach ($menu_items as $item): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo htmlspecialchars($item['url']); ?>">
                            <?php echo htmlspecialchars($item['label']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="#servicios">Servicios</a></li>
                <li class="nav-item"><a class="nav-link" href="#nosotros">Nosotros</a></li>
                <li class="nav-item"><a class="nav-link" href="#proyectos">Proyectos</a></li>
                <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
            <?php endif; ?>
          </ul>
          <a class="btn btn-success ms-3" href="<?php echo htmlspecialchars($whatsapp_link); ?>" target="_blank">
              <i class="fab fa-whatsapp"></i> Escríbenos
          </a>
        </div>
      </div>
    </nav>
  </header>
</body>
</html>