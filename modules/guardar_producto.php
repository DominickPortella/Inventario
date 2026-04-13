<?php
require 'config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Recibimos los datos del formulario "Registrar Nuevo Artículo"
        $codigo = $_POST['codigo_interno'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $unidad = $_POST['unidad_medida'] ?? '';
        $fabricante = $_POST['fabricante'] ?? '';
        $tipo = $_POST['tipo'] ?? '';
        $almacen = $_POST['almacen'] ?? '';
        $stock_act = $_POST['stock_actual'] ?? 0;
        $stock_min = $_POST['stock_minimo'] ?? 0;
        $precio = $_POST['precio_unitario'] ?? 0;
        $obs = $_POST['observaciones'] ?? '';

        // Validar campos obligatorios
        if (empty($codigo) || empty($nombre)) {
            echo json_encode(['status' => 'error', 'message' => 'Código y Nombre son obligatorios']);
            exit;
        }

        // Preparamos el SQL con todas las columnas
        $sql = "INSERT INTO productos 
                (codigo_interno, nombre, unidad_medida, fabricante, tipo, almacen, stock_actual, stock_minimo, precio_unitario, observaciones) 
                VALUES 
                (:codigo, :nombre, :unidad, :fabricante, :tipo, :almacen, :stock_act, :stock_min, :precio, :obs)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':codigo' => $codigo,
            ':nombre' => $nombre,
            ':unidad' => $unidad,
            ':fabricante' => $fabricante,
            ':tipo' => $tipo,
            ':almacen' => $almacen,
            ':stock_act' => $stock_act,
            ':stock_min' => $stock_min,
            ':precio' => $precio,
            ':obs' => $obs
        ]);

        echo json_encode(['status' => 'success']);

    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
}