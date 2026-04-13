<?php
session_start();
require 'db.php';
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
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --bg-body: #f8f9fa;
        }

        body {
            background-color: var(--bg-body);
            font-family: 'Inter', sans-serif;
            color: #343a40;
        }

        .main-container {
            max-width: 1300px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        /* Card con sombra suave estilo Apple/Stripe */
        .custom-card {
            background: white;
            border-radius: 16px;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        /* Estilos de Tabla */
        .table thead th {
            background-color: #fcfcfc;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            font-weight: 700;
            color: #6c757d;
            padding: 1.25rem 1rem;
            border-bottom: 2px solid #f1f1f1;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8faff;
        }

        /* Badges de Estado Premium */
        .badge-status {
            padding: 0.6rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.7rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-entrada {
            background-color: #e6fcf5;
            color: #087f5b;
            border: 1px solid #c3fae8;
        }

        .status-salida {
            background-color: #fff5f5;
            color: #c92a2a;
            border: 1px solid #ffe3e3;
        }

        /* Icono de Producto */
        .product-icon {
            width: 40px;
            height: 40px;
            background: #eef2ff;
            color: #4361ee;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 1.2rem;
        }

        /* Animación Botón Volver */
        .btn-back {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-back:hover {
            background-color: #f1f3f5;
            transform: translateX(-4px);
        }

        @media print {
            .btn-back, .navbar, .btn-print { display: none !important; }
            .main-container { margin: 0; width: 100%; max-width: 100%; }
            .custom-card { box-shadow: none; border: 1px solid #ddd; }
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark bg-dark py-3 sticky-top shadow-sm">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold fs-4">
                <i class="bi bi-layers-half me-2"></i>OBRA LPDC
            </span>
            <a href="panel_inventario.php" class="btn btn-outline-light btn-sm btn-back px-3 border-0">
                <i class="bi bi-arrow-left me-2"></i>Volver al Panel
            </a>
        </div>
    </nav>

    <div class="main-container">
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

        <div class="custom-card">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Fecha y Hora</th>
                            <th>Material / Producto</th>
                            <th class="text-center">Tipo de Movimiento</th>
                            <th class="text-center">Cantidad</th>
                            <th>Responsable</th>
                            <th class="pe-4">Ubicación Destino</th>
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
                                        <span class="fw-bold text-dark"><?php echo date('d M, Y', strtotime($h['fecha'])); ?></span>
                                        <small class="text-muted"><i class="bi bi-clock me-1"></i><?php echo date('H:i', strtotime($h['fecha'])); ?> hs</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="product-icon me-3">
                                            <i class="bi bi-box-seam"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($h['producto_nombre']); ?></div>
                                            <span class="badge bg-light text-primary border fw-medium"><?php echo $h['codigo_interno']; ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge-status <?php echo $statusClass; ?>">
                                        <i class="bi <?php echo $statusIcon; ?>"></i>
                                        <?php echo strtoupper($h['tipo_movimiento']); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fs-5 fw-bold <?php echo $isSalida ? 'text-danger' : 'text-success'; ?>">
                                        <?php echo ($isSalida ? '-' : '+'); ?>
                                        <?php echo ($h['cantidad'] == floor($h['cantidad'])) ? number_format($h['cantidad'], 0) : number_format($h['cantidad'], 2); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-secondary-subtle rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                            <i class="bi bi-person text-secondary" style="font-size: 0.9rem;"></i>
                                        </div>
                                        <span class="text-dark fw-medium"><?php echo htmlspecialchars($h['responsable'] ?: 'Sin asignar'); ?></span>
                                    </div>
                                </td>
                                <td class="pe-4">
                                    <span class="text-secondary small fw-medium">
                                        <i class="bi bi-geo-alt-fill me-1 text-danger"></i>
                                        <?php echo htmlspecialchars($h['ubicacion'] ?: 'Obra Central'); ?>
                                    </span>
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