<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturamos los IDs y valores según los "name" de tu modal
    $id       = $_POST['producto_id'] ?? null;
    $tipo     = $_POST['tipo_movimiento'] ?? null; // Recibe 'entrada' o 'salida'
    $cantidad = $_POST['cantidad'] ?? 0;
    
    // CORRECCIÓN: Tu HTML usa name="referencia"
    $referencia = $_POST['referencia'] ?? ''; 
    
    // Estos campos no existen en tu modal actual, los dejamos como opcionales
    $ubicacion = $_POST['ubicacion_obra'] ?? 'Obra';
    $notas     = $_POST['observaciones'] ?? '';

    try {
        if (!$id || !$tipo || $cantidad <= 0) {
            throw new Exception("Faltan datos obligatorios o cantidad no válida.");
        }

        $pdo->beginTransaction();

        // 1. Insertar en movimientos (Asegúrate que la tabla tenga estos nombres de columna)
        $sql1 = "INSERT INTO movimientos (producto_id, tipo_movimiento, cantidad, responsable, ubicacion_obra, observaciones, usuario_id) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        // Usamos $referencia para la columna 'responsable'
        $pdo->prepare($sql1)->execute([
            $id, 
            $tipo, 
            $cantidad, 
            $referencia, 
            $ubicacion, 
            $notas, 
            $_SESSION['user_id']
        ]);

        // 2. Actualizar stock
        // CORRECCIÓN: Comparar con 'salida' en minúsculas (según tu HTML)
        $operacion = ($tipo === 'salida') ? "-" : "+";
        
        $sql2 = "UPDATE productos SET stock_actual = stock_actual $operacion ? WHERE id = ?";
        $pdo->prepare($sql2)->execute([$cantidad, $id]);

        $pdo->commit();
        echo json_encode(['status' => 'success']);
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}