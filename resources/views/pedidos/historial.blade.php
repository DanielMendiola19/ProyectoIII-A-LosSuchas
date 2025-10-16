@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/historial_pedido/historial_pedido.css') }}">

<div class="container">
    <h1>Historial de Pedidos</h1>

    {{-- Formulario de filtros --}}
    <form method="GET" action="{{ route('pedidos.historial') }}" class="form-filtro">
        <label for="estado">Estado:</label>
        <select name="estado" id="estado">
            <option value="">Todos</option>
            <option value="pendiente"      {{ request('estado')=='pendiente' ? 'selected' : '' }}>Pendiente</option>
            <option value="en preparación" {{ request('estado')=='en preparación' ? 'selected' : '' }}>En preparación</option>
            <option value="listo"          {{ request('estado')=='listo' ? 'selected' : '' }}>Listo</option>
            <option value="entregado"      {{ request('estado')=='entregado' ? 'selected' : '' }}>Entregado</option>
        </select>

        <label for="fecha">Fecha:</label>
        <input type="date" name="fecha" id="fecha" value="{{ request('fecha') }}">

        <button type="submit" class="btn-filtrar">Filtrar</button>
        <a href="{{ route('pedidos.historial') }}" class="btn-limpiar">Limpiar</a>
    </form>

    <table class="tabla-historial">
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Mesa</th>
                <th>Estado</th>
                <th class="text-end">Total (Bs)</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($pedidos) && count($pedidos) > 0)
                @foreach($pedidos as $p)
                    <tr>
                        <td data-label="#">{{ $p->id }}</td>
                        <td data-label="Fecha">{{ optional($p->created_at)->format('Y-m-d H:i') }}</td>
                        <td data-label="Mesa">
                            {{ optional($p->mesa)->numero ?? ($p->mesa ?? '-') }}
                        </td>
                        <td data-label="Estado">
                            <form action="{{ route('pedidos.estado', $p->id) }}" method="POST" class="form-estado">
                                @csrf
                                @method('PUT')
                                <select name="estado" class="estado-select">
                                    @php $estado = strtolower($p->estado ?? 'pendiente'); @endphp
                                    <option value="pendiente"      {{ $estado==='pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="en preparación" {{ $estado==='en preparación' ? 'selected' : '' }}>En preparación</option>
                                    <option value="listo"          {{ $estado==='listo' ? 'selected' : '' }}>Listo</option>
                                    <option value="entregado"      {{ $estado==='entregado' ? 'selected' : '' }}>Entregado</option>
                                </select>

                                {{-- Botón confirmar --}}
                                <button type="submit" class="btn-confirmar" title="Confirmar cambio de estado">
                                    <i class="bi bi-check-circle"></i> Confirmar
                                </button>
                            </form>
                        </td>
                        <td data-label="Total (Bs)" class="text-end">
                            {{ number_format($p->total ?? 0, 2) }}
                        </td>
                        <td data-label="Acciones" class="text-center">
                            <a href="{{ route('pedidos.detalle', $p->id) }}" class="btn-ver" title="Ver detalles">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" class="vacio">No hay pedidos registrados.</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>



@endsection
