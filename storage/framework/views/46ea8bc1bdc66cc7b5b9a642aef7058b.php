
<?php $__env->startSection('title', 'Menú Coffeeology'); ?>
<?php $__env->startSection('content'); ?>
<div class="container my-5">
    <header class="text-center mb-4">
        <h1><i class="fas fa-store"></i> Menú Coffeeology</h1>
    </header>

    <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <h2 class="categoria-titulo mt-5 text-center"><?php echo e($categoria->nombre); ?></h2>


        <div class="products-grid">
            <?php $__empty_1 = true; $__currentLoopData = $categoria->productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="product-card">
                    <div class="card h-100">
                        <?php if($producto->imagen): ?>
                            <img src="<?php echo e(asset('storage/'.$producto->imagen)); ?>" class="card-img-top fixed-size" alt="<?php echo e($producto->nombre); ?>">
                        <?php else: ?>
                            <img src="<?php echo e(asset('images/default.jpg')); ?>" class="card-img-top fixed-size" alt="Imagen no disponible">
                        <?php endif; ?>
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo e($producto->nombre); ?></h5>
                            <p class="card-text"><?php echo e($producto->descripcion); ?></p>
                        </div>
                        <div class="card-footer text-center">
                            <strong>Bs <?php echo e(number_format($producto->precio, 2)); ?></strong>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-center text-muted">No hay productos en esta categoría.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.menu.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\UNIVALLE\6TO SEMESTRE\Proyecto de Sistemas III\Coffeeology\ProyectoIII-A-LosSuchas\resources\views/menu/index.blade.php ENDPATH**/ ?>