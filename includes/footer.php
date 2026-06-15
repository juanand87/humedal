<?php
// Incluir configuración si no está incluida
if (!function_exists('getSiteSettings')) {
    include_once __DIR__ . '/../panel/config.php';
}
$settings = getSiteSettings();
$menu_items = json_decode($settings['menu_items'] ?? '[]', true);
$social_links = json_decode($settings['social_links'] ?? '[]', true);
$logo = !empty($settings['logo']) ? 'assets/img/' . $settings['logo'] : 'assets/img/logo.svg';

$platforms_icons = [
    'facebook' => 'fab fa-facebook',
    'instagram' => 'fab fa-instagram',
    'twitter' => 'fab fa-twitter',
    'linkedin' => 'fab fa-linkedin',
    'youtube' => 'fab fa-youtube',
    'whatsapp' => 'fab fa-whatsapp',
];
?>

  <footer class="site-footer bg-dark text-light mt-5">
    <!-- Nueva fila de 4 columnas -->
    <div class="container-fluid bg-light text-dark py-4">
      <div class="container">
        <div class="row">
          <!-- Columna 1: Logo -->
          <div class="col-md-3 text-center mb-3 mb-md-0">
            <img src="<?php echo $logo; ?>" alt="Logo Humedal" height="48" />
          </div>
          
          <!-- Columna 2: Menú Principal -->
          <div class="col-md-3 mb-3 mb-md-0">
            <h6>Enlaces</h6>
            <ul class="list-unstyled">
              <?php if (!empty($menu_items)): ?>
                  <?php foreach ($menu_items as $item): ?>
                      <li><a href="<?php echo htmlspecialchars($item['url']); ?>" class="text-decoration-none text-dark"><?php echo htmlspecialchars($item['label']); ?></a></li>
                  <?php endforeach; ?>
              <?php else: ?>
                  <li>No hay enlaces</li>
              <?php endif; ?>
            </ul>
          </div>
          
          <!-- Columna 3: Servicios Rápidos -->
          <div class="col-md-3 mb-3 mb-md-0">
            <h6>Servicios</h6>
            <ul class="list-unstyled">
                <li><a href="#servicios" class="text-decoration-none text-dark">Tramitaciones</a></li>
                <li><a href="#servicios" class="text-decoration-none text-dark">Proyectos Sanitarios</a></li>
                <li><a href="#servicios" class="text-decoration-none text-dark">Alcantarillado</a></li>
            </ul>
          </div>
          
          <!-- Columna 4: Redes Sociales -->
          <div class="col-md-3">
            <h6>Redes Sociales</h6>
            <div class="d-flex gap-3">
              <?php if (!empty($social_links)): ?>
                  <?php foreach ($social_links as $link): ?>
                      <?php if (!empty($link['platform']) && !empty($link['url'])): ?>
                          <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank" class="text-dark fs-4">
                              <i class="<?php echo $platforms_icons[$link['platform']] ?? 'fas fa-link'; ?>"></i>
                          </a>
                      <?php endif; ?>
                  <?php endforeach; ?>
              <?php else: ?>
                  <span class="text-muted small">No configuradas</span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Fin nueva fila -->
    
    <div class="container py-4">
      <div class="row">
        <div class="col-md-6">
          <h5>HUMEDAL</h5>
          <p>Ingeniería y Gestión Sanitaria. Todos los derechos reservados.</p>
        </div>
        <div class="col-md-6 text-md-end">
          <p>Contacto: <a href="mailto:contacto@humedal.cl" class="link-light">contacto@humedal.cl</a> · +56 9 1234 5678</p>
        </div>
      </div>
    </div>
  </footer>

  <!-- Floating WhatsApp Button -->
  <a href="https://wa.me/56955858896" class="whatsapp-float" target="_blank">
    <i class="fab fa-whatsapp"></i>
  </a>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Carrusel de servicios: mueve el track por el ancho visible (aprox. 5 cards)
    document.addEventListener('DOMContentLoaded', function(){
      const body = document.body;

      body.classList.add('page-loaded');

      document.querySelectorAll('.services-carousel').forEach(function(carousel){
        const track = carousel.querySelector('.carousel-track');
        const prev = carousel.querySelector('.carousel-prev');
        const next = carousel.querySelector('.carousel-next');

        if(!track) return;

        prev.addEventListener('click', function(){
          track.scrollBy({left: -track.clientWidth, behavior:'smooth'});
        });
        next.addEventListener('click', function(){
          track.scrollBy({left: track.clientWidth, behavior:'smooth'});
        });
      });

      document.querySelectorAll('a[href]').forEach(function(link){
        const href = (link.getAttribute('href') || '').trim();

        if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript:') || link.target === '_blank') {
          return;
        }

        link.addEventListener('click', function(){
          if (href.indexOf('://') === -1 || href.startsWith(window.location.origin)) {
            body.classList.remove('page-loaded');
            body.classList.add('page-exiting');
          }
        });
      });
    });
  </script>
</body>
</html>
