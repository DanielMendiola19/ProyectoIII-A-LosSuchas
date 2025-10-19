<?php $__env->startSection('title', 'Dashboard - Coffeeology'); ?>

<?php $__env->startSection('content'); ?>
    <h1>Panel de Control</h1>

    <!-- TARJETAS -->
    <div class="cards">
        <div class="card" onclick="location.href='<?php echo e(route('productos.index')); ?>'">
            <i class="fas fa-coffee"></i> Productos
        </div>
        <div class="card" onclick="location.href='<?php echo e(route('inventario.index')); ?>'">
            <i class="fas fa-boxes"></i> Inventario
        </div>
        <div class="card" onclick="location.href='<?php echo e(route('menu.index')); ?>'">
            <i class="fas fa-store"></i> Menú
        </div>
        <div class="card" onclick="location.href='<?php echo e(route('pedido.index')); ?>'">
            <i class="fas fa-shopping-bag"></i> Pedidos
        </div>
        <div class="card" onclick="location.href='<?php echo e(route('pedidos.historial')); ?>'">
            <i class="fas fa-clock-rotate-left"></i> Historial Pedidos
        </div>
        <div class="card" onclick="location.href='<?php echo e(route('mesas.index')); ?>'">
            <i class="fas fa-chair"></i> Mesas
        </div>
        <div class="card">
            <i class="fas fa-users"></i> Usuarios
        </div>
        <div class="card">
            <i class="fas fa-chart-bar"></i> Reportes
        </div>
        <div class="card" onclick="location.href='<?php echo e(route('informacion.index')); ?>'">
            <i class="fa-solid fa-circle-info"></i> Información
        </div>
    </div>

    <!-- ESTADÍSTICAS -->
    <div class="stats">
        <div class="stat-card">
            <h2><i class="fas fa-chart-bar"></i> Gráfico de Barras</h2>
            <canvas id="barChart"></canvas>
        </div>
        <div class="stat-card">
            <h2><i class="fas fa-chart-pie"></i> Gráfico de Torta</h2>
            <canvas id="pieChart"></canvas>
        </div>
        <div class="stat-card">
            <h2><i class="fas fa-chart-line"></i> Gráfico de Línea</h2>
            <canvas id="lineChart"></canvas>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Gráfico de Barras
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: { labels: [], datasets: [{ label: 'Datos', data: [], backgroundColor: '#E6B325' }] },
        options: { responsive: true }
    });

    // Gráfico de Torta
    new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: { labels: [], datasets: [{ data: [], backgroundColor: ['#E6B325','#4B2E2E','#C0C0C0'] }] },
        options: { responsive: true }
    });

    // Gráfico de Línea
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: { labels: [], datasets: [{ label: 'Evolución', data: [], borderColor: '#E6B325', backgroundColor: 'rgba(230,179,37,0.2)' }] },
        options: { responsive: true }
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\UNIVALLE\6TO SEMESTRE\Proyecto de Sistemas III\Coffeeology\ProyectoIII-A-LosSuchas\resources\views/dashboard.blade.php ENDPATH**/ ?>