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
      <form>
        <div class="input-group">
          <label for="nombre">Nombre</label>
          <input type="text" id="nombre" name="nombre" required>
        </div>
        <div class="input-group">
          <label for="apellido_p">Apellido Paterno</label>
          <input type="text" id="apellido" name="apellido_p" required>
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
        <p class="switch">¿Ya tienes cuenta? <a href="login.html">Inicia sesión</a></p>
      </form>
    </div>
  </div>
</body>
</html>
