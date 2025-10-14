@extends('layouts.app')

@section('title', 'Gestión de Mesas')

@section('content')
<link rel="stylesheet" href="{{ asset('css/mesas/mesa.css') }}">
<script src="{{ asset('js/mesas/mesa.js') }}" defer></script>

<div class="container-mesas">
    <h1><i class= "fas fa-chair"></i> Gestión de Mesas</h1>

    <img src="{{ asset('img/mesas/ubicacion.png') }}" alt="Ubicación de Mesas" class="mesas-img">

    <div class="stats">
        <span class="disponibles">Disponibles: {{ $disponibles }}</span>
        <span class="ocupadas">Ocupadas: {{ $ocupadas }}</span>
    </div>

    <form id="formMesa" method="POST" action="{{ route('mesas.store') }}">
        @csrf
        <div>
            <label for="numero_mesa">Número de Mesa:</label>
            <input type="text" id="numero_mesa" name="numero_mesa" required>
            <span id="errorNumero" class="input-error"></span>
        </div>
        <div>
            <label for="capacidad">Capacidad:</label>
            <input type="number" id="capacidad" name="capacidad" required min="2" max="6">
            <span id="errorCapacidad" class="input-error"></span>
        </div>
        <div>
            <label for="estado">Estado:</label>
            <select name="estado" required>
                <option value="disponible">Disponible</option>
                <option value="ocupada">Ocupada</option>
            </select>
        </div>
        <button type="submit" class="btn btn-agregar">Agregar Mesa</button>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>N° Mesa</th>
                <th>Capacidad</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mesas as $mesa)
                <tr>
                    <td>{{ $mesa->numero_mesa }}</td>
                    <td>{{ $mesa->capacidad }}</td>
                    <td class="{{ $mesa->estado === 'ocupada' ? 'estado-ocupada' : 'estado-disponible' }}">
                        {{ ucfirst($mesa->estado) }}
                    </td>
                    <td>
                        <button class="btn btn-editar" data-mesa='@json($mesa)'>Editar</button>
                        <form action="{{ route('mesas.destroy', $mesa) }}" method="POST" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-eliminar">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal para editar -->
<div id="modalEditar" class="modal">
    <div class="modal-content">
        <h3>Editar Mesa</h3>
        <form id="formEditar" method="POST">
            @csrf @method('PUT')
            <div>
                <label for="edit_numero_mesa">Número de Mesa:</label>
                <input type="text" id="edit_numero_mesa" name="numero_mesa" readonly>
            </div>
            <div>
                <label for="edit_capacidad">Capacidad:</label>
                <input type="number" id="edit_capacidad" name="capacidad" required min="2" max="6">
                <span id="errorEditarCapacidad" class="input-error"></span>
            </div>
            <div>
                <label for="edit_estado">Estado:</label>
                <select id="edit_estado" name="estado" required>
                    <option value="disponible">Disponible</option>
                    <option value="ocupada">Ocupada</option>
                </select>
            </div>
            <button type="submit" class="btn btn-agregar">Guardar Cambios</button>
            <button type="button" id="cerrarModal" class="btn btn-eliminar">Cancelar</button>
        </form>
    </div>
</div>

<div id="notificacion" class="notificacion"></div>
@endsection
