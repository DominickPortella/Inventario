<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario Pro - Acceso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css?v=1.1">
</head>

<body>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="text-center mb-4">
                <img src="img/logo.png" alt="Electro Corrali Logo" class="brand-logo-main">
                <h2 class="auth-title mt-3">Electro Corrali</h2>
                <p class="auth-subtitle">Gestión de Materiales</p>
            </div>

            <form action="auth.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Usuario</label>
                    <input type="text" name="usuario" class="form-control" required placeholder="Ingresa tu usuario">
                </div>

                <div class="mb-4">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" required placeholder="••••••••">
                </div>

                <button type="submit" class="btn-login">
                    INICIAR SESIÓN
                </button>
            </form>

            <div class="login-footer">
                <p>&copy; 2026 Ingeniería Eléctrica</p>
            </div>
        </div>
    </div>

</body>

</html>