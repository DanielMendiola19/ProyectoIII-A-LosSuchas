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
                <div class="product-card" 
                     data-id="{{ $producto->id }}" 
                     data-nombre="{{ $producto->nombre }}" 
                     data-precio="{{ $producto->precio }}"
                     data-imagen="{{ asset('storage/' . $producto->imagen) }}">
                    
                    <div class="card h-100">
                        <img src="{{ asset('storage/'.$producto->imagen) }}" alt="{{ $producto->nombre }}" class="card-img-top">
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ $producto->nombre }}</h5>
                            <p class="card-text">Bs {{ number_format($producto->precio, 2) }}</p>
                        </div>
                        <button type="button" class="btn-agregar">
                            <i class="fas fa-cart-plus"></i> Agregar
                        </button>
                    </div>
                </div>
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

<!-- Modal de método de pago -->
<div id="modalPago" class="modal">
    <div class="modal-content">
        <h3><i class="fas fa-credit-card"></i> Método de Pago</h3>
        <form id="formPedido">
            <label for="metodo_pago">Seleccione un método:</label>
            <select id="metodo_pago" name="metodo_pago" required>
                @foreach ($metodosPago as $metodo)
                    <option value="{{ $metodo }}">{{ $metodo }}</option>
                @endforeach
            </select>

            <div class="modal-actions">
                <button type="submit" class="btn-principal">Confirmar Pedido</button>
                <button type="button" id="btnCancelarPago" class="btn-secundario">Cancelar</button>
            </div>
        </form>
    </div>
</div>
@endsection
