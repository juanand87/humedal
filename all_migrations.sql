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

-- 2) Tabla SLIDES (carrusel de la portada) - con TODOS los campos del Hero integrados
CREATE TABLE IF NOT EXISTS slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    title_line2 VARCHAR(255) DEFAULT NULL,
    title_highlight VARCHAR(255) DEFAULT NULL,
    subtitle TEXT,
    image_url VARCHAR(500) DEFAULT NULL,
    link_url VARCHAR(500) DEFAULT NULL,
    `order` INT DEFAULT 0,
    feature1_title VARCHAR(100) DEFAULT NULL,
    feature1_text VARCHAR(255) DEFAULT NULL,
    feature1_icon VARCHAR(50) DEFAULT 'fa-check',
    feature2_title VARCHAR(100) DEFAULT NULL,
    feature2_text VARCHAR(255) DEFAULT NULL,
    feature2_icon VARCHAR(50) DEFAULT 'fa-file-contract',
    feature3_title VARCHAR(100) DEFAULT NULL,
    feature3_text VARCHAR(255) DEFAULT NULL,
    feature3_icon VARCHAR(50) DEFAULT 'fa-user-friends',
    button1_text VARCHAR(100) DEFAULT NULL,
    button1_url VARCHAR(500) DEFAULT NULL,
    button1_style VARCHAR(50) DEFAULT 'success',
    button2_text VARCHAR(100) DEFAULT NULL,
    button2_url VARCHAR(500) DEFAULT NULL,
    button2_style VARCHAR(50) DEFAULT 'outline-light',
    background_type ENUM('color', 'image') DEFAULT 'color',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Si la tabla slides YA EXISTE sin estas columnas, ejecutar estas líneas:
/*
ALTER TABLE slides ADD COLUMN title_line2 VARCHAR(255) DEFAULT NULL;
ALTER TABLE slides ADD COLUMN title_highlight VARCHAR(255) DEFAULT NULL;
ALTER TABLE slides ADD COLUMN feature1_title VARCHAR(100) DEFAULT NULL;
ALTER TABLE slides ADD COLUMN feature1_text VARCHAR(255) DEFAULT NULL;
ALTER TABLE slides ADD COLUMN feature1_icon VARCHAR(50) DEFAULT 'fa-check';
ALTER TABLE slides ADD COLUMN feature2_title VARCHAR(100) DEFAULT NULL;
ALTER TABLE slides ADD COLUMN feature2_text VARCHAR(255) DEFAULT NULL;
ALTER TABLE slides ADD COLUMN feature2_icon VARCHAR(50) DEFAULT 'fa-file-contract';
ALTER TABLE slides ADD COLUMN feature3_title VARCHAR(100) DEFAULT NULL;
ALTER TABLE slides ADD COLUMN feature3_text VARCHAR(255) DEFAULT NULL;
ALTER TABLE slides ADD COLUMN feature3_icon VARCHAR(50) DEFAULT 'fa-user-friends';
ALTER TABLE slides ADD COLUMN button1_text VARCHAR(100) DEFAULT NULL;
ALTER TABLE slides ADD COLUMN button1_url VARCHAR(500) DEFAULT NULL;
ALTER TABLE slides ADD COLUMN button1_style VARCHAR(50) DEFAULT 'success';
ALTER TABLE slides ADD COLUMN button2_text VARCHAR(100) DEFAULT NULL;
ALTER TABLE slides ADD COLUMN button2_url VARCHAR(500) DEFAULT NULL;
ALTER TABLE slides ADD COLUMN button2_style VARCHAR(50) DEFAULT 'outline-light';
ALTER TABLE slides ADD COLUMN background_type ENUM('color', 'image') DEFAULT 'color';
*/
