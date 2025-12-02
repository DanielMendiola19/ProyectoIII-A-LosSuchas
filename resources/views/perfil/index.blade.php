@extends('layouts.app')

@section('title', 'Mi Perfil - Coffeeology')

@section('content')
<link rel="stylesheet" href="{{ asset('css/perfil/perfil.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
            <div class="form-group password-container">
                <label for="current_password">Contraseña Actual</label>
                <div class="input-with-icon">
                    <input type="password" name="current_password" id="current_password" class="form-control" required>
                    <i id="toggleCurrentPassword" class="bi bi-eye toggle-password"></i>
                </div>
                @error('current_password') 
                    <small class="error-message">{{ $message }}</small> 
                @enderror
            </div>

            <!-- Nueva Contraseña -->
            <div class="form-group password-container">
                <label for="password">Nueva Contraseña</label>
                <div class="input-with-icon">
                    <input type="password" name="password" id="password" class="form-control" required>
                    <i id="togglePassword" class="bi bi-eye toggle-password"></i>
                </div>
                @error('password') 
                    <small class="error-message">{{ $message }}</small> 
                @enderror

                <div id="password-requirements" class="password-requirements">
                    <span id="pw-length" class="invalid">Mínimo 8 caracteres</span>
                    <span id="pw-uppercase" class="invalid">Al menos 1 mayúscula</span>
                    <span id="pw-number" class="invalid">Al menos 1 número</span>
                    <span id="pw-symbol" class="invalid">Al menos 1 símbolo (!@#$%^&*)</span>
                </div>
            </div>

            <!-- Confirmar Nueva Contraseña -->
            <div class="form-group password-container">
                <label for="password_confirmation">Confirmar Nueva Contraseña</label>
                <div class="input-with-icon">
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                    <i id="togglePasswordConfirm" class="bi bi-eye toggle-password"></i>
                </div>
                <div id="pw-match" class="password-match">Las contraseñas no coinciden</div>
            </div>

            <div class="perfil-acciones">
                <button type="submit" class="btn-principal"><i class="fas fa-save"></i> Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- Contenedor de notificaciones -->
<div id="notifications-container"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const toggleCurrentPw = document.getElementById('toggleCurrentPassword');
    const togglePw = document.getElementById('togglePassword');
    const togglePwConfirm = document.getElementById('togglePasswordConfirm');
    const currentPwInput = document.getElementById('current_password');
    const pwInput = document.getElementById('password');
    const pwConfirmInput = document.getElementById('password_confirmation');
    const pwRequirementsDiv = document.getElementById('password-requirements');
    const pwMatch = document.getElementById('pw-match');
    const notificationsContainer = document.getElementById('notifications-container');
    
    const pwRequirements = {
        length: document.getElementById('pw-length'),
        uppercase: document.getElementById('pw-uppercase'),
        number: document.getElementById('pw-number'),
        symbol: document.getElementById('pw-symbol'),
    };

    // Toggle visibilidad de contraseñas
    function setupPasswordToggle(toggle, input) {
        toggle.addEventListener('click', () => {
            if (input.type === 'password') {
                input.type = 'text';
                toggle.classList.remove('bi-eye');
                toggle.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                toggle.classList.remove('bi-eye-slash');
                toggle.classList.add('bi-eye');
            }
        });
    }

    // Configurar toggles
    setupPasswordToggle(toggleCurrentPw, currentPwInput);
    setupPasswordToggle(togglePw, pwInput);
    setupPasswordToggle(togglePwConfirm, pwConfirmInput);

    // Mostrar requisitos al enfocar
    pwInput.addEventListener('focus', () => {
        pwRequirementsDiv.style.display = 'block';
    });

    pwInput.addEventListener('blur', () => {
        if (!pwInput.value) {
            pwRequirementsDiv.style.display = 'none';
        }
    });

    // Validación en tiempo real de requisitos
    pwInput.addEventListener('input', () => {
        const val = pwInput.value;
        
        // Actualizar indicadores visuales
        pwRequirements.length.classList.toggle('valid', val.length >= 8);
        pwRequirements.uppercase.classList.toggle('valid', /[A-Z]/.test(val));
        pwRequirements.number.classList.toggle('valid', /\d/.test(val));
        pwRequirements.symbol.classList.toggle('valid', /[!@#$%^&*]/.test(val));

        // Validar coincidencia
        validatePasswordMatch();
    });

    // Validación de coincidencia
    pwConfirmInput.addEventListener('input', validatePasswordMatch);

    function validatePasswordMatch() {
        if (pwConfirmInput.value.length > 0) {
            pwMatch.style.display = 'block';
            const isMatch = pwInput.value === pwConfirmInput.value;
            pwMatch.classList.toggle('valid', isMatch);
            pwMatch.classList.toggle('invalid', !isMatch);
            pwMatch.textContent = isMatch ? 'Las contraseñas coinciden' : 'Las contraseñas no coinciden';
        } else {
            pwMatch.style.display = 'none';
        }
    }

    // Función para mostrar notificaciones
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        
        const icon = type === 'success' ? 
            '<i class="fas fa-check-circle"></i>' : 
            '<i class="fas fa-exclamation-circle"></i>';
        
        notification.innerHTML = `
            <div class="notification-content">
                <div class="notification-icon">${icon}</div>
                <div class="notification-message">${message}</div>
                <button class="notification-close">&times;</button>
            </div>
        `;

        notificationsContainer.appendChild(notification);

        // Animación de entrada
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        // Cerrar notificación al hacer clic en la X
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => {
            closeNotification(notification);
        });

        // Auto-cerrar después de 5 segundos
        setTimeout(() => {
            if (notification.parentNode) {
                closeNotification(notification);
            }
        }, 5000);
    }

    function closeNotification(notification) {
        notification.classList.remove('show');
        notification.classList.add('hide');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }

    // Mostrar notificaciones automáticamente si hay mensajes en la sesión
    @if(session('success_modal'))
        showNotification("{{ session('success_modal') }}", 'success');
    @endif

    @if(session('error_modal'))
        showNotification("{{ session('error_modal') }}", 'error');
    @endif
});
</script>
@endsection