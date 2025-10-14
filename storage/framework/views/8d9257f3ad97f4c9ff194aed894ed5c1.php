<?php $__env->startSection('title', 'Gestión de Productos Coffeeology'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <header>
        <h1><i class="fas fa-coffee"></i> Gestión de Productos Coffeeology</h1>
    </header>
    <a href="<?php echo e(route('productos.eliminados')); ?>" class="btn btn-eliminados" style="margin-left: 15px;">
        <i class="fas fa-trash"></i> Ver productos eliminados
    </a>

    <!-- Estadísticas -->
    <div class="estadisticas">
        <div class="estadistica">
            <div class="valor" id="totalProductos"><?php echo e($productos->count()); ?></div>
            <div class="etiqueta">Total Productos</div>
        </div>
        <div class="estadistica">
            <div class="valor" id="valorInventario">
                <?php echo e($productos->sum(fn($p) => $p->precio)); ?> Bs
            </div>
            <div class="etiqueta">Valor Inventario</div>
        </div>
        <div class="estadistica">
            <div class="valor" id="productosBebidas">
                <?php echo e($productos->filter(fn($p) => $p->categoria->nombre === 'Bebidas')->count()); ?>

            </div>
            <div class="etiqueta">Productos Bebidas</div>
        </div>
    </div>

    <!-- Formulario de productos -->
    <section class="card formulario">
        <h2><i class="fas fa-plus-circle"></i> Agregar Nuevo Producto</h2>
        <form id="productForm" action="<?php echo e(route('productos.store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <label for="nombre"><i class="fas fa-tag"></i> Nombre del producto:</label>
            <input type="text" id="nombre" name="nombre" placeholder="Ej: Café Americano" required>

            <label for="precio"><i class="fas fa-dollar-sign"></i> Precio (Bs):</label>
            <input type="number" id="precio" name="precio" placeholder="Ej: 15" min="0" step="0.01" required>

            <label for="stock"><i class="fas fa-boxes"></i> Stock:</label>
            <input type="number" id="stock" name="stock" placeholder="Ej: 10" min="0" required>

            <label for="categoria_id"><i class="fas fa-list"></i> Categoría:</label>
            <select id="categoria_id" name="categoria_id" required>
                <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($categoria->id); ?>"><?php echo e($categoria->nombre); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                    <?php $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="td-imagen">
                            <img src="<?php echo e($producto->imagen ? asset('storage/'.$producto->imagen) : asset('img/defecto.png')); ?>"
                                 alt="<?php echo e($producto->nombre); ?>"
                                 style="width:60px; height:60px; object-fit:cover; border-radius:6px;">
                        </td>
                        <td class="td-nombre"><?php echo e($producto->nombre); ?></td>
                        <td class="td-precio"><?php echo e($producto->precio); ?> Bs</td>
                        <td class="td-stock"><?php echo e($producto->stock); ?></td>
                        <td class="td-categoria"><?php echo e($producto->categoria->nombre); ?></td>
                        <td>
                            <div class="acciones">
                                <button
                                    class="btn-editar"
                                    data-id="<?php echo e($producto->id); ?>"
                                    data-imagen="<?php echo e($producto->imagen ? asset('storage/'.$producto->imagen) : ''); ?>"
                                    data-categoria="<?php echo e($producto->categoria_id); ?>"
                                >
                                    <i class="fas fa-edit"></i> Editar
                                </button>

                                <form action="<?php echo e(route('productos.destroy', $producto->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="button" class="btn-eliminar">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <input type="hidden" id="editId" name="id">

            <label for="editNombre">Nombre del producto:</label>
            <input type="text" id="editNombre" name="nombre" required>

            <label for="editPrecio">Precio (Bs):</label>
            <input type="number" id="editPrecio" name="precio" min="0" step="0.01" required>

            <label for="editStock">Stock:</label>
            <input type="number" id="editStock" name="stock" min="0" required>

            <label for="editCategoria">Categoría:</label>
            <select id="editCategoria" name="categoria_id" required>
                <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($categoria->id); ?>"><?php echo e($categoria->nombre); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <label for="editImagen"><i class="fas fa-image"></i> Cambiar imagen (opcional):</label>
            <input type="file" id="editImagen" name="imagen" accept="image/*">
            <label class="custom-file-label" for="editImagen">Seleccionar nueva imagen</label>

            <div class="imagen-preview" style="margin-top:10px;">
                <img id="editPreview" src="<?php echo e(asset('img/defecto.png')); ?>" alt="Preview" style="width:120px; height:80px; object-fit:cover; border-radius:6px;">
            </div>

            <button type="submit" class="btn btn-agregar" style="margin-top:10px;">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </form>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div id="modalConfirmacion" class="modal-confirmacion">
    <div class="modal-confirmacion-content">
        <div class="modal-confirmacion-icono">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3 class="modal-confirmacion-titulo">¿Estás seguro?</h3>
        <p class="modal-confirmacion-mensaje" id="confirmacionMensaje">
            Esta acción eliminará el producto de manera lógica. 
            Podrás recuperarlo desde la sección de productos eliminados.
        </p>
        <div class="modal-confirmacion-botones">
            <button id="btnConfirmarEliminar" class="btn-confirmar">
                <i class="fas fa-trash"></i> Sí, eliminar
            </button>
            <button id="btnCancelarEliminar" class="btn-cancelar">
                <i class="fas fa-times"></i> Cancelar
            </button>
        </div>
    </div>
</div>

<!-- Notificación -->
<div id="notificacion" class="notificacion"></div>

<!-- CSRF Token para JavaScript -->
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.productos.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\UNIVALLE\6TO SEMESTRE\Proyecto de Sistemas III\Coffeeology\ProyectoIII-A-LosSuchas\resources\views/productos/index.blade.php ENDPATH**/ ?>