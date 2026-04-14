<?php
session_start();
require 'config/db.php'; // Asegúrate de que la ruta sea correcta según la ubicación del archivo

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['producto_id'];
    $tipo = $_POST['tipo_movimiento'];
    $cantidad = floatval($_POST['cantidad']);
    $responsable = $_POST['responsable'];
    $ubicacion = $_POST['ubicacion'] ?? 'Obra'; // Ajustado a tu columna real 'ubicacion'
    
    // LÓGICA DE PRECIO:
    // Si es Entrada, intentamos capturar el precio. Si es Salida, forzamos 0.
    if (strtolower($tipo) === 'entrada') {
        $precio = (isset($_POST['precio_movimiento']) && $_POST['precio_movimiento'] !== '') 
                  ? floatval($_POST['precio_movimiento']) 
                  : 0.00;
    } else {
        $precio = 0.00; // En salidas el precio siempre será 0
    }

    try {
        $pdo->beginTransaction();

        // 1. Insertar movimiento con el precio ya validado
        $sql1 = "INSERT INTO movimientos (producto_id, tipo_movimiento, cantidad, precio_movimiento, responsable, ubicacion, observaciones, usuario_id) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $pdo->prepare($sql1)->execute([
            $id, $tipo, $cantidad, $precio, $responsable, $ubicacion, $_POST['observaciones'] ?? '', $_SESSION['user_id']
        ]);

        // 2. Actualizar stock
        $operacion = (strtolower($tipo) == 'salida') ? "-" : "+";
        $sql2 = "UPDATE productos SET stock_actual = stock_actual $operacion ? WHERE id = ?";
        $pdo->prepare($sql2)->execute([$cantidad, $id]);

        $pdo->commit();
        echo json_encode(['status' => 'success']);

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}