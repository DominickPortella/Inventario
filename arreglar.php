<?php
require 'db.php';

// La contraseña que quieres usar
$password_clara = 'admin'; 
$nuevo_hash = password_hash($password_clara, PASSWORD_DEFAULT);

try {
    $sql = "UPDATE usuarios SET password_hash = ? WHERE usuario = 'admin'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nuevo_hash]);
    
    echo "✅ Hash actualizado correctamente. <br>";
    echo "Contraseña: <b>$password_clara</b> <br>";
    echo "Nuevo Hash en BD: <code>$nuevo_hash</code> <br>";
    echo "<a href='index.php'>Ir al Login</a>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>