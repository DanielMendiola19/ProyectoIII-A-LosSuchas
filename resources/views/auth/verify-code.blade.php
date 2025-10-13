<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verificar código | Coffeeology</title>
<link rel="stylesheet" href="{{ asset('css/stylesAuth.css') }}">
<style>
.resend {
  text-align: center;
  margin-top: 15px;
}
.resend button, .resend a {
  background: none;
  border: none;
  color: var(--dorado);
  cursor: pointer;
  font-weight: bold;
}
.resend button:disabled {
  color: gray;
  cursor: not-allowed;
}
</style>
</head>
<body class="auth-body">
  <div class="auth-container">
    <div class="auth-card">
      <h1 class="auth-title">Ingresa el código</h1>

      {{-- Mensajes --}}
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

      <form id="verifyForm" action="{{ route('password.check.code') }}" method="POST">
        @csrf
        <input type="hidden" name="correo" value="{{ session('correo') ?? old('correo') }}">
        <div class="input-group">
          <label>Código de 6 dígitos</label>
          <div class="code-inputs">
            @for($i = 1; $i <= 6; $i++)
              <input type="text" name="codigo{{$i}}" maxlength="1" required>
            @endfor
          </div>
          <span class="error" id="error-codigo"></span>
        </div>
        <button type="submit" class="btn">Verificar código</button>
      </form>

      <div class="resend">
        <button id="resendBtn" disabled>Reenviar código (<span id="countdown">15</span>s)</button>
      </div>
    </div>
  </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const inputs = document.querySelectorAll('.code-inputs input');
    const form = document.getElementById('verifyForm');
    const resendBtn = document.getElementById('resendBtn');
    const countdownEl = document.getElementById('countdown');

    // ===== Auto avanzar y retroceder
    inputs.forEach((input, i) => {
        input.addEventListener('input', e => {
            if(e.inputType !== 'deleteContentBackward' && input.value.length === 1 && i < inputs.length - 1){
                inputs[i+1].focus();
            }
        });

        input.addEventListener('keydown', e => {
            // Retroceder con Backspace
            if(e.key === 'Backspace' && !input.value && i > 0){
                inputs[i-1].focus();
            }
            // Retroceder con Enter
            if(e.key === 'Enter' && i > 0 && !input.value){
                inputs[i-1].focus();
                e.preventDefault();
            }
        });

        // ===== Copiar/Pegar completo
        input.addEventListener('paste', e => {
            e.preventDefault();
            let paste = e.clipboardData.getData('text').slice(0,6);
            for(let j=0;j<6;j++){
                if(inputs[j]) inputs[j].value = paste[j] || '';
            }
            if(inputs[5].value) inputs[5].focus();
        });
    });

    // ===== Countdown para reenviar
    let countdown = 15;
    let timer = setInterval(() => {
        countdown--;
        countdownEl.textContent = countdown;
        if(countdown <= 0){
            clearInterval(timer);
            resendBtn.disabled = false;
            countdownEl.parentElement.textContent = 'Reenviar código';
        }
    },1000);

    resendBtn.addEventListener('click', async () => {
        resendBtn.disabled = true;
        countdown = 15;
        countdownEl.textContent = countdown;
        countdownEl.parentElement.innerHTML = 'Reenviar código (<span id="countdown">15</span>s)';
        timer = setInterval(() => {
            countdown--;
            countdownEl.textContent = countdown;
            if(countdown <= 0){
                clearInterval(timer);
                resendBtn.disabled = false;
                countdownEl.parentElement.textContent = 'Reenviar código';
            }
        },1000);

        // 🔹 Llamada POST para generar token de nuevo
        const correo = document.querySelector('input[name="correo"]').value;
        const token = document.querySelector('input[name="_token"]').value;
        try {
            const res = await fetch("{{ route('password.send') }}", {
                method: 'POST',
                headers: {'Accept':'application/json','X-CSRF-TOKEN': token},
                body: new URLSearchParams({correo})
            });
            const data = await res.json();
            if(data.token){
                alert('Se generó un nuevo código (temporal)');
            }
        } catch(err){
            alert('Error al generar nuevo código.');
            console.error(err);
        }
    });
});

</script>
</body>
</html>
