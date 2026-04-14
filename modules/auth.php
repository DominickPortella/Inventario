<?php
session_start();
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_input = $_POST['usuario'];
    $pass_input = $_POST['password'];

    // 1. Buscamos al usuario por su nombre
    $stmt = $pdo->prepare("SELECT id, usuario, password_hash, rol FROM usuarios WHERE usuario = ?");
    $stmt->execute([$user_input]);
    $usuario = $stmt->fetch();

    // 2. Verificamos la contraseña usando SOLO el hash de la BD
    if ($usuario && password_verify($pass_input, $usuario['password_hash'])) {

        // 3. Si es correcto, creamos la sesión
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['username'] = $usuario['usuario'];
        $_SESSION['rol'] = $usuario['rol'];

        header("Location: ../panel_inventario.php");
        exit();
    } else {
        // 4. Si falla, regresamos con error
        header("Location: ../index.php?error=1");
        exit();
    }
}
?>