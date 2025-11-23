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
                    // 🔹 MEJORADO: Manejo más robusto de productos eliminados
                    $producto = $d->producto;
                    $nombreProducto = $producto ? $producto->nombre : 'Producto no disponible';
                    $unit = (float) ($d->precio_unitario ?? $d->precio_unit ?? $producto->precio ?? 0);
                    $cant = (int) ($d->cantidad ?? 0);
                    $linea = $unit * $cant;
                    
                    // 🔹 Clase para productos eliminados
                    $claseFila = $producto && $producto->trashed() ? 'producto-eliminado' : '';
                @endphp
                <tr class="{{ $claseFila }}">
                    <td data-label="Producto">
                        {{ $nombreProducto }}
                        @if($producto && $producto->trashed())
                            <span class="badge-eliminado"></span>
                        @endif
                    </td>
                    <td data-label="Cantidad">{{ $cant }}</td>
                    <td data-label="Precio Unitario">{{ number_format($unit, 2) }} Bs</td>
                    <td data-label="Total">{{ number_format($linea, 2) }} Bs</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $total = $pedido->detalles->sum(function($d){
            $unit = (float) ($d->precio_unitario ?? $d->precio_unit ?? ($d->producto->precio ?? 0));
            $cant = (int) ($d->cantidad ?? 0);
            return $unit * $cant;
        });
    @endphp
    <div class="total-resumen">
        Total del Pedido: <span id="totalPedido">{{ number_format($total, 2) }}</span> Bs
    </div>

    <a href="{{ route('pedidos.historial') }}" class="btn-volver">← Volver al Historial</a>
</div>
@endsection