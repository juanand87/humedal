-- Script para restablecer permisos de MySQL en XAMPP
-- Ejecutar después de iniciar MySQL con skip-grant-tables

FLUSH PRIVILEGES;

-- Crear usuario root para localhost si no existe
CREATE USER IF NOT EXISTS 'root'@'localhost' IDENTIFIED BY '';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION;

-- Crear usuario root para 127.0.0.1 si no existe  
CREATE USER IF NOT EXISTS 'root'@'127.0.0.1' IDENTIFIED BY '';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' WITH GRANT OPTION;

-- Crear usuario root para ::1 (IPv6) si no existe
CREATE USER IF NOT EXISTS 'root'@'::1' IDENTIFIED BY '';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'::1' WITH GRANT OPTION;

FLUSH PRIVILEGES;

-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS humedal;

SELECT 'Permisos restablecidos correctamente' AS resultado;
