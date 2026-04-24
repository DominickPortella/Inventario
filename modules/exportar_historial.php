<?php
require '../config/db.php';

// Cabeceras para descarga
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=Historial_Movimientos_" . date('d-m-Y') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Consulta idéntica a la de historial.php para que los datos coincidan
$sql = "SELECT m.*, p.nombre as producto_nombre, p.codigo_interno, u.usuario as nombre_usuario
        FROM movimientos m 
        JOIN productos p ON m.producto_id = p.id 
        LEFT JOIN usuarios u ON m.usuario_id = u.id
        ORDER BY m.fecha DESC";
$stmt = $pdo->query($sql);
$historial = $stmt->fetchAll();
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<table border="1">
    <thead>
        <tr style="background-color: #198754; color: white; font-weight: bold;">
            <th style="background-color: #198754;">FECHA</th>
            <th style="background-color: #198754;">HORA</th>
            <th style="background-color: #198754;">CÓDIGO</th>
            <th style="background-color: #198754;">MATERIAL / PRODUCTO</th>
            <th style="background-color: #198754;">TIPO</th>
            <th style="background-color: #198754;">CANTIDAD</th>
            <th style="background-color: #198754;">COSTO UNIT. (S/)</th>
            <th style="background-color: #198754;">RESPONSABLE</th>
            <th style="background-color: #198754;">UBICACIÓN</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($historial as $h): 
            $isSalida = (strtolower($h['tipo_movimiento']) == 'salida');
        ?>
            <tr>
                <td><?php echo date('d/m/Y', strtotime($h['fecha'])); ?></td>
                <td><?php echo date('H:i', strtotime($h['fecha'])); ?></td>
                <td><?php echo $h['codigo_interno']; ?></td>
                <td><?php echo htmlspecialchars($h['producto_nombre']); ?></td>
                <td align="center"><?php echo strtoupper($h['tipo_movimiento']); ?></td>
                <td align="center" style="color: <?php echo $isSalida ? '#dc3545' : '#198754'; ?>;">
                    <?php echo ($isSalida ? '-' : '+') . number_format($h['cantidad'], 2); ?>
                </td>
                <td align="right">
                    <?php echo ($h['precio_movimiento'] > 0) ? number_format($h['precio_movimiento'], 2) : '0.00'; ?>
                </td>
                <td><?php echo htmlspecialchars($h['responsable'] ?: 'Sin asignar'); ?></td>
                <td><?php echo htmlspecialchars($h['ubicacion'] ?: 'Obra'); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>