@extends('layouts.app')
@section('title', 'Gestión de Usuarios - Coffeeology')
@section('content')

<link rel="stylesheet" href="{{ asset('css/usuarios/usuarios.css') }}">

<div class="container">
    <h1>Gestión de Usuarios</h1>

    {{-- Botón para ver eliminados --}}
    <div class="mb-3">
        <a href="{{ route('usuarios.eliminados') }}" class="btn-eliminados">
            <i class="fas fa-trash-restore"></i> Usuarios Eliminados
        </a>

    </div>

    {{-- FILTROS --}}
    <form method="GET" action="{{ route('usuarios.index') }}" class="form-filtro">
        <div class="filtro-grupo">
            <label for="rol">Rol:</label>
            <select name="rol" id="rol">
                <option value="">Todos</option>
                @foreach($roles as $r)
                    <option value="{{ $r->id }}" {{ $rolFiltro == $r->id ? 'selected' : '' }}>
                        {{ $r->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="botones-filtro">
            <button type="submit" class="btn-filtrar">
                <i class="bi bi-funnel"></i> Filtrar
            </button>
            <a href="{{ route('usuarios.index') }}" class="btn-limpiar">
                <i class="bi bi-arrow-clockwise"></i> Limpiar
            </a>
        </div>
    </form>

    {{-- TABLA --}}
    <table class="tabla-usuarios">
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre completo</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Fecha de Registro</th>
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
                    <td>{{ \Carbon\Carbon::parse($u->created_at)->format('d/m/Y H:i') }}</td>

                    <td class="text-center acciones">
                        <a href="{{ route('usuarios.edit', $u->id) }}" class="btn-editar">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        <form action="{{ route('usuarios.destroy', $u->id) }}" method="POST" class="form-eliminar-usuario">
                            @csrf @method('DELETE')
                            <button type="button" class="btn-eliminar abrir-modal" data-id="{{ $u->id }}" data-nombre="{{ $u->nombre }} {{ $u->apellido }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="6" class="vacio">No hay usuarios registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal de confirmación -->
<div id="modalConfirmacionUsuario" class="modal-confirmacion">
    <div class="modal-confirmacion-content">
        <div class="modal-confirmacion-icono">
            <i class="fas fa-exclamation-triangle"></i>
        </div>

        <h3 class="modal-confirmacion-titulo">¿Estás seguro?</h3>

        <p class="modal-confirmacion-mensaje" id="mensajeEliminarUsuario">
            Esta acción eliminará al usuario del sistema.
        </p>

        <div class="modal-confirmacion-botones">
            <button id="btnConfirmarEliminarUsuario" class="btn-confirmar">
                <i class="fas fa-trash"></i> Sí, eliminar
            </button>
            <button id="btnCancelarEliminarUsuario" class="btn-cancelar">
                <i class="fas fa-times"></i> Cancelar
            </button>
        </div>
    </div>
</div>

<script src="{{ asset('js/usuarios/usuarios.js') }}"></script>

@endsection
