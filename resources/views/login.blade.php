<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Coffeeology</title>
  <link rel="stylesheet" href="{{ asset('css/stylesLoginSignUp.css') }}">
  <link rel="stylesheet" href="{{ asset('css/alertas.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    .error { color: red; font-size: 0.875rem; display: block; }
    .valid { color: green; }
    .password-container { position: relative; }
    .password-container input { width: 100%; padding-right: 40px; box-sizing: border-box; }
    .toggle-password { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 1.2rem; color: #888; transition: color 0.2s; z-index: 2; }
    .toggle-password:hover { color: #333; }
    .input-group { margin-bottom: 15px; position: relative; }
  </style>
</head>
<body class="auth-body">
  <div class="auth-container">
    <div class="auth-card">
      <h1 class="auth-title">Iniciar Sesión</h1>

      <form id="loginForm" novalidate>
        @csrf

        <!-- Correo -->
        <div class="input-group">
            <label for="correo">Correo</label>
            <input type="email" id="correo" name="correo">
            <span class="error" id="error-correo"></span>
        </div>

        <!-- Contraseña -->
        <div class="input-group password-container">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password">
            <i id="togglePassword" class="bi bi-eye toggle-password"></i>
            <span class="error" id="error-password"></span>
        </div>

        <button type="submit" class="btn">Ingresar</button>
        <p class="switch">¿No tienes cuenta? <a href="{{ route('signup.form') }}">Regístrate</a></p>
    </form>
    </div>
  </div>

  <script>
    const loginForm = document.getElementById('loginForm');
    const fields = ['correo', 'password'];
    const pwInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');

    // Toggle ojo
    togglePassword.addEventListener('click', () => {
      if(pwInput.type === 'password'){
        pwInput.type = 'text';
        togglePassword.classList.replace('bi-eye','bi-eye-slash');
      } else {
        pwInput.type = 'password';
        togglePassword.classList.replace('bi-eye-slash','bi-eye');
      }
    });

    // Validación en tiempo real y submit con SweetAlert2
    async function validateField(field){
      const value = document.getElementById(field).value;
      const token = document.querySelector('input[name="_token"]').value;
      const formData = new FormData();
      formData.append(field, value);
      formData.append('_token', token);

      try {
        const response = await fetch("{{ route('login') }}", {
          method: 'POST',
          headers: { 'Accept': 'application/json' },
          body: formData
        });
        const data = await response.json();
        const errorSpan = document.getElementById('error-' + field);
        errorSpan.textContent = data.errors && data.errors[field] ? data.errors[field][0] : '';
      } catch(err){ console.error(err); }
    }

    fields.forEach(f => {
      document.getElementById(f).addEventListener('input', () => validateField(f));
    });

    loginForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(loginForm);
      formData.append('_token', document.querySelector('input[name="_token"]').value);

      // Limpiar errores
      fields.forEach(f => document.getElementById('error-' + f).textContent = '');

      // SweetAlert2 de carga con colores del sistema
      Swal.fire({
        title: 'Verificando...',
        html: '<div class="loader"></div><p>Por favor espera</p>',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
          // Mostrar animación de carga
          const content = Swal.getHtmlContainer();
          content.querySelector('.loader').style.border = '4px solid var(--gris-suave)';
          content.querySelector('.loader').style.borderTop = '4px solid var(--dorado)';
          content.querySelector('.loader').style.borderRadius = '50%';
          content.querySelector('.loader').style.width = '40px';
          content.querySelector('.loader').style.height = '40px';
          content.querySelector('.loader').style.margin = '0 auto';
          content.querySelector('.loader').style.animation = 'spin 1s linear infinite';
        }
      });

      try {
        const response = await fetch("{{ route('login') }}", {
          method: 'POST',
          headers: { 'Accept': 'application/json' },
          body: formData
        });

        const data = await response.json();
        Swal.close();

        if(response.status === 422){
          Object.keys(data.errors).forEach(key => {
            const errorSpan = document.getElementById('error-' + key);
            if(errorSpan) errorSpan.textContent = data.errors[key][0];
          });
        } else if(response.status === 401){
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.message || 'Credenciales incorrectas',
            confirmButtonText: 'Aceptar'
          });
        } else if(response.ok){
          Swal.fire({
            icon: 'success',
            title: '¡Bienvenido!',
            text: data.message || 'Inicio de sesión exitoso',
            confirmButtonText: 'Aceptar'
          }).then(() => { 
            //window.location.href = "/dashboard";
            window.location.href = "/bienvenida";  
          });
        }

      } catch(err) {
        Swal.close();
        Swal.fire({
          icon: 'error',
          title: 'Error crítico',
          text: 'No se pudo iniciar sesión. Intenta nuevamente más tarde',
          confirmButtonText: 'Aceptar'
        });
        console.error(err);
      }
    });

  </script>
</body>
</html>
