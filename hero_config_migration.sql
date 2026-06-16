-- ============================================
-- Migración: Tabla hero_config para portada
-- Ejecutar en base de datos de producción
-- ============================================

CREATE TABLE IF NOT EXISTS hero_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    field_key VARCHAR(50) UNIQUE NOT NULL,
    field_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar valores por defecto (no sobrescribe los existentes)
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
