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
                {{ $productos->filter(fn($p) => $p->categoria->nombre === 'Bebidas')->count() }}
            </div>
        <div class="etiqueta">Productos Bebidas</div>
    </div>
    </div>

    <!-- Formulario de productos -->
    <section class="card formulario">
        <h2><i class="fas fa-plus-circle"></i> Agregar Nuevo Producto</h2>
        <form id="productForm" action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
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

            <label for="imagen"><i class="fas fa-image"></i> Imagen:</label>
            <input type="file" id="imagen" name="imagen" accept="image/*">
            <label class="custom-file-label" for="imagen">Seleccionar imagen</label>



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
                        <th>Imagen</th>
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
                        <td class="td-imagen">
                            <img src="{{ $producto->imagen ? asset('storage/'.$producto->imagen) : asset('img/defecto.png') }}"
                                 alt="{{ $producto->nombre }}"
                                 style="width:60px; height:60px; object-fit:cover; border-radius:6px;">
                        </td>
                        <td class="td-nombre">{{ $producto->nombre }}</td>
                        <td class="td-precio">{{ $producto->precio }} Bs</td>
                        <td class="td-stock">{{ $producto->stock }}</td>
                        <td class="td-categoria">{{ $producto->categoria->nombre }}</td>
                        <td>
                            <button
                                class="btn-editar"
                                data-id="{{ $producto->id }}"
                                data-imagen="{{ $producto->imagen ? asset('storage/'.$producto->imagen) : '' }}"
                                data-categoria="{{ $producto->categoria_id }}"
                            >Editar</button>

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
        <form id="editForm" method="POST" enctype="multipart/form-data">
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

            <label for="editImagen"><i class="fas fa-image"></i> Cambiar imagen (opcional):</label>
            <label class="custom-file-label" for="editImagen">Seleccionar nueva imagen</label>
            <input type="file" id="editImagen" name="imagen" accept="image/*">


            <div class="imagen-preview" style="margin-top:10px;">
                <img id="editPreview" src="{{ asset('img/defecto.png') }}" alt="Preview" style="width:120px; height:80px; object-fit:cover; border-radius:6px;">
            </div>

            <button type="submit" class="btn btn-agregar" style="margin-top:10px;">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </form>
    </div>
</div>

<!-- Notificación -->
<div id="notificacion" class="notificacion"></div>
@endsection
