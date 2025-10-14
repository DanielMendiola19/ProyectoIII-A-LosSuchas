

<?php $__env->startSection('title', 'Productos Eliminados'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <header>
        <h1><i class="fas fa-trash"></i> Productos Eliminados</h1>
        <a href="<?php echo e(route('productos.index')); ?>" class="btn btn-agregar">
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
                <?php $__empty_1 = true; $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <img src="<?php echo e($producto->imagen ? asset('storage/'.$producto->imagen) : asset('img/defecto.png')); ?>"
                             alt="<?php echo e($producto->nombre); ?>"
                             style="width:60px; height:60px; object-fit:cover; border-radius:6px;">
                    </td>
                    <td><?php echo e($producto->nombre); ?></td>
                    <td><?php echo e($producto->precio); ?> Bs</td>
                    <td><?php echo e($producto->categoria->nombre ?? 'Sin categoría'); ?></td>
                    <td>
                        <form action="<?php echo e(route('productos.restaurar', $producto->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button class="btn btn-agregar"><i class="fas fa-undo"></i> Restaurar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5">No hay productos eliminados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.productos.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\UNIVALLE\6TO SEMESTRE\Proyecto de Sistemas III\Coffeeology\ProyectoIII-A-LosSuchas\resources\views/productos/eliminados.blade.php ENDPATH**/ ?>