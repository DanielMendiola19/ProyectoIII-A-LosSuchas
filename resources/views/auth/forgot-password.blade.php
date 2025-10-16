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

      {{-- Mensajes de éxito o error --}}
      @if(session('success'))
        <div style="color: green; text-align:center; margin-bottom:10px;">
          {{ session('success') }}
        </div>
      @endif
      @if(session('error'))
        <div style="color: red; text-align:center; margin-bottom:10px;">
          {{ session('error') }}
        </div>
      @endif

      <form action="{{ route('password.send') }}" method="POST" novalidate>
        @csrf
        <div class="input-group">
          <label for="correo">Correo electrónico</label>
          <input type="email" name="correo" id="correo" placeholder="Ingresa tu correo" required value="{{ old('correo') }}">
          @error('correo')
            <span class="error">{{ $message }}</span>
          @enderror
        </div>
        <button type="submit" class="btn">Enviar código</button>
      </form>

      <a href="{{ route('login.form') }}" class="back-dashboard">
          ← Volver al inicio de sesión
      </a>

      <style>
      .back-dashboard {
          display: inline-block;
          margin-top: 15px;
          color: #4B2E2E;
          text-decoration: none;
          font-weight: bold;
          transition: all 0.3s ease;
      }

      .back-dashboard:hover {
          color: #f5c518; /* color dorado */
          transform: translateX(-3px);
      }
      </style>


    </div>
  </div>
</body>
</html>