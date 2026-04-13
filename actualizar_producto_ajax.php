<?php
require 'config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Capturamos los datos del FormData basándonos en tu HTML
        $id = $_POST['id'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $codigo = $_POST['codigo_interno'] ?? '';
        $unidad = $_POST['unidad_medida'] ?? '';
        $tipo = $_POST['tipo'] ?? '';
        $stock_min = $_POST['stock_minimo'] ?? 0;

        // Estos son los campos que no te estaban actualizando:
        $fabricante = $_POST['fabricante'] ?? '';
        $almacen = $_POST['almacen'] ?? '';
        $precio = $_POST['precio_unitario'] ?? 0;

        if (empty($id)) {
            echo json_encode(['status' => 'error', 'message' => 'ID no recibido.']);
            exit;
        }

        $sql = "UPDATE productos SET 
                nombre = :nombre, 
                codigo_interno = :codigo, 
                unidad_medida = :unidad, 
                tipo = :tipo, 
                stock_minimo = :stock_min,
                fabricante = :fabricante,
                almacen = :almacen,
                precio_unitario = :precio
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            ':nombre' => $nombre,
            ':codigo' => $codigo,
            ':unidad' => $unidad,
            ':tipo' => $tipo,
            ':stock_min' => $stock_min,
            ':fabricante' => $fabricante,
            ':almacen' => $almacen,
            ':precio' => $precio,
            ':id' => $id
        ]);

        echo json_encode(['status' => 'success']);

    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}