<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido | Coffeeology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --negro-carbon: #0D0D0D; 
            --dorado: #E6B325;
            --blanco-hueso: #FAF9F6;
            --gris-suave: #C0C0C0;
            --cafe-espresso: #4B2E2E;
            --rojo-fuego: #9f1010;
            --amarillo-alarma: #F2C94C;
        }

        body {
            background-color: var(--blanco-hueso);
            color: var(--cafe-espresso);
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            background-color: var(--blanco-hueso);
            border: 2px solid var(--gris-suave);
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .btn-logout {
            background-color: var(--dorado);
            color: var(--blanco-hueso);
            font-weight: bold;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            margin-top: 20px;
            transition: background 0.3s;
        }

        .btn-logout:hover {
            background-color: #c89a20;
        }

        h2 {
            margin-bottom: 15px;
            font-weight: bold;
        }

        p {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <div class="card">
        <h2>¡Bienvenido, {{ $usuario->nombre }} {{ $usuario->apellido }}!</h2>
        <p><strong>Correo:</strong> {{ $usuario->correo }}</p>
        <p><strong>Rol:</strong> {{ $usuario->rol->nombre }}</p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-logout">Cerrar Sesión</button>
        </form>
    </div>

</body>
</html>
