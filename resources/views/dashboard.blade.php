@extends('layouts.app')

@section('title', 'Dashboard - Coffeeology')

@section('content')
    <h1>Panel de Control</h1>

    <!-- TARJETAS -->
    <div class="cards">
        <div class="card" onclick="location.href='{{ route('productos.index') }}'">
            <i class="fas fa-coffee"></i> Productos
        </div>
        <div class="card" onclick="location.href='{{ route('menu.index') }}'">
            <i class="fas fa-store"></i> Menú
        </div>
        <div class="card" onclick="location.href='{{ route('pedido.index') }}'">
            <i class="fas fa-shopping-bag"></i> Pedidos
        </div>
        <div class="card">
            <i class="fas fa-users"></i> Usuarios
        </div>
        <div class="card">
            <i class="fas fa-chart-bar"></i> Reportes
        </div>
        <div class="card">
            <i class="fas fa-cog"></i> Configuración
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
@endsection

@push('scripts')
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
@endpush