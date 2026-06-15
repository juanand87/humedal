<?php
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

$new_password = 'prueba123';
$hash = password_hash($new_password, PASSWORD_BCRYPT);

echo "Hash to Store: " . htmlspecialchars($hash) . "<br>";

try {
    $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE username = ?");
    $stmt->execute([$hash, 'nuevo_usuario']);
    echo "Hash updated successfully.<br>";
} catch(PDOException $e) {
    echo "Error updating hash: " . $e->getMessage();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute(['nuevo_usuario']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Stored Password: " . htmlspecialchars($user['password']) . "<br>";
    echo "Stored Password Length: " . strlen($user['password']) . "<br>";
    echo "Stored Password Hex: " . bin2hex($user['password']) . "<br>";
} catch(PDOException $e) {
    echo "Error retrieving hash: " . $e->getMessage();
}
?>