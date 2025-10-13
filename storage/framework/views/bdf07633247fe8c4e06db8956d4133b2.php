<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verificar código | Coffeeology</title>
<link rel="stylesheet" href="<?php echo e(asset('css/stylesAuth.css')); ?>">
</head>
<body class="auth-body">
  <div class="auth-container">
    <div class="auth-card">
      <h1 class="auth-title">Ingresa el código</h1>
      <form action="<?php echo e(route('password.check.code')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="correo" value="<?php echo e(session('correo') ?? old('correo')); ?>">
        <div class="input-group">
          <label>Código de 6 dígitos</label>
          <div class="code-inputs">
            <input type="text" name="codigo1" maxlength="1" required>
            <input type="text" name="codigo2" maxlength="1" required>
            <input type="text" name="codigo3" maxlength="1" required>
            <input type="text" name="codigo4" maxlength="1" required>
            <input type="text" name="codigo5" maxlength="1" required>
            <input type="text" name="codigo6" maxlength="1" required>
          </div>
          <span class="error" id="error-codigo"></span>
        </div>
        <button type="submit" class="btn">Verificar código</button>
      </form>
    </div>
  </div>
</body>
</html>
<?php /**PATH C:\laragon\www\ProyectoIII-A-LosSuchas\resources\views/auth/verify-code.blade.php ENDPATH**/ ?>