<?php
// Configuración de conexión a la base de datos
$host = '127.0.0.1';
$username = 'root';
$password = '';
$database = 'humedal';

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

// Función para obtener configuración del sitio
function getSiteSettings() {
    $stmt = executeQuery("SELECT * FROM site_settings LIMIT 1");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>