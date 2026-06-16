<?php
require __DIR__ . '/includes/header.php';

// Obtener slides para el slider
$slides = [];
try {
    // La conexión $pdo ya viene de includes/header.php -> panel/config.php
    if (isset($pdo)) {
        $stmt = $pdo->query('SELECT * FROM slides ORDER BY `order`, id ASC LIMIT 10');
        if ($stmt) {
            $slides = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }
    }
} catch (Exception $e) {
    // Si hay error, continuamos sin slides
    $slides = [];
}

// Obtener configuración del Hero
$hero_config = [];
try {
    if (isset($pdo)) {
        $rows = $pdo->query("SELECT field_key, field_value FROM hero_config")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $hero_config[$row['field_key']] = $row['field_value'];
        }
    }
} catch (Exception $e) {
    $hero_config = [];
}
?>

<main>
  <?php if (!empty($slides)): ?>
  <!-- Slider configurable (reemplaza al hero estático cuando hay slides) -->
  <section class="hero-slider">
    <div id="mainSlider" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <?php foreach ($slides as $index => $slide): ?>
          <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
            <div class="slider-content" style="background-image: url('<?php echo htmlspecialchars($slide['image_url'] ?? ''); ?>');">
              <div class="container">
                <div class="slider-text">
                  <h2><?php echo htmlspecialchars($slide['title']); ?></h2>
                  <?php if (!empty($slide['subtitle'])): ?>
                    <p class="lead"><?php echo htmlspecialchars($slide['subtitle']); ?></p>
                  <?php endif; ?>
                  <?php if (!empty($slide['link_url'])): ?>
                    <a href="<?php echo htmlspecialchars($slide['link_url']); ?>" class="btn btn-success btn-lg">VER MÁS</a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#mainSlider" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#mainSlider" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Siguiente</span>
      </button>
    </div>
  </section>
  <?php else: ?>
  <!-- Hero configurable desde el panel admin -->
  <section class="hero" <?php if (($hero_config['background_type'] ?? '') === 'image' && !empty($hero_config['background_image'])): ?>style="background-image: url('<?php echo htmlspecialchars($hero_config['background_image']); ?>'); background-size: cover; background-position: center;"<?php endif; ?>>
    <div class="container hero-inner">
      <div class="hero-left">
        <h1>
          <?php echo htmlspecialchars($hero_config['title_line1'] ?? 'Expertos en tramitaciones'); ?><br/>
          <?php echo htmlspecialchars($hero_config['title_line2'] ?? 'sanitarias ante'); ?> <span><?php echo htmlspecialchars($hero_config['title_highlight'] ?? 'SEREMI Salud'); ?></span>
        </h1>
        <p class="hero-sub"><?php echo htmlspecialchars($hero_config['subtitle'] ?? ''); ?></p>

        <div class="hero-features">
          <?php for ($i = 1; $i <= 3; $i++): ?>
          <div class="feature">
            <div class="feature-icon"><i class="fa-solid <?php echo htmlspecialchars($hero_config['feature' . $i . '_icon'] ?? 'fa-check'); ?>"></i></div>
            <div class="feature-text">
              <strong><?php echo htmlspecialchars($hero_config['feature' . $i . '_title'] ?? ''); ?></strong>
              <div class="small"><?php echo htmlspecialchars($hero_config['feature' . $i . '_text'] ?? ''); ?></div>
            </div>
          </div>
          <?php endfor; ?>
        </div>

        <div class="hero-ctas mt-3">
          <a href="<?php echo htmlspecialchars($hero_config['button1_url'] ?? '#servicios'); ?>" class="btn btn-<?php echo htmlspecialchars($hero_config['button1_style'] ?? 'success'); ?> btn-lg"><?php echo htmlspecialchars($hero_config['button1_text'] ?? ''); ?></a>
          <a href="<?php echo htmlspecialchars($hero_config['button2_url'] ?? '#contacto'); ?>" class="btn btn-<?php echo htmlspecialchars($hero_config['button2_style'] ?? 'outline-light'); ?> btn-lg"><?php echo htmlspecialchars($hero_config['button2_text'] ?? ''); ?></a>
        </div>
      </div>
      <div class="hero-right" aria-hidden="true"></div>
    </div>
  </section>
  <?php endif; ?>
  
  
  


  

  <section id="servicios" class="services">
    <div class="container">
      <h2>Soluciones sanitarias integrales</h2>
      <p class="lead">Gestionamos y desarrollamos proyectos sanitarios cumpliendo con la normativa vigente.</p>
      <div class="services-carousel">
        <button class="carousel-btn carousel-prev" aria-label="Anterior">&lsaquo;</button>
        <div class="carousel-track services-grid">
        <div class="card service-card text-center p-3">
          <div class="service-icon"><i class="fa-solid fa-file-medical fa-2x"></i></div>
          <h5>Resoluciones Sanitarias</h5>
          <p class="small text-muted">Obtención, renovación y ampliación.</p>
        </div>

        <div class="card service-card text-center p-3">
          <div class="service-icon"><i class="fa-solid fa-file-lines fa-2x"></i></div>
          <h5>Aprobación de Proyectos Sanitarios</h5>
          <p class="small text-muted">Memorias, planos y especificaciones técnicas.</p>
        </div>

        <div class="card service-card text-center p-3">
          <div class="service-icon"><i class="fa-solid fa-recycle fa-2x"></i></div>
          <h5>Plantas de Compostaje</h5>
          <p class="small text-muted">Diseño y tramitación para resolución sanitaria.</p>
        </div>

        <div class="card service-card text-center p-3">
          <div class="service-icon"><i class="fa-solid fa-box-open fa-2x"></i></div>
          <h5>Centros de Acopio / Puntos Limpios</h5>
          <p class="small text-muted">Proyectos y permisos sanitarios.</p>
        </div>

        <div class="card service-card text-center p-3">
          <div class="service-icon"><i class="fa-solid fa-tint fa-2x"></i></div>
          <h5>Alcantarillado Particular</h5>
          <p class="small text-muted">Diseño, cálculo y tramitación.</p>
        </div>

        <div class="card service-card text-center p-3">
          <div class="service-icon"><i class="fa-solid fa-warehouse fa-2x"></i></div>
          <h5>Bodegas y Recintos Productivos</h5>
          <p class="small text-muted">Tramitaciones y adecuaciones sanitarias.</p>
        </div>

        <div class="card service-card text-center p-3">
          <div class="service-icon"><i class="fa-solid fa-file-lines fa-2x"></i></div>
          <h5>Informes y Respuestas a Observaciones</h5>
          <p class="small text-muted">Elaboración de antecedentes técnicos y legales.</p>
        </div>

        <div class="card service-card text-center p-3">
          <div class="service-icon"><i class="fa-solid fa-hands-helping fa-2x"></i></div>
          <h5>Asesoría y Gestión Integral</h5>
          <p class="small text-muted">Acompañamiento en todo el proceso.</p>
        </div>

        </div>
        <button class="carousel-btn carousel-next" aria-label="Siguiente">&rsaquo;</button>
      </div>
    </div>

    <!-- Residuos: No peligrosos / Peligrosos -->
    <div class="container mt-4">
      <div class="residues-row d-flex justify-content-center gap-3">
        <div class="residue-card green d-flex align-items-center gap-3 p-3">
          <div class="residue-icon"><i class="fa-solid fa-recycle fa-2x"></i></div>
          <div>
            <strong>Residuos No Peligrosos</strong>
            <div class="small">Gestión integral de residuos sólidos no peligrosos: manejo, almacenamiento, transporte y disposición final.</div>
          </div>
        </div>

        <div class="residue-card red d-flex align-items-center gap-3 p-3">
          <div class="residue-icon"><i class="fa-solid fa-skull-crossbones fa-2x"></i></div>
          <div>
            <strong>Residuos Peligrosos</strong>
            <div class="small">Gestión integral de residuos peligrosos: manejo, almacenamiento, transporte y disposición final.</div>
          </div>
        </div>
      </div>
    </div>
  </section>
  
    <section class="process-strip">
      <div class="container d-flex gap-4 align-items-start">
        <div class="process-steps flex-grow-1">
          <h4 class="text-center text-white mb-3">Nuestro proceso — Así trabajamos</h4>
          <div class="steps-row d-flex align-items-center justify-content-between">
            <div class="step-item text-center">
              <div class="step-circle">1</div>
              <div class="step-icon"><i class="fa-solid fa-comment-dots"></i></div>
              <div class="step-title">Diagnóstico</div>
              <div class="small text-muted-light">Analizamos tu proyecto y requisitos aplicables.</div>
            </div>
            <div class="step-item text-center">
              <div class="step-circle">2</div>
              <div class="step-icon"><i class="fa-solid fa-file-lines"></i></div>
              <div class="step-title">Desarrollo</div>
              <div class="small text-muted-light">Elaboramos antecedentes técnicos y planos.</div>
            </div>
            <div class="step-item text-center">
              <div class="step-circle">3</div>
              <div class="step-icon"><i class="fa-solid fa-paper-plane"></i></div>
              <div class="step-title">Tramitación</div>
              <div class="small text-muted-light">Ingresamos y gestionamos ante SEREMI Salud.</div>
            </div>
            <div class="step-item text-center">
              <div class="step-circle">4</div>
              <div class="step-icon"><i class="fa-solid fa-square-check"></i></div>
              <div class="step-title">Seguimiento</div>
              <div class="small text-muted-light">Realizamos seguimiento y respondemos observaciones.</div>
            </div>
            <div class="step-item text-center">
              <div class="step-circle">5</div>
              <div class="step-icon"><i class="fa-solid fa-award"></i></div>
              <div class="step-title">Aprobación</div>
              <div class="small text-muted-light">Obtenemos la resolución sanitaria para tu proyecto.</div>
            </div>
          </div>
        </div>

        <div class="process-right">
          <h5 class="text-white">Compromiso y experiencia</h5>
          <ul class="process-bullets">
            <li>Amplia experiencia en proyectos sanitarios y ambientales.</li>
            <li>Conocimiento profundo de la normativa sanitaria vigente.</li>
            <li>Equipo profesional multidisciplinario.</li>
            <li>Acompañamiento personalizado y comunicación constante.</li>
          </ul>
        </div>
      </div>
    </section>

  <section id="contacto" class="contact-cta">
    <div class="container">
      <h4>¿Tienes un proyecto? Te ayudamos a hacerlo realidad.</h4>
      <a href="mailto:contacto@humedal.cl" class="btn btn-success">Escríbenos</a>
    </div>
  </section>

    <!-- Franja blanca con contacto rápido -->
    <section class="contact-strip-white">
    <div class="container">
      <div class="row align-items-stretch py-4">
        <!-- Cuadro de contacto a la izquierda -->
        <div class="col-lg-4 col-md-5 order-md-1">
          <div class="contact-cta-group">
            <h5 class="cta-header">Contáctanos hoy</h5>
            <p class="cta-text">Recibe asesoría inicial sin compromiso</p>
            
            <div class="contact-buttons">
              <a class="contact-btn whatsapp-btn" href="https://wa.me/5692131232" target="_blank" rel="noopener">
                <i class="fa-brands fa-whatsapp"></i>
                <div class="btn-content">
                  <span class="btn-label">WhatsApp</span>
                  <span class="btn-value">+56 9 2131 232</span>
                </div>
              </a>

              <a class="contact-btn email-btn" href="mailto:contacto@humedalingeniria.cl">
                <i class="fa-solid fa-envelope"></i>
                <div class="btn-content">
                  <span class="btn-label">Email</span>
                  <span class="btn-value">contacto@humedalingeniria.cl</span>
                </div>
              </a>
            </div>
          </div>
        </div>

        <!-- Información y aliados a la derecha -->
        <div class="col-lg-8 col-md-7 order-md-2">
          <div class="partners-section">
            <span class="badge-label">¿CON QUIÉN TRABAJAMOS?</span>
            <h3 class="contact-title">Aliados en el desarrollo sostenible</h3>
            <p class="contact-subtitle">Trabajamos con organizaciones comprometidas con el cumplimiento normativo y la sustentabilidad.</p>
            
            <div class="partners-grid">
              <div class="partner-item">
                <div class="partner-icon">
                  <i class="fa-solid fa-landmark"></i>
                </div>
                <span class="partner-label">Municipalidades</span>
              </div>
              
              <div class="partner-item">
                <div class="partner-icon">
                  <i class="fa-solid fa-building"></i>
                </div>
                <span class="partner-label">Empresas</span>
              </div>
              
              <div class="partner-item">
                <div class="partner-icon">
                  <i class="fa-solid fa-compass-drafting"></i>
                </div>
                <span class="partner-label">Oficinas de<br>Arquitectura</span>
              </div>
              
              <div class="partner-item">
                <div class="partner-icon">
                  <i class="fa-solid fa-helmet-safety"></i>
                </div>
                <span class="partner-label">Empresas<br>Constructoras</span>
              </div>
              
              <div class="partner-item">
                <div class="partner-icon">
                  <i class="fa-solid fa-handshake"></i>
                </div>
                <span class="partner-label">Organizaciones y<br>Emprendedores</span>
              </div>
            </div>
          </div>
        </div>
    </section>

</main>

<?php
require __DIR__ . '/includes/footer.php';
?>
