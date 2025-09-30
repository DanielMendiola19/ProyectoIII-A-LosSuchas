<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro | Coffeeology</title>
  <link rel="stylesheet" href="stylesLoginSignUp.css">
</head>
<body class="auth-body">
  <div class="auth-container">
    <div class="auth-card">
      <h1 class="auth-title">Crear Cuenta</h1>
      <form action="{{ route('signup') }}" method="POST">
        @csrf
        <div class="input-group">
          <label for="nombre">Nombre</label>
          <input type="text" id="nombre" name="nombre" required>
        </div>

        <div class="input-group">
          <label for="apellido">Apellido</label>
          <input type="text" id="apellido" name="apellido" required>
        </div>

        <div class="input-group">
          <label for="rol_id">Rol</label>
          <select name="rol_id" id="rol_id" required>
            <option value="">-- Selecciona un rol --</option>
            @foreach($roles as $rol)
              <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
            @endforeach
          </select>
        </div>

        <div class="input-group">
          <label for="correo">Correo</label>
          <input type="email" id="correo" name="correo" required>
        </div>

        <div class="input-group">
          <label for="password">Contraseña</label>
          <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn">Registrarse</button>
        <p class="switch">¿Ya tienes cuenta? <a href="{{ route('login.form') }}">Inicia sesión</a></p>
      </form>

    </div>
  </div>
</body>
</html>
