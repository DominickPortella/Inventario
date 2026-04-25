<?php
session_start();
require 'config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['producto_id'];
    $tipo = $_POST['tipo_movimiento'];
    $cantidad = floatval($_POST['cantidad']);
    $responsable = $_POST['responsable'];
    $ubicacion = $_POST['ubicacion'] ?? 'Obra';

    // --- NUEVA LÓGICA DE FECHA Y HORA ---
    $fecha_input = $_POST['fecha_manual'];
    $hora_input = $_POST['hora_manual'];

    if (!empty($hora_input)) {
        // Si puso hora, la guardamos normal
        $fecha_final = $fecha_input . ' ' . $hora_input . ':00';
    } else {
        // Si NO puso hora, guardamos la fecha con una marca especial (00:00:01)
        $fecha_final = $fecha_input . ' 00:00:01';
    }

    // ------------------------------------

    if (strtolower($tipo) === 'entrada') {
        $precio = (isset($_POST['precio_movimiento']) && $_POST['precio_movimiento'] !== '')
            ? floatval($_POST['precio_movimiento'])
            : 0.00;
    } else {
        $precio = 0.00;
    }

    try {
        $pdo->beginTransaction();

        // 1. Insertar movimiento (Agregamos la columna 'fecha' en el INSERT)
        $sql1 = "INSERT INTO movimientos (producto_id, tipo_movimiento, cantidad, precio_movimiento, responsable, ubicacion, observaciones, usuario_id, fecha) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $pdo->prepare($sql1)->execute([
            $id,
            $tipo,
            $cantidad,
            $precio,
            $responsable,
            $ubicacion,
            $_POST['observaciones'] ?? '',
            $_SESSION['user_id'],
            $fecha_final // <--- Aquí mandamos la fecha armada
        ]);

        // 2. ACTUALIZACIÓN DE PRODUCTO
        if (strtolower($tipo) === 'entrada') {
            $sql2 = "UPDATE productos SET 
                     stock_actual = stock_actual + ?, 
                     precio_unitario = precio_unitario + ? 
                     WHERE id = ?";
            $pdo->prepare($sql2)->execute([$cantidad, $precio, $id]);
        } else {
            $sql2 = "UPDATE productos SET stock_actual = stock_actual - ? WHERE id = ?";
            $pdo->prepare($sql2)->execute([$cantidad, $id]);
        }

        $pdo->commit();
        echo json_encode(['status' => 'success']);

    } catch (Exception $e) {
        if ($pdo->inTransaction())
            $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}