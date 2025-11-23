

<?php $__env->startSection('title', 'Gestión de Mesas'); ?>

<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/mesas/mesa.css')); ?>">
<script src="<?php echo e(asset('js/mesas/mesa.js')); ?>" defer></script>

<div class="container-mesas">
    <h1><i class="fas fa-chair"></i> Gestión de Mesas</h1>

    <!-- 🔹 Contenedor visual de mesas -->
    <div id="area-mesas" class="area-mesas">
        <?php $__currentLoopData = $mesas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mesa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="mesa-item <?php echo e($mesa->estado); ?>" 
                data-id="<?php echo e($mesa->id); ?>" 
                style="left: <?php echo e($mesa->pos_x ?? 50); ?>px; top: <?php echo e($mesa->pos_y ?? 50); ?>px;">
                <div class="numero-mesa">Mesa <?php echo e($mesa->numero_mesa); ?></div>
                <div class="icono-mesa"><i class="fas fa-chair"></i></div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>


    <div class="acciones-mesas">
        <button id="guardar-posiciones" class="btn btn-guardar">
            <i class="fas fa-save"></i> Guardar posiciones
        </button>
    </div>

    <div class="stats">
        <span class="disponibles">Disponibles: <?php echo e($disponibles); ?></span>
        <span class="ocupadas">Ocupadas: <?php echo e($ocupadas); ?></span>
        <span class="mantenimiento">En mantenimiento: <?php echo e($mantenimiento); ?></span>
    </div>

    <!-- 🔹 FORMULARIO CREAR -->
    <form id="formMesa" method="POST" action="<?php echo e(route('mesas.store')); ?>">
        <?php echo csrf_field(); ?>
        <div>
            <label for="numero_mesa">Número de Mesa:</label>
            <input type="text" id="numero_mesa" name="numero_mesa" required placeholder="Ejemplo: 1, 2, 3...">
            <span id="errorNumero" class="input-error"></span>
        </div>

        <div>
            <label for="capacidad">Capacidad:</label>
            <input type="number" id="capacidad" name="capacidad" required min="2" max="6" placeholder="2-6 personas">
            <span id="errorCapacidad" class="input-error"></span>
        </div>

        <div>
            <label for="estado">Estado:</label>
            <select name="estado" required>
                <option value="disponible">Disponible</option>
                <option value="ocupada">Ocupada</option>
                <option value="mantenimiento">Mantenimiento</option>
            </select>
        </div>

        <button type="submit" class="btn btn-agregar">Agregar Mesa</button>
    </form>

    <!-- 🔹 TABLA DE MESAS -->
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
                    <td class="
                        <?php echo e($mesa->estado === 'ocupada' ? 'estado-ocupada' : ''); ?>

                        <?php echo e($mesa->estado === 'disponible' ? 'estado-disponible' : ''); ?>

                        <?php echo e($mesa->estado === 'mantenimiento' ? 'estado-mantenimiento' : ''); ?>

                    ">
                        <?php echo e(ucfirst($mesa->estado)); ?>

                    </td>
                    <!-- En la tabla, actualiza el botón de mantenimiento -->
                    <td>
                        <button class="btn btn-editar" data-mesa='<?php echo json_encode($mesa, 15, 512) ?>'>Editar</button>
                        <button class="btn btn-mantenimiento" data-id="<?php echo e($mesa->id); ?>">
                            <i class="fas fa-tools"></i> 
                            <?php echo e($mesa->estado === 'mantenimiento' ? 'Quitar Mantenimiento' : 'Poner Mantenimiento'); ?>

                        </button>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>

<!-- 🔹 MODAL EDITAR -->
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
                    <option value="mantenimiento">Mantenimiento</option>
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