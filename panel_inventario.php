<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Carga de Lógica (Consultas y Arreglos)
require 'includes/logica_inventario.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario Obra - LPDC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --bg-body: #f4f7f6;
        }

        body {
            background-color: var(--bg-body);
            font-family: 'Inter', sans-serif;
        }

        .custom-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .stat-icon {
            background: var(--primary-color);
            color: white;
            border-radius: 12px;
            padding: 1rem;
        }

        .stock-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .status-ok {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-low {
            background-color: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
        }

        .table thead th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        input::-webkit-calendar-picker-indicator {
            display: none !important;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-dark bg-dark py-3 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="bi bi-building-gear me-2 text-primary"></i>OBRA LPDC</a>
            <div class="d-flex align-items-center">
                <div class="text-white me-3 d-none d-md-block">
                    <small class="text-muted">Hola,</small> <strong><?php echo $_SESSION['username']; ?></strong>
                </div>
                <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3">Salir</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4 mb-5 px-4">
    </div>

    <?php
    // Carga de Modales (Nuevo, Editar, Movimiento, Usuarios)
    require 'includes/modals_inventario.php';

    // Carga de JavaScript
    require 'includes/scripts_inventario.php';
    ?>
</body>

</html>