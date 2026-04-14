<?php
// Incluir tu archivo de conexión real
require '../config/db.php';

// Configuración de cabeceras para descarga inmediata
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=Inventario_LPDC_" . date('d-m-Y') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Crear la tabla HTML que Excel interpretará como celdas
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<table border="1">
    <thead>
        <tr style="background-color: #0d6efd; color: white; font-weight: bold;">
            <th style="background-color: #0d6efd;">CÓDIGO</th>
            <th style="background-color: #0d6efd;">DESCRIPCIÓN</th>
            <th style="background-color: #0d6efd;">UNIDAD</th>
            <th style="background-color: #0d6efd;">FABRICANTE</th>
            <th style="background-color: #0d6efd;">CATEGORÍA</th>
            <th style="background-color: #0d6efd;">ALMACÉN</th>
            <th style="background-color: #0d6efd;">STOCK ACTUAL</th>
            <th style="background-color: #0d6efd;">PRECIO UNITARIO</th>
            <th style="background-color: #0d6efd;">TOTAL S/</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Consulta a la base de datos (asegúrate que el nombre de la tabla sea correcto)
        $sql = "SELECT * FROM productos ORDER BY nombre ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($productos as $p) {
            $total_fila = $p['stock_actual'] * $p['precio_unitario'];
            echo "<tr>";
            echo "<td>" . $p['codigo_interno'] . "</td>";
            echo "<td>" . htmlspecialchars($p['nombre']) . "</td>";
            echo "<td>" . $p['unidad_medida'] . "</td>";
            echo "<td>" . ($p['fabricante'] ?? '-') . "</td>";
            echo "<td>" . $p['tipo'] . "</td>";
            echo "<td>" . ($p['almacen'] ?? 'OB. MULTIFAM PARDO') . "</td>";
            echo "<td align='center'>" . number_format($p['stock_actual'], 2) . "</td>";
            echo "<td align='right'>" . number_format($p['precio_unitario'], 2) . "</td>";
            echo "<td align='right' style='font-weight:bold;'>" . number_format($total_fila, 2) . "</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>