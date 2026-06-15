<?php
$password = 'prueba123';
$hash = '$2y$10$8yMA7e6eov0a.6EbqQjKOOj59JRbBDDuNJy76kKcKA3LK5GzVU7rS';

if (password_verify($password, $hash)) {
    echo "Password verified successfully.";
} else {
    echo "Password verification failed.";
}
?>