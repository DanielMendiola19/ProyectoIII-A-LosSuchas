

<?php $__env->startSection('title', 'Pedidos - Coffeeology'); ?>

<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/pedido/pedido.css')); ?>">
<script src="<?php echo e(asset('js/pedido/pedido.js')); ?>" defer></script>

<div class="container my-5">
    <header>
        <h1><i class="fas fa-shopping-bag"></i> Realizar Pedido</h1>
    </header>

    <!-- Productos divididos por categoría -->
    <?php $__currentLoopData = $productos->groupBy('categoria.nombre'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoriaNombre => $productosCategoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <h2 class="categoria-titulo"><?php echo e($categoriaNombre); ?></h2>
        <div class="products-grid">
            <?php $__currentLoopData = $productosCategoria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($producto->stock > 0): ?>
                <div class="product-card" 
                     data-id="<?php echo e($producto->id); ?>" 
                     data-nombre="<?php echo e($producto->nombre); ?>" 
                     data-precio="<?php echo e($producto->precio); ?>"
                     data-imagen="<?php echo e(asset('storage/' . $producto->imagen)); ?>"
                     data-stock="<?php echo e($producto->stock); ?>">
                    
                    <div class="card h-100">
                        <img src="<?php echo e(asset('storage/'.$producto->imagen)); ?>" alt="<?php echo e($producto->nombre); ?>" class="card-img-top">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo e($producto->nombre); ?></h5>
                            <p class="card-text">Bs <?php echo e(number_format($producto->precio, 2)); ?></p>
                            <p class="card-text stock-info">Stock: <?php echo e($producto->stock); ?></p>
                        </div>
                        <button type="button" class="btn-agregar">
                            <i class="fas fa-cart-plus"></i> Agregar
                        </button>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<div id="modalMesa" class="modal"> <!-- Cambié de modalMesas a modalMesa -->
    <div class="modal-content">
        <h3><i class="fas fa-chair"></i> Seleccione una mesa</h3>

        <div id="listaMesas" class="mesas-grid">
            <?php $__currentLoopData = $mesas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mesa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="mesa-item <?php echo e($mesa->estado === 'ocupada' ? 'ocupada' : 'disponible'); ?>"
                     data-id="<?php echo e($mesa->id); ?>"
                     data-estado="<?php echo e($mesa->estado); ?>">
                    <p>Mesa <?php echo e($mesa->numero_mesa); ?></p>
                    <small>Capacidad: <?php echo e($mesa->capacidad); ?></small>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                    <?php $__currentLoopData = $metodosPago; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metodo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($metodo); ?>"><?php echo e($metodo); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.menu.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\UNIVALLE\6TO SEMESTRE\Proyecto de Sistemas III\Coffeeology\ProyectoIII-A-LosSuchas\resources\views/pedido/index.blade.php ENDPATH**/ ?>