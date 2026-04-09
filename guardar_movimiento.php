<?php
session_start();
require 'db.php';

// Indicamos que la respuesta será JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['producto_id'];
    $tipo = $_POST['tipo_movimiento'];
    $cantidad = $_POST['cantidad'];
    $responsable = $_POST['responsable'];
    $ubicacion = $_POST['ubicacion_obra'] ?? 'Obra';
    $notas = $_POST['observaciones'] ?? '';

    try {
        $pdo->beginTransaction();

        // 1. Insertar en movimientos
        $sql1 = "INSERT INTO movimientos (producto_id, tipo_movimiento, cantidad, responsable, ubicacion_obra, observaciones, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $pdo->prepare($sql1)->execute([$id, $tipo, $cantidad, $responsable, $ubicacion, $notas, $_SESSION['user_id']]);

        // 2. Actualizar stock
        $operacion = ($tipo == 'Salida') ? "-" : "+";
        $sql2 = "UPDATE productos SET stock_actual = stock_actual $operacion ? WHERE id = ?";
        $pdo->prepare($sql2)->execute([$cantidad, $id]);

        $pdo->commit();
        
        // Enviamos éxito al JavaScript
        echo json_encode(['status' => 'success']);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}