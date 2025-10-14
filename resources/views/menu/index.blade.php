@extends('layouts.menu.app')
@section('title', 'Menú Coffeeology')
@section('content')
<div class="container my-5">
    <header class="text-center mb-4">
        <h1><i class="fas fa-store"></i> Menú Coffeeology</h1>
    </header>

    @foreach ($categorias as $categoria)
        <h2 class="categoria-titulo mt-5 text-center">{{ $categoria->nombre }}</h2>

        <div class="products-grid">
            @forelse ($categoria->productos as $producto)
                <div class="product-card">
                    <div class="card h-100">
                        @if($producto->imagen)
                            <img src="{{ asset('storage/'.$producto->imagen) }}" class="card-img-top" alt="{{ $producto->nombre }}">
                        @else
                            <img src="{{ asset('images/default.jpg') }}" class="card-img-top" alt="Imagen no disponible">
                        @endif
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ $producto->nombre }}</h5>
                            <p class="card-text">{{ $producto->descripcion }}</p>
                        </div>
                        <div class="card-footer text-center">
                            <div class="precio">Bs {{ number_format($producto->precio, 2) }}</div>
                            <div class="stock-info">
                                <span class="stock-label">Stock:</span>
                                @if($producto->stock > 0)
                                    <span class="stock-cantidad stock-disponible">
                                        <span class="stock-indicator disponible"></span>
                                        {{ $producto->stock }}
                                    </span>
                                @else
                                    <span class="stock-cantidad stock-agotado">
                                        <span class="stock-indicator agotado"></span>
                                        Sin Stock
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted">No hay productos en esta categoría.</p>
            @endforelse
        </div>
    @endforeach
</div>
@endsection