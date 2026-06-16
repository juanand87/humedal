-- ============================================
-- TODAS las migraciones necesarias
-- Base de datos: humedal
-- ============================================

-- 1) Tabla MEDIA (biblioteca de imágenes)
CREATE TABLE IF NOT EXISTS media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) DEFAULT NULL,
    file_size INT DEFAULT 0,
    uploaded_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2) Tabla SLIDES (carrusel de la portada)
CREATE TABLE IF NOT EXISTS slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255) DEFAULT NULL,
    image_url VARCHAR(500) DEFAULT NULL,
    link_url VARCHAR(500) DEFAULT NULL,
    `order` INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3) Tabla HERO_CONFIG (textos editables de la portada)
CREATE TABLE IF NOT EXISTS hero_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    field_key VARCHAR(50) UNIQUE NOT NULL,
    field_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar valores por defecto del Hero
INSERT IGNORE INTO hero_config (field_key, field_value) VALUES
('title_line1', 'Expertos en tramitaciones'),
('title_line2', 'sanitarias ante'),
('title_highlight', 'SEREMI Salud'),
('subtitle', 'Asesoría técnica integral para proyectos sanitarios, ambientales y de infraestructura.'),
('feature1_title', 'Experiencia'),
('feature1_text', 'en proyectos sanitarios'),
('feature1_icon', 'fa-check'),
('feature2_title', 'Tramitación'),
('feature2_text', 'eficiente y profesional'),
('feature2_icon', 'fa-file-contract'),
('feature3_title', 'Acompañamiento'),
('feature3_text', 'en todo el proceso'),
('feature3_icon', 'fa-user-friends'),
('button1_text', 'NUESTROS SERVICIOS'),
('button1_url', '#servicios'),
('button1_style', 'success'),
('button2_text', 'CONTÁCTANOS'),
('button2_url', '#contacto'),
('button2_style', 'outline-light'),
('background_type', 'color'),
('background_image', '');
