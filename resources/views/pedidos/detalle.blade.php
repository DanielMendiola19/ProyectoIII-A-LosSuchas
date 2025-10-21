@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/historial_pedido/historial_pedido.css') }}">
<script src="{{ asset('js/historial_pedido/detalle_pedido.js') }}" defer></script>

<div class="container detalle-pedido">
    <h1>Detalle del Pedido #{{ $pedido->id }}</h1>

    <table class="tabla-detalle">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->detalles as $d)
                @php
                    // Toma el precio del detalle si existe; si no, cae al precio del producto
                    $unit = (float) ($d->precio_unit ?? $d->precio ?? $d->producto->precio ?? 0);
                    $cant = (int) ($d->cantidad ?? 0);
                    $linea = $unit * $cant;
                @endphp
                <tr>
                    <td data-label="Producto">{{ $d->producto->nombre ?? 'Producto eliminado' }}</td>
                    <td data-label="Cantidad">{{ $cant }}</td>
                    <td data-label="Precio Unitario">{{ number_format($unit, 2) }}</td>
                    <td data-label="Total">{{ number_format($linea, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $total = $pedido->detalles->sum(function($d){
            $unit = (float) ($d->precio_unit ?? $d->precio ?? ($d->producto->precio ?? 0));
            $cant = (int) ($d->cantidad ?? 0);
            return $unit * $cant;
        });
    @endphp
    <div class="total-resumen">
        Total del Pedido: <span id="totalPedido">{{ number_format($total, 2) }}</span> Bs
    </div>

    <a href="{{ route('pedidos.historial') }}" class="btn-volver">← Volver</a>
</div>
@endsection
