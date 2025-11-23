@extends('layouts.app')

@section('title', 'Mi Perfil - Coffeeology')

@section('content')
<link rel="stylesheet" href="{{ asset('css/perfil/perfil.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<div class="perfil-container">
    <h1><i class="fas fa-user-circle"></i> Mi Perfil</h1>

    <div class="perfil-card">
        <div class="perfil-info">
            <p><strong>Nombre:</strong> {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}</p>
            <p><strong>Correo:</strong> {{ Auth::user()->correo }}</p>
            <p><strong>Rol:</strong> {{ Auth::user()->rol->nombre }}</p>
            <p><strong>Fecha de Registro:</strong> {{ Auth::user()->created_at->format('d/m/Y') }}</p>
        </div>

        @if(Auth::user()->rol->nombre == 'Mesero' || Auth::user()->rol->nombre == 'Cajero')
            <div class="perfil-pedidos">
                <h3><i class="fas fa-shopping-bag"></i> Tus Pedidos</h3>
                <p>Total: {{ Auth::user()->pedidos->count() }}</p>
                @if(Auth::user()->pedidos->count() > 0)
                    <ul>
                        @foreach(Auth::user()->pedidos->take(3) as $pedido)
                            <li>Pedido #{{ $pedido->id }} - {{ $pedido->estado }} - {{ $pedido->created_at->format('d/m/Y H:i') }}</li>
                        @endforeach
                    </ul>
                @else
                    <p>No has realizado pedidos aún.</p>
                @endif
            </div>
        @endif

        <hr style="margin: 25px 0; border-color: rgba(230,179,37,0.3)">

        <h3><i class="fas fa-key"></i> Cambiar Contraseña</h3>
        <form id="form-cambiar-password" action="{{ route('perfil.cambiarPassword') }}" method="POST">
            @csrf

            <!-- Contraseña Actual -->
            <div class="form-group password-container" style="position: relative;">
                <label for="current_password">Contraseña Actual</label>
                <input type="password" name="current_password" id="current_password" class="form-control" required style="padding-right: 40px;">
                <i id="toggleCurrentPassword" class="bi bi-eye toggle-password" style="position: absolute; right: 10px; top: 38px; cursor: pointer;"></i>
                @error('current_password') <small style="color: var(--rojo-peligro)">{{ $message }}</small> @enderror
            </div>

            <!-- Nueva Contraseña -->
            <div class="form-group password-container" style="position: relative;">
                <label for="password">Nueva Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" required style="padding-right: 40px;">
                <i id="togglePassword" class="bi bi-eye toggle-password" style="position: absolute; right: 10px; top: 38px; cursor: pointer;"></i>
                @error('password') <small style="color: var(--rojo-peligro)">{{ $message }}</small> @enderror

                <div id="password-requirements" style="font-size:0.85rem; margin-top:5px; display:none;">
                    <span id="pw-length" class="invalid">Mínimo 8 caracteres</span><br>
                    <span id="pw-uppercase" class="invalid">Al menos 1 mayúscula</span><br>
                    <span id="pw-number" class="invalid">Al menos 1 número</span><br>
                    <span id="pw-symbol" class="invalid">Al menos 1 símbolo (!@#$%^&*)</span>
                </div>
            </div>

            <!-- Confirmar Nueva Contraseña -->
            <div class="form-group password-container" style="position: relative;">
                <label for="password_confirmation">Confirmar Nueva Contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required style="padding-right: 40px;">
                <i id="togglePasswordConfirm" class="bi bi-eye toggle-password" style="position: absolute; right: 10px; top: 38px; cursor: pointer;"></i>
                <div id="pw-match" style="font-size:0.85rem; margin-top:5px; color:red; display:none;">Las contraseñas no coinciden</div>
            </div>

            <div class="perfil-acciones">
                <button type="submit" class="btn-principal"><i class="fas fa-save"></i> Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
    const toggleCurrentPw = document.getElementById('toggleCurrentPassword');
    const togglePw = document.getElementById('togglePassword');
    const togglePwConfirm = document.getElementById('togglePasswordConfirm');

    const currentPwInput = document.getElementById('current_password');
    const pwInput = document.getElementById('password');
    const pwConfirmInput = document.getElementById('password_confirmation');

    const pwRequirementsDiv = document.getElementById('password-requirements');
    const pwRequirements = {
        length: document.getElementById('pw-length'),
        uppercase: document.getElementById('pw-uppercase'),
        number: document.getElementById('pw-number'),
        symbol: document.getElementById('pw-symbol'),
    };
    const pwMatch = document.getElementById('pw-match');

    // Toggle ojos
    [[toggleCurrentPw, currentPwInput],[togglePw, pwInput],[togglePwConfirm, pwConfirmInput]].forEach(([toggle, input])=>{
        toggle.addEventListener('click', ()=>{
            if(input.type==='password'){
                input.type='text';
                toggle.classList.remove('bi-eye'); toggle.classList.add('bi-eye-slash');
            } else{
                input.type='password';
                toggle.classList.remove('bi-eye-slash'); toggle.classList.add('bi-eye');
            }
        });
    });

    // Mostrar requisitos al enfocar
    pwInput.addEventListener('focus', ()=>{ pwRequirementsDiv.style.display = 'block'; });
    pwInput.addEventListener('blur', ()=>{ if(!pwInput.value) pwRequirementsDiv.style.display = 'none'; });

    // Validación en tiempo real de requisitos
    pwInput.addEventListener('input', ()=>{
        const val = pwInput.value;
        pwRequirements.length.style.color = val.length >= 8 ? 'green' : 'red';
        pwRequirements.uppercase.style.color = /[A-Z]/.test(val) ? 'green' : 'red';
        pwRequirements.number.style.color = /\d/.test(val) ? 'green' : 'red';
        pwRequirements.symbol.style.color = /[!@#$%^&*]/.test(val) ? 'green' : 'red';

        // Validar coincidencia solo si ya escribió confirmación
        if(pwConfirmInput.value.length > 0){
            pwMatch.style.display = 'block';
            pwMatch.style.color = val === pwConfirmInput.value ? 'green' : 'red';
            pwMatch.textContent = val === pwConfirmInput.value ? 'Las contraseñas coinciden' : 'Las contraseñas no coinciden';
        }
    });

    // Validación de coincidencia
    pwConfirmInput.addEventListener('input', ()=>{
        if(pwConfirmInput.value.length > 0){
            pwMatch.style.display = 'block';
            pwMatch.style.color = pwInput.value === pwConfirmInput.value ? 'green' : 'red';
            pwMatch.textContent = pwInput.value === pwConfirmInput.value ? 'Las contraseñas coinciden' : 'Las contraseñas no coinciden';
        } else {
            pwMatch.style.display = 'none';
        }
    });
</script>
@endsection
