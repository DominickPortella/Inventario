<?php
require 'db.php';

// Configuración del usuario inicial
$usuario_nombre = 'admin';
$password_clara = 'admin123'; // Puedes cambiarla aquí
$nuevo_hash = password_hash($password_clara, PASSWORD_DEFAULT);

try {
    // Primero intentamos insertar el usuario
    // Si ya existe, fallará por el UNIQUE del campo usuario, así que usamos un try/catch
    $sql = "INSERT INTO usuarios (usuario, password_hash, rol) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario_nombre, $nuevo_hash, 'admin']);

    echo "✅ Usuario maestro creado correctamente. <br>";
    echo "Usuario: <b>$usuario_nombre</b> <br>";
    echo "Contraseña: <b>$password_clara</b> <br>";
    echo "<br><a href='index.php' style='padding:10px; background:#0d6efd; color:white; text-decoration:none; border-radius:5px;'>Ir al Login</a>";

} catch (Exception $e) {
    // Si sale error porque ya existe, entonces intentamos actualizarlo
    if ($e->getCode() == 23000) {
        $sql = "UPDATE usuarios SET password_hash = ? WHERE usuario = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nuevo_hash, $usuario_nombre]);
        echo "✅ El usuario ya existía, así que se actualizó la contraseña. <br>";
    } else {
        echo "❌ Error: " . $e->getMessage();
    }
}
?>