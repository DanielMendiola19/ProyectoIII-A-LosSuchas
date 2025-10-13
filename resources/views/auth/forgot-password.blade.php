<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recuperar contraseña | Coffeeology</title>
<link rel="stylesheet" href="{{ asset('css/stylesAuth.css') }}">
</head>
<body class="auth-body">
  <div class="auth-container">
    <div class="auth-card">
      <h1 class="auth-title">Recuperar contraseña</h1>
      <form action="{{ route('password.send') }}" method="POST">
        @csrf
        <div class="input-group">
          <label for="correo">Correo electrónico</label>
          <input type="email" name="correo" id="correo" placeholder="Ingresa tu correo" required>
          <span class="error" id="error-correo"></span>
        </div>
        <button type="submit" class="btn">Enviar código</button>
      </form>
    </div>
  </div>
</body>
</html>
