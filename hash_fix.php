<?php
require 'config/db.php';

$password_deseada = '12345'; // La clave que quieras usar
$nuevo_hash = password_hash($password_deseada, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("UPDATE usuarios SET password_hash = ? WHERE usuario = 'developer'");
    $stmt->execute([$nuevo_hash]);
    echo "✅ ÉXITO: El hash se generó y guardó correctamente en la BD.<br>";
    echo "Ahora puedes borrar este archivo y usar la seguridad real.";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}