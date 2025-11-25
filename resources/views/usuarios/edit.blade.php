@extends('layouts.app')
@section('title', 'Editar Usuario - Coffeeology')
@section('content')

<link rel="stylesheet" href="{{ asset('css/usuarios/usuarios.css') }}">

<div class="container editar-usuario">
    
    <h1>Editar Usuario</h1>

    <!-- Mostrar mensaje de éxito -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Mostrar mensaje de error general -->
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('usuarios.update', $usuario->id) }}" class="form-editar" id="form-editar-usuario">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $usuario->nombre) }}" 
                   class="@error('nombre') error-input @enderror" required>
            @error('nombre')
                <span class="input-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="apellido">Apellido</label>
            <input type="text" name="apellido" id="apellido" value="{{ old('apellido', $usuario->apellido) }}" 
                   class="@error('apellido') error-input @enderror" required>
            @error('apellido')
                <span class="input-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="correo">Correo</label>
            <input type="email" id="correo" value="{{ $usuario->correo }}" readonly class="readonly-input">
        </div>

        <div class="form-group">
            <label for="rol_id">Rol</label>
            <select name="rol_id" id="rol_id" class="@error('rol_id') error-input @enderror" required>
                <option value="">Selecciona un rol</option>
                @foreach($roles as $r)
                    <option value="{{ $r->id }}" {{ (old('rol_id', $usuario->rol_id) == $r->id) ? 'selected' : '' }}>
                        {{ $r->nombre }}
                    </option>
                @endforeach
            </select>
            @error('rol_id')
                <span class="input-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="botones">
            <button type="submit" class="btn-guardar">
                <i class="bi bi-check2"></i> Guardar cambios
            </button>
            <a href="{{ route('usuarios.index') }}" class="btn-volver">← Volver</a>
        </div>
    </form>

</div>

<!-- Agregar validación JavaScript en el frontend -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-editar-usuario');
    const nombre = document.getElementById('nombre');
    const apellido = document.getElementById('apellido');
    const rol = document.getElementById('rol_id');

    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Limpiar errores anteriores
        document.querySelectorAll('.input-error').forEach(error => {
            error.style.display = 'none';
        });
        document.querySelectorAll('.error-input').forEach(input => {
            input.classList.remove('error-input');
        });

        // Validar nombre
        if (nombre.value.trim().length < 2) {
            showError(nombre, 'El nombre debe tener al menos 2 caracteres');
            isValid = false;
        } else if (nombre.value.trim().length > 80) {
            showError(nombre, 'El nombre no puede superar 80 caracteres');
            isValid = false;
        } else if (!/^[A-Za-zÀ-ÿ]+(?: [A-Za-zÀ-ÿ]+)*$/.test(nombre.value.trim())) {
            showError(nombre, 'El nombre solo puede contener letras y un espacio entre palabras');
            isValid = false;
        }

        // Validar apellido
        if (apellido.value.trim().length < 2) {
            showError(apellido, 'El apellido debe tener al menos 2 caracteres');
            isValid = false;
        } else if (apellido.value.trim().length > 100) {
            showError(apellido, 'El apellido no puede superar 100 caracteres');
            isValid = false;
        } else if (!/^[A-Za-zÀ-ÿ]+(?: [A-Za-zÀ-ÿ]+)*$/.test(apellido.value.trim())) {
            showError(apellido, 'El apellido solo puede contener letras y un espacio entre palabras');
            isValid = false;
        }

        // Validar rol
        if (!rol.value) {
            showError(rol, 'Selecciona un rol');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    function showError(input, message) {
        input.classList.add('error-input');
        let errorSpan = input.parentNode.querySelector('.input-error');
        if (!errorSpan) {
            errorSpan = document.createElement('span');
            errorSpan.className = 'input-error';
            input.parentNode.appendChild(errorSpan);
        }
        errorSpan.textContent = message;
        errorSpan.style.display = 'block';
    }
});
</script>

@endsection