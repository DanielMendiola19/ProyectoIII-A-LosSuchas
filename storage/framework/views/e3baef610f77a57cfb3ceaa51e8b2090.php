

<?php $__env->startSection('title', 'Gestión de Mesas'); ?>

<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/mesas/mesa.css')); ?>">
<script src="<?php echo e(asset('js/mesas/mesa.js')); ?>" defer></script>

<div class="container-mesas">
    <h1><i class= "fas fa-chair"></i> Gestión de Mesas</h1>

    <img src="<?php echo e(asset('img/mesas/ubicacion.png')); ?>" alt="Ubicación de Mesas" class="mesas-img">

    <div class="stats">
        <span class="disponibles">Disponibles: <?php echo e($disponibles); ?></span>
        <span class="ocupadas">Ocupadas: <?php echo e($ocupadas); ?></span>
    </div>

    <form id="formMesa" method="POST" action="<?php echo e(route('mesas.store')); ?>">
        <?php echo csrf_field(); ?>
        <div>
            <label for="numero_mesa">Número de Mesa:</label>
            <input type="text" id="numero_mesa" name="numero_mesa" required>
            <span id="errorNumero" class="input-error"></span>
        </div>
        <div>
            <label for="capacidad">Capacidad:</label>
            <input type="number" id="capacidad" name="capacidad" required min="2" max="6">
            <span id="errorCapacidad" class="input-error"></span>
        </div>
        <div>
            <label for="estado">Estado:</label>
            <select name="estado" required>
                <option value="disponible">Disponible</option>
                <option value="ocupada">Ocupada</option>
            </select>
        </div>
        <button type="submit" class="btn btn-agregar">Agregar Mesa</button>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>N° Mesa</th>
                <th>Capacidad</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $mesas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mesa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($mesa->numero_mesa); ?></td>
                    <td><?php echo e($mesa->capacidad); ?></td>
                    <td class="<?php echo e($mesa->estado === 'ocupada' ? 'estado-ocupada' : 'estado-disponible'); ?>">
                        <?php echo e(ucfirst($mesa->estado)); ?>

                    </td>
                    <td>
                        <button class="btn btn-editar" data-mesa='<?php echo json_encode($mesa, 15, 512) ?>'>Editar</button>
                        <form action="<?php echo e(route('mesas.destroy', $mesa)); ?>" method="POST" style="display:inline">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-eliminar">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>

<!-- Modal para editar -->
<div id="modalEditar" class="modal">
    <div class="modal-content">
        <h3>Editar Mesa</h3>
        <form id="formEditar" method="POST">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <div>
                <label for="edit_numero_mesa">Número de Mesa:</label>
                <input type="text" id="edit_numero_mesa" name="numero_mesa" readonly>
            </div>
            <div>
                <label for="edit_capacidad">Capacidad:</label>
                <input type="number" id="edit_capacidad" name="capacidad" required min="2" max="6">
                <span id="errorEditarCapacidad" class="input-error"></span>
            </div>
            <div>
                <label for="edit_estado">Estado:</label>
                <select id="edit_estado" name="estado" required>
                    <option value="disponible">Disponible</option>
                    <option value="ocupada">Ocupada</option>
                </select>
            </div>
            <button type="submit" class="btn btn-agregar">Guardar Cambios</button>
            <button type="button" id="cerrarModal" class="btn btn-eliminar">Cancelar</button>
        </form>
    </div>
</div>

<div id="notificacion" class="notificacion"></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\UNIVALLE\6TO SEMESTRE\Proyecto de Sistemas III\Coffeeology\ProyectoIII-A-LosSuchas\resources\views/mesas/index.blade.php ENDPATH**/ ?>