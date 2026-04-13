<?php
session_start();
require 'config/db.php';

// Seguridad: Solo admin puede tocar usuarios
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    if ($action === 'crear') {
        $user = $_POST['usuario'];
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $rol = $_POST['rol'];

        $stmt = $pdo->prepare("INSERT INTO usuarios (usuario, password_hash, rol) VALUES (?, ?, ?)");
        $stmt->execute([$user, $pass, $rol]);
        echo json_encode(['status' => 'success']);

    } elseif ($action === 'editar') {
        $id = $_POST['id'];
        $user = $_POST['usuario'];
        $rol = $_POST['rol'];

        // Si mandó contraseña nueva, la actualizamos, si no, solo el resto
        if (!empty($_POST['password'])) {
            $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET usuario = ?, password_hash = ?, rol = ? WHERE id = ?");
            $stmt->execute([$user, $pass, $rol, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios SET usuario = ?, rol = ? WHERE id = ?");
            $stmt->execute([$user, $rol, $id]);
        }
        echo json_encode(['status' => 'success']);

    } elseif ($action === 'eliminar') {
        $id = $_POST['id'];
        // Evitar que el admin se borre a sí mismo
        if ($id == $_SESSION['user_id']) {
            echo json_encode(['status' => 'error', 'message' => 'No puedes eliminarte a ti mismo']);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}