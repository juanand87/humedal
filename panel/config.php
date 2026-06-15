<?php
// Configuración de conexión a la base de datos
$host = '127.0.0.1';
$username = 'root';
$password = '';
$database = 'humedal';

// URL base del sitio (constante global)
if (!defined('BASE_URL')) {
    define('BASE_URL', '/humedal/');
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}

// Función para ejecutar consultas
function executeQuery($sql, $params = []) {
    global $pdo;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

// Función para normalizar arrays de menú guardados en JSON o serializados
function normalizeMenuItems($value) {
    $clean = function($items) {
        if (!is_array($items)) {
            return [];
        }

        return array_values(array_filter($items, function($item) {
            return is_array($item) && (!empty($item['label']) || !empty($item['url']));
        }));
    };

    if (is_array($value)) {
        return $clean($value);
    }

    if (!is_string($value) || trim($value) === '') {
        return [];
    }

    $decoded = json_decode($value, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        return $clean($decoded);
    }

    $unserialized = @unserialize($value);
    if (is_array($unserialized)) {
        return $clean($unserialized);
    }

    return [];
}

// Función para obtener configuración del sitio
function getSiteSettings() {
    $stmt = executeQuery("SELECT * FROM site_settings LIMIT 1");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>