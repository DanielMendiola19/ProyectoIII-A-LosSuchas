<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Restablecer contraseña | Coffeeology</title>
<link rel="stylesheet" href="{{ asset('css/stylesAuth.css') }}">
</head>
<body class="auth-body">
  <div class="auth-container">
    <div class="auth-card">
      <h1 class="auth-title">Restablecer contraseña</h1>
      <form action="{{ route('password.reset') }}" method="POST">
        @csrf
        <input type="hidden" name="correo" value="{{ session('correo') ?? old('correo') }}">
        <div class="input-group">
          <label>Nueva contraseña</label>
          <input type="password" name="password" placeholder="Nueva contraseña" required>
        </div>
        <div class="input-group">
          <label>Confirmar contraseña</label>
          <input type="password" name="password_confirmation" placeholder="Confirmar nueva contraseña" required>
        </div>
        <button type="submit" class="btn">Restablecer contraseña</button>
      </form>
    </div>
  </div>
</body>
</html>
