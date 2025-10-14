<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro | Coffeeology</title>
  <link rel="stylesheet" href="<?php echo e(asset('css/stylesLoginSignUp.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('css/alertas.css')); ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <style>
    .error { color: red; font-size: 0.875rem; display: block; }
    .valid { color: green; }
    .password-requirements { font-size: 0.8rem; margin-top: 5px; }
    .password-requirements span { display: block; }
    .password-requirements span.valid { color: green; }
    .password-requirements span.invalid { color: red; }

    /* Contenedor relativo para el input con ojo */
    .password-container {
      position: relative;
    }

    /* Input con padding para el ojo */
    .password-container input {
      width: 100%;
      padding-right: 40px;
      box-sizing: border-box;
    }

    /* Ojo dentro del input */
    .toggle-password {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      width: 24px;
      height: 24px;
      fill: #888;
      transition: fill 0.2s;
      z-index: 2; /* Asegura que esté por encima del input */
    }

    .toggle-password:hover {
      fill: #333;
    }
    
    /* Estilo para el grupo de input */
    .input-group {
      margin-bottom: 15px;
      position: relative; /* Asegura que el contenedor de contraseña se posicione correctamente */
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="auth-body">
  <div class="auth-container">
    <div class="auth-card">
      <h1 class="auth-title">Crear Cuenta</h1>

      <form id="signupForm">
        <?php echo csrf_field(); ?>

        <!-- Nombre -->
        <div class="input-group">
          <label for="nombre">Nombre</label>
          <input type="text" id="nombre" name="nombre">
          <span class="error" id="error-nombre"></span>
        </div>

        <!-- Apellido -->
        <div class="input-group">
          <label for="apellido">Apellido</label>
          <input type="text" id="apellido" name="apellido">
          <span class="error" id="error-apellido"></span>
        </div>

        <!-- Rol -->
        <div class="input-group">
          <label for="rol_id">Rol</label>
          <select name="rol_id" id="rol_id">
            <option value="">-- Selecciona un rol --</option>
            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($rol->id); ?>"><?php echo e($rol->nombre); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <span class="error" id="error-rol_id"></span>
        </div>

        <!-- Correo -->
        <div class="input-group">
          <label for="correo">Correo</label>
          <input type="email" id="correo" name="correo">
          <span class="error" id="error-correo"></span>
        </div>

        <!-- Contraseña con ojo dentro del input -->
        <div class="input-group">
          <label for="password">Contraseña</label>
          <div class="input-group password-container">
              <input type="password" id="password" name="password" class="form-control" style="padding-right: 40px;">
              <i id="togglePassword" class="bi bi-eye toggle-password"></i>
            </div>

          <span class="error" id="error-password"></span>
          <div class="password-requirements" id="password-requirements">
            <span id="pw-length" class="invalid">Mínimo 8 caracteres</span>
            <span id="pw-uppercase" class="invalid">Al menos 1 mayúscula</span>
            <span id="pw-lowercase" class="invalid">Al menos 1 minúscula</span>
            <span id="pw-number" class="invalid">Al menos 1 número</span>
            <span id="pw-symbol" class="invalid">Al menos 1 símbolo (!@#$%^&*()_+-=[]{}|;:'",.<>?/)</span>
          </div>
        </div>

        <button type="submit" class="btn">Registrarse</button>
        <p class="switch">¿Ya tienes cuenta? <a href="<?php echo e(route('login.form')); ?>">Inicia sesión</a></p>
      </form>
      <a href="<?php echo e(route('dashboard')); ?>" class="back-dashboard">
          Volver al inicio
      </a>

      <style>
      .back-dashboard {
          display: block;           /* Para centrar con margin auto */
          text-align: center;       /* Centrar texto */
          margin: 20px auto 0 auto; /* Separación arriba y centrado horizontal */
          color: #333;
          text-decoration: none;
          font-weight: bold;
          font-size: 0.85rem;       /* Letra más pequeña */
          transition: all 0.3s ease;
      }

      .back-dashboard:hover {
          color: #f5c518;           /* Color dorado */
          transform: translateY(-2px); /* Pequeña animación al hover */
      }
      </style>

    </div>
  </div>

  <script>
    const form = document.getElementById('signupForm');
    const fields = ['nombre', 'apellido', 'rol_id', 'correo', 'password'];

    const pwInput = document.getElementById('password');
    const pwRequirements = {
      length: document.getElementById('pw-length'),
      uppercase: document.getElementById('pw-uppercase'),
      lowercase: document.getElementById('pw-lowercase'),
      number: document.getElementById('pw-number'),
      symbol: document.getElementById('pw-symbol')
    };

    function updatePasswordRequirements(value) {
      pwRequirements.length.className = value.length >= 8 ? 'valid' : 'invalid';
      pwRequirements.uppercase.className = /[A-Z]/.test(value) ? 'valid' : 'invalid';
      pwRequirements.lowercase.className = /[a-z]/.test(value) ? 'valid' : 'invalid';
      pwRequirements.number.className = /\d/.test(value) ? 'valid' : 'invalid';
      pwRequirements.symbol.className = /[!@#$%^&*()_+\-=[\]{}|;:'",.<>?/]/.test(value) ? 'valid' : 'invalid';
    }

    pwInput.addEventListener('input', (e) => {
      updatePasswordRequirements(e.target.value);
    });

    // Toggle contraseña
    const togglePassword = document.getElementById('togglePassword');

    togglePassword.addEventListener('click', () => {
      if(pwInput.type === 'password') {
        pwInput.type = 'text';
        togglePassword.classList.remove('bi-eye');
        togglePassword.classList.add('bi-eye-slash');
      } else {
        pwInput.type = 'password';
        togglePassword.classList.remove('bi-eye-slash');
        togglePassword.classList.add('bi-eye');
      }
    });


    // Validación en tiempo real backend
    async function validateField(field) {
      const value = document.getElementById(field).value;
      const token = document.querySelector('input[name="_token"]').value;
      const formData = new FormData();
      formData.append(field, value);
      formData.append('_token', token);

      try {
        const response = await fetch("<?php echo e(route('signup.validate')); ?>", {
          method: 'POST',
          headers: { 'Accept': 'application/json' },
          body: formData
        });
        const data = await response.json();
        const errorSpan = document.getElementById('error-' + field);
        errorSpan.textContent = data.errors && data.errors[field] ? data.errors[field][0] : '';
      } catch (error) {
        console.error('Error de validación:', error);
      }
    }

    fields.forEach(field => {
      document.getElementById(field).addEventListener('input', () => validateField(field));
      if(field === 'rol_id') {
        document.getElementById(field).addEventListener('change', () => validateField(field));
      }
    });

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(form);
      formData.append('_token', document.querySelector('input[name="_token"]').value);

      try {
        const response = await fetch("<?php echo e(route('signup')); ?>", {
          method: 'POST',
          headers: { 'Accept': 'application/json' },
          body: formData
        });
        const data = await response.json();

        if(response.status === 422) {
          Object.keys(data.errors).forEach(key => {
            const errorSpan = document.getElementById('error-' + key);
            if(errorSpan) errorSpan.textContent = data.errors[key][0];
          });
        } else if(response.ok) {
          Swal.fire({
            icon: 'success',
            title: '¡Registro exitoso!',
            text: data.message || 'Cuenta creada correctamente',
            showConfirmButton: true,
            confirmButtonText: 'Aceptar',
          });
          setTimeout(() => {
            window.location.href = "<?php echo e(route('login.form')); ?>";
          }, 2000);
        } else {
          Swal.fire({
            icon: 'error',
            title: '¡Error!',
            text: 'Ocurrió un problema inesperado. Intenta nuevamente.',
            showConfirmButton: true,
            confirmButtonText: 'Aceptar',
          });
        }

      } catch (error) {
        console.error('Error crítico:', error);
        Swal.fire({
          icon: 'error',
          title: '¡Error crítico!',
          text: 'No se pudo completar la acción. Revisa tu conexión o intenta más tarde.',
          showConfirmButton: true,
          confirmButtonText: 'Aceptar',
        });
      }
    });
  </script>
</body>
</html><?php /**PATH C:\laragon\www\ProyectoIII-A-LosSuchas\resources\views/signup.blade.php ENDPATH**/ ?>