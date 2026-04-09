<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CORRECCIÓN: Los nombres deben coincidir con el atributo 'name' del HTML
    $codigo = $_POST['codigo_interno']; // Antes era 'codigo'
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $unidad = $_POST['unidad_medida']; // Antes era 'unidad'
    $minimo = $_POST['stock_minimo'];  // Antes era 'minimo'
    $stock_inicial = $_POST['stock_actual'] ?? 0;

    try {
        // Añadimos stock_actual a la consulta para que el stock inicial no sea siempre 0
        $sql = "INSERT INTO productos (codigo_interno, nombre, tipo, unidad_medida, stock_minimo, stock_actual) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$codigo, $nombre, $tipo, $unidad, $minimo, $stock_inicial]);

        // Si la petición es AJAX (por tu JS), devolvemos JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['status' => 'success']);
        } else {
            header("Location: panel_inventario.php?success=1");
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}