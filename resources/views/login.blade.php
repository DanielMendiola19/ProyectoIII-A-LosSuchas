<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Coffeeology</title>
  <link rel="stylesheet" href="stylesLoginSignUp.css">
</head>
<body class="auth-body">
  <div class="auth-container">
    <div class="auth-card">
      <h1 class="auth-title">Iniciar Sesión</h1>
      <form>
        <div class="input-group">
          <label for="correo">Correo</label>
          <input type="email" id="correo" name="correo" required>
        </div>
        <div class="input-group">
          <label for="password">Contraseña</label>
          <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn">Ingresar</button>
        <p class="switch">¿No tienes cuenta? <a href="signup.html">Regístrate</a></p>
      </form>
    </div>
  </div>
</body>
</html>
