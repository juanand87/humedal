<?php
// Configuración de conexión a la base de datos
$host = 'localhost';
$username = 'root';
$password = ''; // Sin contraseña
$database = 'humedal';

// Crear conexión
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
?>