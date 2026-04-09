<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario Pro - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f6;
            display: flex;
            align-items: center;
            height: 100vh;
        }

        .login-card {
            max-width: 400px;
            width: 100%;
            margin: auto;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>

    <div class="card login-card p-4">
        <div class="text-center mb-4">
            <h3>Acceso al Sistema</h3>
            <p class="text-muted">Gestión de Materiales</p>
        </div>

        <form action="auth.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input type="text" name="usuario" class="form-control" required placeholder="Tu usuario">
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">Ingresar Seguro</button>
        </form>
    </div>

</body>

</html>