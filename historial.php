<?php
session_start();
require './config/db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$sql = "SELECT m.*, p.nombre as producto_nombre, p.codigo_interno, u.usuario as nombre_usuario
        FROM movimientos m 
        JOIN productos p ON m.producto_id = p.id 
        LEFT JOIN usuarios u ON m.usuario_id = u.id
        ORDER BY m.fecha DESC";
$stmt = $pdo->query($sql);
$historial = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Movimientos - LPDC Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/historial.css">
</head>

<body>
    <div class="print-header">
        <img src="img/logo.png" alt="Logo Empresa" class="print-logo">
        <div class="text-end">
            <h3 class="fw-bold mb-0">REPORTE DE MOVIMIENTOS</h3>
            <p class="mb-0">Obra LPDC - Generado el: <?php echo date('d/m/Y H:i'); ?></p>
        </div>
    </div>

    <nav class="navbar navbar-dark bg-dark py-2 sticky-top shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center" href="panel_inventario.php">
                <img src="img/logo.png" alt="Logo OBRA LPDC" class="navbar-logo rounded-2">
            </a>
            <a href="panel_inventario.php" class="btn btn-outline-light btn-sm btn-back px-3 border-0">
                <i class="bi bi-arrow-left me-2"></i>Volver al Panel
            </a>
        </div>
    </nav>

    <div class="main-container container-fluid mt-4">
        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <h2 class="fw-bold text-dark mb-1">Historial de Movimientos</h2>
                <p class="text-muted mb-0">Auditoría en tiempo real de entradas y salidas.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <button onclick="window.print()" class="btn btn-white shadow-sm border btn-print fw-bold px-4 py-2">
                    <i class="bi bi-printer me-2 text-primary"></i>Imprimir Reporte
                </button>
            </div>
        </div>

        <div class="custom-card shadow-sm bg-white rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3">FECHA Y HORA</th>
                            <th class="py-3">MATERIAL / PRODUCTO</th>
                            <th class="py-3">TIPO</th>
                            <th class="py-3">CANTIDAD</th>
                            <th class="py-3">COSTO (S/)</th>
                            <th class="py-3">RESPONSABLE</th>
                            <th class="py-3">UBICACIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historial as $h):
                            $tipo_check = strtolower($h['tipo_movimiento']);
                            $isSalida = ($tipo_check == 'salida');
                            $statusClass = $isSalida ? 'status-salida' : 'status-entrada';
                            $statusIcon = $isSalida ? 'bi-arrow-up-right' : 'bi-arrow-down-left';
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex flex-column">
                                        <span
                                            class="fw-bold text-dark"><?php echo date('d M, Y', strtotime($h['fecha'])); ?></span>
                                        <small class="text-muted"><?php echo date('H:i', strtotime($h['fecha'])); ?>
                                            hs</small>
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($h['producto_nombre']); ?>
                                    </div>
                                    <span
                                        class="badge bg-light text-primary border-0 small"><?php echo $h['codigo_interno']; ?></span>
                                </td>

                                <td class="text-center">
                                    <span
                                        class="badge-status <?php echo $statusClass; ?> px-2 py-1 rounded-3 small fw-bold">
                                        <i class="bi <?php echo $statusIcon; ?>"></i>
                                        <?php echo strtoupper($h['tipo_movimiento']); ?>
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="fs-6 fw-bold <?php echo $isSalida ? 'text-danger' : 'text-success'; ?>">
                                        <?php echo ($isSalida ? '-' : '+'); ?>
                                        <?php echo number_format($h['cantidad'], 0); ?>
                                    </span>
                                </td>

                                <td class="text-center">
                                    <?php if (!$isSalida && $h['precio_movimiento'] > 0): ?>
                                        <span class="fw-bold text-dark">S/
                                            <?php echo number_format($h['precio_movimiento'], 2); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">---</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <span
                                        class="text-dark fw-medium"><?php echo htmlspecialchars($h['responsable'] ?: 'Sin asignar'); ?></span>
                                </td>

                                <td class="pe-4">
                                    <small class="text-secondary">
                                        <i
                                            class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($h['ubicacion'] ?: 'Obra'); ?>
                                    </small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>