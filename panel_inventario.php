<?php
session_start();
require 'config/db.php';
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/panel_inventario.css">
</head>

<body>
    <nav class="navbar navbar-dark bg-dark py-2 shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center" href="panel_inventario.php">
                <img src="img/logo.png" alt="Logo OBRA LPDC" class="navbar-logo rounded-2">
            </a>
            <div class="d-flex align-items-center">
                <div class="text-white me-3 d-none d-md-block">
                    <small class="text-muted">Hola,</small> <strong><?php echo $_SESSION['username']; ?></strong>
                </div>
                <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3">Salir</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4 mb-5 px-4">

        <div class="d-flex gap-2">

        </div>

        <?php
        // Carga de Modales (Nuevo, Editar, Movimiento, Usuarios)
        require 'includes/modals_inventario.php';

        // Carga de JavaScript
        require 'includes/scripts_inventario.php';
        ?>
</body>

</html>