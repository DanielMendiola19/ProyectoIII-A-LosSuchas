<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verificar código | Coffeeology</title>
<link rel="stylesheet" href="<?php echo e(asset('css/stylesAuth.css')); ?>">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  .resend {
    text-align: center;
    margin-top: 15px;
  }
  .resend button {
    background: none;
    border: none;
    color: var(--dorado);
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s ease;
  }
  .resend button:hover:not(:disabled) {
    color: #f1c40f;
    transform: scale(1.05);
  }
  .resend button:disabled {
    color: gray;
    cursor: not-allowed;
  }
  .use-another button {
    color: #c0392b;
    font-weight: bold;
    background: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  .use-another button:hover {
    color: #e74c3c;
    transform: scale(1.05);
  }
  .toast-success {
    background: #27ae60;
    color: #fff;
  }
  .toast-error {
    background: #c0392b;
    color: #fff;
  }
</style>
</head>

<body class="auth-body">
  <div class="auth-container">
    <div class="auth-card">
      <h1 class="auth-title">Ingresa el código</h1>

      
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

      <form id="verifyForm" action="<?php echo e(route('password.check.code')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="correo" value="<?php echo e(session('correo_recuperacion') ?? old('correo')); ?>">
        <div class="input-group">
          <label>Código de 6 dígitos</label>
          <div class="code-inputs">
            <?php for($i = 1; $i <= 6; $i++): ?>
              <input type="text" name="codigo<?php echo e($i); ?>" maxlength="1" required>
            <?php endfor; ?>
          </div>
          <span class="error" id="error-codigo"></span>
          
        </div>
        <button type="submit" class="btn">Verificar código</button>
          <p style="text-align:center; color:#aaa; font-size:0.9rem; margin-top:10px;">
            El código expira en <strong>3 minutos</strong>.
          </p>

      </form>

      <div class="resend">
        <button id="resendBtn" disabled>
          Reenviar código <span id="countdown">(15s)</span>
        </button>
      </div>

      <div class="resend use-another">
          <form action="<?php echo e(route('password.clear.session')); ?>" method="POST" style="margin-top:10px;">
              <?php echo csrf_field(); ?>
              <button type="submit">Usar otro correo</button>
          </form>
      </div>
    </div>
  </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const inputs = document.querySelectorAll('.code-inputs input');
  const resendBtn = document.getElementById('resendBtn');
  const countdownEl = document.getElementById('countdown');
  let timer = null;

  // ===== Auto avanzar y retroceder =====
  inputs.forEach((input, i) => {
    input.addEventListener('input', e => {
      const value = e.target.value;
      if (!/^\d$/.test(value)) {
        e.target.value = '';
        return;
      }
      if (value && i < inputs.length - 1) inputs[i + 1].focus();
    });

    input.addEventListener('keydown', e => {
      if (e.key === 'Backspace' && !input.value && i > 0) inputs[i - 1].focus();
    });

    input.addEventListener('paste', e => {
      e.preventDefault();
      const paste = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
      for (let j = 0; j < paste.length; j++) {
        if (inputs[j]) inputs[j].value = paste[j];
      }
      if (paste.length < 6) {
        inputs[paste.length]?.focus();
      } else {
        inputs[5].focus();
      }
    });
  });

  // ===== Countdown =====
  function startCountdown() {
    let countdown = 15;
    resendBtn.disabled = true;
    countdownEl.style.display = "inline"; // mostrar contador
    countdownEl.textContent = `(${countdown}s)`; // inicializar con paréntesis

    if (timer) clearInterval(timer);
    timer = setInterval(() => {
      countdown--;
      if (countdown > 0) {
        countdownEl.textContent = `(${countdown}s)`;
      } else {
        clearInterval(timer);
        resendBtn.disabled = false;
        countdownEl.style.display = "none"; // ocultar contador
      }
    }, 1000);
  }



  startCountdown();

  // ===== SweetAlert Toasts =====
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
  });

  // ===== Reenviar código =====
  resendBtn.addEventListener('click', async () => {
    startCountdown();

    const token = document.querySelector('input[name="_token"]').value;

    try {
      const res = await fetch("<?php echo e(route('password.resend')); ?>", {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': token
        },
      });

      const data = await res.json();
      if (data.success) {
        Toast.fire({
          icon: 'success',
          title: 'Se ha enviado un nuevo código de verificación.'
        });
      } else {
        Toast.fire({
          icon: 'error',
          title: 'Ocurrió un error al generar el nuevo código.'
        });
      }

    } catch (err) {
      Toast.fire({
        icon: 'error',
        title: 'Error al contactar con el servidor.'
      });
      console.error(err);
    }
  });
});

</script>

</body>
</html>
<?php /**PATH C:\laragon\www\ProyectoIII-A-LosSuchas\resources\views/auth/verify-code.blade.php ENDPATH**/ ?>