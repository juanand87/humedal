<?php
session_start();

$message = '';
$message_type = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message_text = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $subject === '' || $message_text === '') {
        $message = 'Por favor completa todos los campos obligatorios.';
        $message_type = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Ingresa un correo válido.';
        $message_type = 'danger';
    } else {
        $to = 'contacto@humedalingeniria.cl';
        $email_subject = 'Nuevo mensaje de contacto: ' . $subject;
        $email_body = "Nombre: $name\n";
        $email_body .= "Correo: $email\n";
        $email_body .= "Teléfono: " . ($phone ?: 'No proporcionado') . "\n\n";
        $email_body .= "Mensaje:\n$message_text\n";

        $headers = [
            'From: no-reply@humedal.test',
            'Reply-To: ' . $email,
            'X-Mailer: PHP/' . phpversion(),
            'Content-Type: text/plain; charset=UTF-8'
        ];

        $sent = mail($to, $email_subject, $email_body, implode("\r\n", $headers));

        if ($sent) {
            $message = 'Tu mensaje se envió correctamente. Nos contactaremos a la brevedad.';
            $message_type = 'success';
        } else {
            $message = 'No se pudo enviar el mensaje en este momento. Puedes escribirnos por WhatsApp o al correo indicado.';
            $message_type = 'warning';
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

<main class="py-5">
  <section class="contact-strip-white">
    <div class="container">
      <div class="row g-4 align-items-stretch">
        <div class="col-lg-7">
          <div class="contact-cta-group h-100">
            <span class="badge-label">Contacto</span>
            <h1 class="contact-title">Escríbenos y te responderemos con gusto</h1>
            <p class="contact-subtitle">Completa el formulario y cuéntanos cómo podemos ayudarte. También puedes comunicarte directamente por WhatsApp si prefieres una respuesta rápida.</p>

            <?php if ($message): ?>
              <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>" role="alert">
                <?php echo htmlspecialchars($message); ?>
              </div>
            <?php endif; ?>

            <form method="post" class="needs-validation" novalidate>
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="name" class="form-label">Nombre completo</label>
                  <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="col-md-6">
                  <label for="email" class="form-label">Correo electrónico</label>
                  <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="col-md-6">
                  <label for="phone" class="form-label">Teléfono (opcional)</label>
                  <input type="text" class="form-control" id="phone" name="phone" placeholder="Ej. +56 9 1234 5678">
                </div>
                <div class="col-md-6">
                  <label for="subject" class="form-label">Asunto</label>
                  <input type="text" class="form-control" id="subject" name="subject" required>
                </div>
                <div class="col-12">
                  <label for="message" class="form-label">Tu mensaje</label>
                  <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-paper-plane me-2"></i> Enviar mensaje
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="contact-cta-group h-100">
            <h2 class="cta-header">También puedes contactarnos por</h2>
            <p class="cta-text">Elige la opción que te resulte más cómoda para recibir una respuesta rápida.</p>

            <div class="contact-buttons">
              <a class="contact-btn whatsapp-btn" href="https://wa.me/56955858896" target="_blank" rel="noopener">
                <i class="fab fa-whatsapp"></i>
                <span class="btn-content">
                  <span class="btn-label">WhatsApp</span>
                  <span class="btn-value">+56 9 5585 8896</span>
                </span>
              </a>
              <a class="contact-btn email-btn" href="mailto:contacto@humedalingeniria.cl">
                <i class="fas fa-envelope"></i>
                <span class="btn-content">
                  <span class="btn-label">Correo</span>
                  <span class="btn-value">contacto@humedalingeniria.cl</span>
                </span>
              </a>
            </div>

            <div class="mt-4 p-3 rounded bg-light border">
              <h5 class="mb-2"><i class="fas fa-clock me-2 text-success"></i> Atención</h5>
              <p class="mb-0 text-muted">Responderemos tus consultas en el menor tiempo posible. Si lo prefieres, puedes escribirnos directamente por WhatsApp para una atención inmediata.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
