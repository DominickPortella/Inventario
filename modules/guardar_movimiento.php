<?php
session_start();
require '../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['producto_id'] ?? null;
    $tipo = $_POST['tipo_movimiento'] ?? null;
    $cantidad = $_POST['cantidad'] ?? 0;
    $precio_movimiento = $_POST['precio_movimiento'] ?? 0; // Capturamos el nuevo precio

    $responsable = $_POST['responsable'] ?? '';
    $ubicacion = $_POST['ubicacion'] ?? '';
    $notas = $_POST['observaciones'] ?? '';

    try {
        if (!$id || !$tipo || $cantidad <= 0) {
            throw new Exception("Faltan datos obligatorios o cantidad no válida.");
        }

        $pdo->beginTransaction();

        // 1. Insertar en historial incluyendo el precio de esta operación
        $sql1 = "INSERT INTO movimientos (producto_id, tipo_movimiento, cantidad, precio_movimiento, responsable, ubicacion, observaciones, usuario_id) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $pdo->prepare($sql1)->execute([
            $id,
            $tipo,
            $cantidad,
            $precio_movimiento,
            $responsable,
            $ubicacion,
            $notas,
            $_SESSION['user_id']
        ]);

        // 2. Actualizar Stock y Precio Acumulado en la tabla productos
        if ($tipo === 'entrada') {
            // Si entra material: SUMA stock y SUMA precio
            $sql2 = "UPDATE productos SET 
                     stock_actual = stock_actual + ?, 
                     precio_unitario = precio_unitario + ? 
                     WHERE id = ?";
            $pdo->prepare($sql2)->execute([$cantidad, $precio_movimiento, $id]);
        } else {
            // Si sale material: RESTA solo stock (el precio acumulado suele mantenerse o bajar proporcionalmente)
            $sql2 = "UPDATE productos SET stock_actual = stock_actual - ? WHERE id = ?";
            $pdo->prepare($sql2)->execute([$cantidad, $id]);
        }

        $pdo->commit();
        echo json_encode(['status' => 'success']);

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}