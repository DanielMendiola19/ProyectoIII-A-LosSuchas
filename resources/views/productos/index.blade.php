@extends('layouts.productos.app')

@section('title', 'Gestión de Productos Coffeeology')

@section('content')
<div class="container">
    <header>
        <h1><i class="fas fa-coffee"></i> Gestión de Productos Coffeeology</h1>
    </header>

    <!-- Estadísticas -->
    <div class="estadisticas">
        <div class="estadistica">
            <div class="valor" id="totalProductos">{{ $productos->count() }}</div>
            <div class="etiqueta">Total Productos</div>
        </div>
        <div class="estadistica">
            <div class="valor" id="valorInventario">
                {{ $productos->sum(fn($p) => $p->precio) }} Bs
            </div>
            <div class="etiqueta">Valor Inventario</div>
        </div>
        <div class="estadistica">
            <div class="valor" id="productosBebidas">
                {{ $productos->where('categoria.nombre', 'bebidas')->count() }}
            </div>
            <div class="etiqueta">Productos Bebidas</div>
        </div>
    </div>

    <!-- Formulario de productos -->
    <section class="card formulario">
        <h2><i class="fas fa-plus-circle"></i> Agregar Nuevo Producto</h2>
        <form id="productForm" action="{{ route('productos.store') }}" method="POST">
            @csrf
            <label for="nombre"><i class="fas fa-tag"></i> Nombre del producto:</label>
            <input type="text" id="nombre" name="nombre" placeholder="Ej: Café Americano" required>

            <label for="precio"><i class="fas fa-dollar-sign"></i> Precio (Bs):</label>
            <input type="number" id="precio" name="precio" placeholder="Ej: 15" min="0" step="0.01" required>

            <label for="stock"><i class="fas fa-boxes"></i> Stock:</label>
            <input type="number" id="stock" name="stock" placeholder="Ej: 10" min="0" required>

            <label for="categoria_id"><i class="fas fa-list"></i> Categoría:</label>
            <select id="categoria_id" name="categoria_id" required>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-agregar">
                <i class="fas fa-plus"></i> Agregar Producto
            </button>
        </form>
    </section>

    <!-- Tabla de productos -->
    <section class="card">
        <h2><i class="fas fa-list-alt"></i> Lista de Productos</h2>
        <div class="tabla-container">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Categoría</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $producto)
                    <tr>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->precio }} Bs</td>
                        <td>{{ $producto->stock }}</td>
                        <td>{{ $producto->categoria->nombre }}</td>
                        <td>
                            <button class="btn-editar" data-id="{{ $producto->id }}">Editar</button>
                            <form action="{{ route('productos.destroy', $producto->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn-eliminar">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>

<!-- Modal de editar -->
<div id="modalEditar" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2><i class="fas fa-edit"></i> Editar Producto</h2>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="editId" name="id">

            <label for="editNombre">Nombre del producto:</label>
            <input type="text" id="editNombre" name="nombre" required>

            <label for="editPrecio">Precio (Bs):</label>
            <input type="number" id="editPrecio" name="precio" min="0" step="0.01" required>

            <label for="editStock">Stock:</label>
            <input type="number" id="editStock" name="stock" min="0" required>

            <label for="editCategoria">Categoría:</label>
            <select id="editCategoria" name="categoria_id" required>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-agregar">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </form>
    </div>
</div>

<!-- Notificación -->
<div id="notificacion" class="notificacion"></div>
@endsection
