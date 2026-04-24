<?php
// Evitamos que cualquier error de texto ensucie la respuesta JSON
error_reporting(0);
ini_set('display_errors', 0);

require_once 'config/db.php';

header('Content-Type: application/json');

try {
    // Verificamos que la variable de db.php sea la correcta
    if (!isset($pdo)) {
        throw new Exception("No se encontró la conexión \$pdo");
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Petición no válida");
    }

    // Captura de datos
    $id = $_POST['id'] ?? null;
    if (!$id) throw new Exception("ID de producto no recibido");

    $data = [
        'nom' => $_POST['nombre'] ?? '',
        'cod' => $_POST['codigo_interno'] ?? '',
        'uni' => $_POST['unidad_medida'] ?? '',
        'fab' => $_POST['fabricante'] ?? '',
        'tip' => $_POST['tipo'] ?? '',
        'alm' => $_POST['almacen'] ?? '',
        'stk' => floatval($_POST['stock_minimo'] ?? 0),
        'pre' => floatval($_POST['precio_unitario'] ?? 0),
        'obs' => $_POST['observaciones'] ?? '',
        'id'  => $id
    ];

    // Query para PDO
    $sql = "UPDATE productos SET 
            nombre = :nom, 
            codigo_interno = :cod, 
            unidad_medida = :uni, 
            fabricante = :fab, 
            tipo = :tip, 
            almacen = :alm, 
            stock_minimo = :stk, 
            precio_unitario = :pre, 
            observaciones = :obs 
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute($data)) {
        echo json_encode(['status' => 'success', 'message' => 'Material actualizado correctamente']);
    } else {
        throw new Exception("Error al ejecutar la actualización");
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}