<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Restablecer contraseña | Coffeeology</title>
  <link rel="stylesheet" href="{{ asset('css/stylesLoginSignUp.css') }}">
  <link rel="stylesheet" href="{{ asset('css/alertas.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .password-container { position: relative; }
    .password-container input { width: 100%; padding-right: 40px; box-sizing: border-box; }
    .toggle-password { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 1.2rem; color: #888; transition: color 0.2s; }
    .toggle-password:hover { color: #333; }
    .password-requirements { font-size: 0.8rem; margin-top: 5px; }
    .password-requirements span { display: block; }
    .password-requirements .valid { color: green; }
    .password-requirements .invalid { color: red; }
    .password-match { font-size: 0.875rem; margin-top: 4px; display: block; }
    .password-match.valid { color: green; }
    .password-match.invalid { color: red; }
    .use-another { text-align: center; margin-top: 15px; }
    .use-another button { background: none; border: none; color: #c0392b; font-weight: bold; cursor: pointer; transition: all 0.3s ease; }
    .use-another button:hover { color: #e74c3c; transform: scale(1.05); }
  </style>
</head>
<body class="auth-body">

  <div class="auth-container">
    <div class="auth-card">
      <h1 class="auth-title">Restablecer contraseña</h1>

      <form id="resetForm" action="{{ route('password.reset') }}" method="POST">
        @csrf
        <input type="hidden" name="correo" value="{{ session('correo_recuperacion') ?? old('correo') }}">

        <div class="input-group">
          <label>Nueva contraseña</label>
          <div class="password-container">
            <input type="password" id="password" name="password" required placeholder="Nueva contraseña">
            <i class="bi bi-eye toggle-password" id="togglePassword"></i>
          </div>
          <div class="password-requirements" id="password-requirements">
            <span id="pw-length" class="invalid">Mínimo 8 caracteres</span>
            <span id="pw-uppercase" class="invalid">Al menos 1 mayúscula</span>
            <span id="pw-lowercase" class="invalid">Al menos 1 minúscula</span>
            <span id="pw-number" class="invalid">Al menos 1 número</span>
            <span id="pw-symbol" class="invalid">Al menos 1 símbolo (!@#$%^&*()_+...)</span>
          </div>
        </div>

        <div class="input-group">
          <label>Confirmar contraseña</label>
          <div class="password-container">
            <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Confirmar contraseña">
            <i class="bi bi-eye toggle-password" id="togglePasswordConfirm"></i>
          </div>
          <span id="password-match" class="password-match invalid"></span>
        </div>

        <button type="submit" class="btn">Restablecer contraseña</button>
      </form>

      <div class="use-another">
        <form action="{{ route('password.clear.session') }}" method="POST">
          @csrf
          <button type="submit">Usar otro correo</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirm = document.getElementById('togglePasswordConfirm');
    const matchSpan = document.getElementById('password-match');
    const form = document.getElementById('resetForm');

    const pwReq = {
      length: document.getElementById('pw-length'),
      uppercase: document.getElementById('pw-uppercase'),
      lowercase: document.getElementById('pw-lowercase'),
      number: document.getElementById('pw-number'),
      symbol: document.getElementById('pw-symbol')
    };

    function updatePwReq(value) {
      pwReq.length.className = value.length >= 8 ? 'valid' : 'invalid';
      pwReq.uppercase.className = /[A-Z]/.test(value) ? 'valid' : 'invalid';
      pwReq.lowercase.className = /[a-z]/.test(value) ? 'valid' : 'invalid';
      pwReq.number.className = /\d/.test(value) ? 'valid' : 'invalid';
      pwReq.symbol.className = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(value) ? 'valid' : 'invalid';
      updateMatch();
    }

    function updateMatch() {
      if(confirmInput.value === ''){
        matchSpan.className = 'password-match invalid';
        matchSpan.textContent = '';
        return;
      }
      if(passwordInput.value === confirmInput.value){
        matchSpan.className = 'password-match valid';
        matchSpan.textContent = 'Las contraseñas coinciden';
      } else {
        matchSpan.className = 'password-match invalid';
        matchSpan.textContent = 'Las contraseñas no coinciden';
      }
    }

    passwordInput.addEventListener('input', e => updatePwReq(e.target.value));
    confirmInput.addEventListener('input', updateMatch);

    const toggle = (input, icon) => {
      if(input.type === 'password'){
        input.type = 'text';
        icon.classList.replace('bi-eye','bi-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash','bi-eye');
      }
    };

    togglePassword.addEventListener('click', () => toggle(passwordInput, togglePassword));
    toggleConfirm.addEventListener('click', () => toggle(confirmInput, toggleConfirm));

    // Validación de coincidencia antes de submit
    form.addEventListener('submit', e => {
      if(passwordInput.value !== confirmInput.value){
        e.preventDefault();
        Swal.fire({ icon: 'error', title: 'Error', text: 'Las contraseñas no coinciden', confirmButtonText: 'Aceptar' });
      }
    });

    // Mostrar SweetAlert de sesión después del redirect
    @if(session('success'))
      Swal.fire({
        icon: 'success',
        title: '¡Listo!',
        text: '{{ session('success') }}',
        confirmButtonText: 'Aceptar'
      });
    @endif

    @if(session('error'))
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('error') }}',
        confirmButtonText: 'Aceptar'
      });
    @endif
  </script>
</body>
</html>