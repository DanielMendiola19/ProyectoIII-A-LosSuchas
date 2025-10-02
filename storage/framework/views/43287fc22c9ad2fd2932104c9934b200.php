<?php $__env->startSection('title', 'Dashboard - Coffeeology'); ?>

<?php $__env->startSection('content'); ?>
    <h1>Panel de Control</h1>

    <!-- TARJETAS -->
    <div class="cards">
        <div class="card" onclick="location.href='<?php echo e(route('productos.index')); ?>'">📦 Productos</div>
        <div class="card">👥 Usuarios</div>
        <div class="card">📊 Reportes</div>
        <div class="card">⚙️ Configuración</div>
    </div>

    <!-- ESTADÍSTICAS -->
    <div class="stats">
        <div class="stat-card">
            <h2>📊 Gráfico de Barras</h2>
            <canvas id="barChart"></canvas>
        </div>
        <div class="stat-card">
            <h2>🥧 Gráfico de Torta</h2>
            <canvas id="pieChart"></canvas>
        </div>
        <div class="stat-card">
            <h2>📈 Gráfico de Línea</h2>
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