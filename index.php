<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario Pro - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
    <style>
        /* Estilo extra solo para centrar el login en pantalla */
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }
        .login-card-wrapper {
            width: 100%;
            max-width: 400px;
            padding: 15px;
        }
    </style>
</head>

<body>

    <div class="login-card-wrapper">
        <div class="login-container"> 
            <div class="text-center mb-4">
                <h3 class="fw-bold">Acceso al Sistema</h3>
                <p class="text-muted">Gestión de Materiales</p>
            </div>

            <form action="auth.php" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Usuario</label>
                    <input type="text" name="usuario" class="form-control" required placeholder="Tu usuario">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Contraseña</label>
                    <input type="password" name="password" class="form-control" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Ingresar Seguro</button>
            </form>
        </div>
    </div>

</body>

</html>