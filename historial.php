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
    <title>Historial LPDC - UX Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-dark: #212529;
            --accent-color: #0d6efd;
        }

        body {
            background-color: #f4f7f6;
            font-family: 'Segoe UI', Roboto, sans-serif;
        }

        /* Contenedor principal con aire */
        .main-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        /* Card estilo moderno */
        .custom-card {
            background: white;
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* Ajustes de Tabla Responsive UX */
        @media (max-width: 768px) {
            .responsive-table thead {
                display: none;
            }

            /* Escondemos cabecera en móvil */
            .responsive-table td {
                display: block;
                text-align: right;
                padding-left: 50%;
                position: relative;
                border-bottom: 1px solid #eee;
            }

            .responsive-table td::before {
                content: attr(data-label);
                /* Usamos etiquetas para saber qué es cada dato */
                position: absolute;
                left: 15px;
                width: 45%;
                text-align: left;
                font-weight: 700;
                color: #666;
            }

            .responsive-table tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #e0e0e0;
                border-radius: 10px;
                background: #fff;
            }
        }

        /* Badges estilizados */
        .badge-custom {
            padding: 0.5em 0.8em;
            border-radius: 6px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
        }

        .bg-salida {
            background-color: #fff5f5;
            color: #e03131;
            border: 1px solid #ffc9c9;
        }

        .bg-entrada {
            background-color: #f4fce3;
            color: #2f9e41;
            border: 1px solid #d8f5a2;
        }

        .btn-back {
            border-radius: 8px;
            transition: 0.3s;
        }

        .btn-back:hover {
            transform: translateX(-5px);
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark bg-dark py-3">
        <div class="container">
            <span class="navbar-brand mb-0 h1"><i class="bi bi-building-gear me-2"></i>OBRA LPDC</span>
            <a href="panel_inventario.php" class="btn btn-outline-light btn-sm btn-back">
                <i class="bi bi-arrow-left"></i> Panel Principal
            </a>
        </div>
    </nav>

    <div class="main-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-0">Historial de Movimientos</h2>
                <p class="text-muted">Registro detallado de entradas y salidas de materiales</p>
            </div>
            <button onclick="window.print()" class="btn btn-white shadow-sm border d-none d-md-block">
                <i class="bi bi-printer me-2"></i>Imprimir Reporte
            </button>
        </div>

        <div class="custom-card">
            <div class="table-responsive">
                <table class="table responsive-table align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Fecha</th>
                            <th>Material</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Cant.</th>
                            <th>Responsable</th>
                            <th class="pe-4">Ubicación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historial as $h): ?>
                            <tr>
                                <td data-label="Fecha" class="ps-4">
                                    <div class="fw-bold"><?php echo date('d/m/Y', strtotime($h['fecha'])); ?></div>
                                    <div class="small text-muted"><?php echo date('H:i', strtotime($h['fecha'])); ?> hs
                                    </div>
                                </td>
                                <td data-label="Material">
                                    <span
                                        class="text-dark fw-semibold"><?php echo htmlspecialchars($h['producto_nombre']); ?></span><br>
                                    <code class="small text-primary"><?php echo $h['codigo_interno']; ?></code>
                                </td>
                                <td data-label="Tipo" class="text-center">
                                    <?php
                                    $typeClass = ($h['tipo_movimiento'] == 'Salida') ? 'bg-salida' : 'bg-entrada';
                                    ?>
                                    <span class="badge-custom <?php echo $typeClass; ?>">
                                        <?php echo $h['tipo_movimiento']; ?>
                                    </span>
                                </td>
                                <td data-label="Cantidad" class="text-center fw-bold text-dark">
                                    <?php echo ($h['cantidad'] == floor($h['cantidad'])) ? number_format($h['cantidad'], 0) : number_format($h['cantidad'], 2); ?>
                                </td>
                                <td data-label="Responsable">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-circle me-2 text-muted"></i>
                                        <span><?php echo htmlspecialchars($h['responsable']); ?></span>
                                    </div>
                                </td>
                                <td data-label="Ubicación" class="pe-4">
                                    <span class="text-muted"><i
                                            class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($h['ubicacion_obra']); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>

</html>