@extends('layouts.app')
@section('title', 'Usuarios Eliminados - Coffeeology')
@section('content')

<link rel="stylesheet" href="{{ asset('css/usuarios/usuarios.css') }}">

<div class="container">

    <h1>Usuarios Eliminados</h1>

    <!-- Botón volver a usuarios -->
    <div style="text-align: center; margin-bottom: 20px;">
        <a href="{{ route('usuarios.index') }}" class="btn-eliminados" style="width: 300px; justify-content: center;">
            <i class="fas fa-arrow-left"></i> Volver a Usuarios
        </a>
    </div>

    <table class="tabla-usuarios">
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre completo</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Fecha de Eliminación</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>

        <tbody>
            @forelse($usuarios as $u)
                <tr>
                    <td>{{ $u->id }}</td>
                    <td>{{ $u->nombre }} {{ $u->apellido }}</td>
                    <td>{{ $u->correo }}</td>
                    <td>{{ $u->rol->nombre }}</td>
                    <td>{{ \Carbon\Carbon::parse($u->deleted_at)->format('d/m/Y H:i') }}</td>

                    <td class="text-center acciones">
                        <form action="{{ route('usuarios.restaurar', $u->id) }}" method="POST" class="form-restaurar-usuario">
                            @csrf
                            <button type="button" class="btn-confirmar abrir-modal-restaurar" 
                                    data-id="{{ $u->id }}" 
                                    data-nombre="{{ $u->nombre }} {{ $u->apellido }}">
                                <i class="fas fa-undo"></i> Restaurar
                            </button>
                        </form>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="6" class="vacio">No hay usuarios eliminados</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal de confirmación restaurar -->
<div id="modalConfirmacionRestaurar" class="modal-confirmacion modal-confirmacion-restaurar">
    <div class="modal-confirmacion-content">
        <div class="modal-confirmacion-icono">
            <i class="fas fa-undo"></i>
        </div>

        <h3 class="modal-confirmacion-titulo">¿Estás seguro?</h3>

        <p class="modal-confirmacion-mensaje" id="mensajeRestaurarUsuario">
            Esta acción restaurará al usuario al sistema.
        </p>

        <div class="modal-confirmacion-botones">
            <button id="btnConfirmarRestaurarUsuario" class="btn-confirmar-restaurar">
                <i class="fas fa-undo"></i> Sí, restaurar
            </button>
            <button id="btnCancelarRestaurarUsuario" class="btn-cancelar">
                <i class="fas fa-times"></i> Cancelar
            </button>
        </div>
    </div>
</div>

<script>
const modalRestaurar = document.getElementById("modalConfirmacionRestaurar");
const mensajeRestaurar = document.getElementById("mensajeRestaurarUsuario");
const btnConfirmarRestaurar = document.getElementById("btnConfirmarRestaurarUsuario");
const btnCancelarRestaurar = document.getElementById("btnCancelarRestaurarUsuario");

let formRestaurarActual = null;

document.querySelectorAll(".abrir-modal-restaurar").forEach(btn => {
    btn.addEventListener("click", () => {
        const nombre = btn.getAttribute("data-nombre");
        mensajeRestaurar.textContent = `¿Deseas restaurar a ${nombre}?`;
        formRestaurarActual = btn.closest("form");
        modalRestaurar.classList.add("show");
    });
});

btnConfirmarRestaurar.addEventListener("click", () => {
    if(formRestaurarActual) formRestaurarActual.submit();
});

btnCancelarRestaurar.addEventListener("click", () => {
    modalRestaurar.classList.remove("show");
});

modalRestaurar.addEventListener("click", e => {
    if (e.target === modalRestaurar) modalRestaurar.classList.remove("show");
});
</script>

@endsection
