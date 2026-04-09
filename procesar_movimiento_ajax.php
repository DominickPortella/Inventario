<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['producto_id'];
    $tipo = $_POST['tipo_movimiento'];
    $cantidad = floatval($_POST['cantidad']);
    $responsable = $_POST['responsable'];
    $ubicacion = $_POST['ubicacion_obra'] ?? 'Obra';
    $notas = $_POST['observaciones'] ?? '';

    try {
        $pdo->beginTransaction();

        // 1. Insertar movimiento
        $sql1 = "INSERT INTO movimientos (producto_id, tipo_movimiento, cantidad, responsable, ubicacion_obra, observaciones, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $pdo->prepare($sql1)->execute([$id, $tipo, $cantidad, $responsable, $ubicacion, $notas, $_SESSION['user_id']]);

        // 2. Actualizar stock
        $operacion = ($tipo == 'Salida') ? "-" : "+";
        $sql2 = "UPDATE productos SET stock_actual = stock_actual $operacion ? WHERE id = ?";
        $pdo->prepare($sql2)->execute([$cantidad, $id]);

        // 3. Obtener el nuevo stock para devolverlo a la interfaz
        $sql3 = "SELECT stock_actual, stock_minimo FROM productos WHERE id = ?";
        $stmt = $pdo->prepare($sql3);
        $stmt->execute([$id]);
        $productoActualizado = $stmt->fetch();

        $pdo->commit();

        echo json_encode([
            'status' => 'success',
            'nuevo_stock' => $productoActualizado['stock_actual'],
            'stock_minimo' => $productoActualizado['stock_minimo']
        ]);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}