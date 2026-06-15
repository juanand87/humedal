# Humedal - Plantilla de portada

Breve plantilla de la portada del sitio creada en PHP (compatible con hosting tipo GoDaddy).

Archivos creados:
- `index.php` — página principal.
- `includes/header.php` — cabecera, enlaces a CSS/JS.
- `includes/footer.php` — pie de página.
- `assets/css/style.css` — estilos personalizados.
- `assets/img/logo.svg`, `assets/img/hero.svg` — recursos SVG de ejemplo.

Despliegue en GoDaddy (resumen):

1. En el panel de GoDaddy (cPanel), sube todos los archivos a la carpeta `public_html` o al dominio/subdominio objetivo.
2. Asegúrate de seleccionar la versión de PHP recomendada (7.4 / 8.0 / 8.1) en el selector de PHP del hosting.
3. Reemplaza `assets/img/hero.svg` por la fotografía real y ajusta `assets/css/style.css` si lo deseas.
4. Si usas FTP, conecta con tus credenciales y sube la estructura de archivos.

Notas:
- Esta es una plantilla básica; puedes ampliar con formularios, CMS o integración con bases de datos según tu necesidad.
- Para compatibilidad máxima con GoDaddy, usa solo librerías cargadas por CDN o sube librerías locales al servidor.
