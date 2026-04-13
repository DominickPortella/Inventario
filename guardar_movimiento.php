<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Capturamos los datos del formulario
    $id         = $_POST['producto_id'] ?? null;
    $tipo       = $_POST['tipo_movimiento'] ?? null; 
    $cantidad   = $_POST['cantidad'] ?? 0;
    
    // CAPTURA DE NUEVOS CAMPOS (Asegúrate que en el HTML los "name" sean estos)
    $responsable = $_POST['responsable'] ?? ''; 
    $ubicacion   = $_POST['ubicacion'] ?? '';
    $notas       = $_POST['observaciones'] ?? '';

    try {
        if (!$id || !$tipo || $cantidad <= 0) {
            throw new Exception("Faltan datos obligatorios o cantidad no válida.");
        }

        $pdo->beginTransaction();

        // 2. Insertar en movimientos usando las nuevas columnas: responsable y ubicacion
        // Se eliminó 'ubicacion_obra' y 'referencia' para usar los campos limpios
        $sql1 = "INSERT INTO movimientos (producto_id, tipo_movimiento, cantidad, responsable, ubicacion, observaciones, usuario_id) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $pdo->prepare($sql1)->execute([
            $id, 
            $tipo, 
            $cantidad, 
            $responsable, // Nombres y Apellidos
            $ubicacion,   // Empresa o lugar (ej: Los Portales)
            $notas, 
            $_SESSION['user_id']
        ]);

        // 3. Actualizar stock en la tabla productos
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