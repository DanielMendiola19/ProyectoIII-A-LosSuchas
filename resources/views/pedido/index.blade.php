@extends('layouts.menu.app')

@section('title', 'Pedidos - Coffeeology')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pedido/pedido.css') }}">
<script src="{{ asset('js/pedido/pedido.js') }}" defer></script>

<div class="container my-5">
    <header>
        <h1><i class="fas fa-shopping-bag"></i> Realizar Pedido</h1>
    </header>

    <!-- Productos divididos por categoría -->
    @foreach ($productos->groupBy('categoria.nombre') as $categoriaNombre => $productosCategoria)
        <h2 class="categoria-titulo">{{ $categoriaNombre }}</h2>

        <div class="products-grid">
            @foreach ($productosCategoria as $producto)
                @if($producto->stock > 0)
                    @php
                        // Verifica si existe una imagen válida en storage
                        $rutaImagen = $producto->imagen ? 'storage/' . $producto->imagen : null;
                        $imagenValida = $rutaImagen && file_exists(public_path($rutaImagen));
                        $imagenFinal = $imagenValida ? asset($rutaImagen) : asset('img/defecto.png');
                    @endphp

                    <div class="product-card" 
                         data-id="{{ $producto->id }}" 
                         data-nombre="{{ $producto->nombre }}" 
                         data-precio="{{ $producto->precio }}"
                         data-imagen="{{ $imagenFinal }}"
                         data-stock="{{ $producto->stock }}">
                        
                        <div class="card h-100">
                            <img src="{{ $imagenFinal }}" 
                                 alt="{{ $producto->nombre ?? 'Producto sin nombre' }}" 
                                 class="card-img-top"
                                 onerror="this.onerror=null; this.src='{{ asset('img/defecto.png') }}';">
                            
                            <div class="card-body text-center">
                                <h5 class="card-title">{{ $producto->nombre }}</h5>
                                <p class="card-text">Bs {{ number_format($producto->precio, 2) }}</p>
                                <p class="card-text stock-info">Stock: {{ $producto->stock }}</p>
                            </div>

                            <button type="button" class="btn-agregar">
                                <i class="fas fa-cart-plus"></i> Agregar
                            </button>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endforeach
</div>

<!-- Botón flotante del carrito -->
<button id="btnCarrito" class="btn-carrito" type="button">
    <i class="fas fa-shopping-cart"></i>
    <span id="contadorCarrito">0</span>
</button>

<!-- Modal del carrito -->
<div id="modalCarrito" class="modal">
    <div class="modal-content">
        <h3><i class="fas fa-shopping-cart"></i> Carrito</h3>
        <div id="listaCarrito"></div>
        <div id="totalContainer">
            <strong>Total: Bs <span id="total">0.00</span></strong>
        </div>
        <div class="modal-actions">
            <button id="btnSiguiente" class="btn-principal" type="button">Siguiente</button>
            <button id="btnCerrarCarrito" class="btn-secundario" type="button">Cerrar</button>
        </div>
    </div>
</div>

<!-- Modal de selección de mesa -->
<!-- Modal de selección de mesa -->
<div id="modalMesa" class="modal">
    <div class="modal-content">
        <h3><i class="fas fa-chair"></i> Seleccione una mesa</h3>

        <div id="listaMesas" class="mesas-grid">
            @if($mesas->count() > 0)
                @foreach ($mesas as $mesa)
                    <div class="mesa-item {{ $mesa->estado === 'ocupada' ? 'ocupada' : 'disponible' }}"
                         data-id="{{ $mesa->id }}"
                         data-estado="{{ $mesa->estado }}">
                        <p>Mesa {{ $mesa->numero_mesa }}</p>
                        <small>Capacidad: {{ $mesa->capacidad }}</small>
                    </div>
                @endforeach
            @else
                <div class="carrito-vacio" style="grid-column: 1 / -1;">
                    <i class="fas fa-exclamation-triangle" style="color: var(--rojo-peligro);"></i>
                    <p>No hay mesas disponibles en este momento</p>
                </div>
            @endif
        </div>

        <div class="modal-actions">
            <button id="btnMesaSiguiente" class="btn-principal" disabled>Siguiente</button>
            <button id="btnCancelarMesa" class="btn-secundario">Cancelar</button>
        </div>
    </div>
</div>

<!-- Modal de método de pago -->
<div id="modalPago" class="modal">
    <div class="modal-content">
        <h3><i class="fas fa-credit-card"></i> Confirmar Pedido</h3>
        <form id="formPedido">
            <div class="form-group">
                <label for="metodo_pago">Seleccione un método de pago:</label>
                <select id="metodo_pago" name="metodo_pago" required>
                    @foreach ($metodosPago as $metodo)
                        <option value="{{ $metodo }}">{{ $metodo }}</option>
                    @endforeach
                </select>
            </div>

            <div class="modal-actions">
                <button type="submit" class="btn-principal">Confirmar Pedido</button>
                <button type="button" id="btnCancelarPago" class="btn-secundario">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Notificación -->
<div id="notificacionPedido" class="notificacion-pedido"></div>

@endsection
