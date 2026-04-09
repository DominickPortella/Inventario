<?php
require 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Recogemos los datos del FormData (los names de tus inputs)
        $id = $_POST['id'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $codigo = $_POST['codigo_interno'] ?? '';
        $unidad = $_POST['unidad_medida'] ?? '';
        $tipo = $_POST['tipo'] ?? '';
        $stock_min = $_POST['stock_minimo'] ?? 0;

        if (empty($id) || empty($nombre)) {
            echo json_encode(['status' => 'error', 'message' => 'Faltan datos críticos.']);
            exit;
        }

        // Consulta SQL con los nombres exactos de tu tabla 'productos'
        $sql = "UPDATE productos SET 
                nombre = :nombre, 
                codigo_interno = :codigo, 
                unidad_medida = :unidad, 
                tipo = :tipo, 
                stock_minimo = :stock_min 
                WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            ':nombre'    => $nombre,
            ':codigo'    => $codigo,
            ':unidad'    => $unidad,
            ':tipo'      => $tipo,
            ':stock_min' => $stock_min,
            ':id'        => $id
        ]);

        if ($result) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No hubo cambios o error en DB.']);
        }

    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error de DB: ' . $e->getMessage()]);
    }
}
?>