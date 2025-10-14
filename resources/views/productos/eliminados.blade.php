@extends('layouts.productos.app')

@section('title', 'Productos Eliminados')

@section('content')
<div class="container">
    <header>
        <h1><i class="fas fa-trash"></i> Productos Eliminados</h1>
        <a href="{{ route('productos.index') }}" class="btn btn-agregar">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </header>

    <section class="card">
        <h2><i class="fas fa-box"></i> Lista de Productos Eliminados</h2>
        <table class="tabla">
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Categoría</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $producto)
                <tr>
                    <td>
                        <img src="{{ $producto->imagen ? asset('storage/'.$producto->imagen) : asset('img/defecto.png') }}"
                             alt="{{ $producto->nombre }}"
                             style="width:60px; height:60px; object-fit:cover; border-radius:6px;">
                    </td>
                    <td>{{ $producto->nombre }}</td>
                    <td>{{ $producto->precio }} Bs</td>
                    <td>{{ $producto->categoria->nombre ?? 'Sin categoría' }}</td>
                    <td>
                        <form action="{{ route('productos.restaurar', $producto->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-agregar"><i class="fas fa-undo"></i> Restaurar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5">No hay productos eliminados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
</div>
@endsection
