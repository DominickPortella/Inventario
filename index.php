<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario Pro - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            /* 1. Agrega la ruta de tu imagen aquí */
            background-image: url('images/fondo.avif'); 
            
            /* 2. Configuración para que la imagen se vea bien */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed; /* Mantiene el fondo fijo al hacer scroll */
            
            display: flex;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-card {
            max-width: 400px;
            width: 100%;
            margin: auto;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            /* Fondo blanco con un toque de transparencia para un efecto moderno */
            background-color: rgba(255, 255, 255, 0.95); 
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