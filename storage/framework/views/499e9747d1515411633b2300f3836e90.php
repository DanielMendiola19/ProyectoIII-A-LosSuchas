<?php $__env->startComponent('mail::message'); ?>
<style>
    .header {
        text-align: center;
    }
    .token-box {
        font-size: 2rem;
        font-weight: bold;
        color: #f5c518;
        background-color: #4B2E2E;
        padding: 15px;
        text-align: center;
        border-radius: 10px;
        margin: 20px 0;
        letter-spacing: 5px;
    }
    .footer {
        font-size: 0.85rem;
        text-align: center;
        color: #888;
        margin-top: 20px;
    }
</style>

<div class="header">
    <img src="https://i.imgur.com/5uPnPo9.png" alt="Coffeeology" width="120">
    <h2>Tu código de verificación</h2>
</div>

<p>Hola, <strong><?php echo e($correo); ?></strong></p>
<p>Usa el siguiente código para continuar con el restablecimiento de tu contraseña. Este código es válido por <strong>3 minutos</strong>.</p>

<div class="token-box"><?php echo e($token); ?></div>

<p>Si no solicitaste este código, ignora este correo.</p>

<div class="footer">
    Coffeeology &copy; <?php echo e(date('Y')); ?>

</div>
<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\laragon\www\ProyectoIII-A-LosSuchas\resources\views/emails/send-token.blade.php ENDPATH**/ ?>