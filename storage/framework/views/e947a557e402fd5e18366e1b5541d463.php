<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recuperar contraseña | Coffeeology</title>
<link rel="stylesheet" href="<?php echo e(asset('css/stylesAuth.css')); ?>">
</head>
<body class="auth-body">
  <div class="auth-container">
    <div class="auth-card">
      <h1 class="auth-title">Recuperar contraseña</h1>

      
      <?php if(session('success')): ?>
        <div style="color: green; text-align:center; margin-bottom:10px;">
          <?php echo e(session('success')); ?>

        </div>
      <?php endif; ?>
      <?php if(session('error')): ?>
        <div style="color: red; text-align:center; margin-bottom:10px;">
          <?php echo e(session('error')); ?>

        </div>
      <?php endif; ?>

      <form action="<?php echo e(route('password.send')); ?>" method="POST" novalidate>
        <?php echo csrf_field(); ?>
        <div class="input-group">
          <label for="correo">Correo electrónico</label>
          <input type="email" name="correo" id="correo" placeholder="Ingresa tu correo" required value="<?php echo e(old('correo')); ?>">
          <?php $__errorArgs = ['correo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span class="error"><?php echo e($message); ?></span>
          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <button type="submit" class="btn">Enviar código</button>
      </form>

      <a href="<?php echo e(route('login.form')); ?>" class="back-dashboard">
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
<?php /**PATH C:\laragon\www\ProyectoIII-A-LosSuchas\resources\views/auth/forgot-password.blade.php ENDPATH**/ ?>