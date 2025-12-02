@extends('layouts.app')

@section('title', 'Dashboard - Coffeeology')

@section('content')
    <h1>Panel de Control</h1>

    <!-- TARJETAS -->
    <div class="cards">
        <div class="card" onclick="location.href='{{ route('productos.index') }}'">
            <i class="fas fa-coffee"></i> Productos
        </div>
        <div class="card" onclick="location.href='{{ route('inventario.index') }}'">
            <i class="fas fa-boxes"></i> Inventario
        </div>
        <div class="card" onclick="location.href='{{ route('menu.index') }}'">
            <i class="fas fa-store"></i> Menú
        </div>
        <div class="card" onclick="location.href='{{ route('pedido.index') }}'">
            <i class="fas fa-shopping-bag"></i> Pedidos
        </div>
        <div class="card" onclick="location.href='{{ route('pedidos.historial') }}'">
            <i class="fas fa-clock-rotate-left"></i> Historial Pedidos
        </div>
        <div class="card" onclick="location.href='{{ route('mesas.index') }}'">
            <i class="fas fa-chair"></i> Mesas
        </div>
        <div class="card" onclick="location.href='{{ route('usuarios.index') }}'">
            <i class="fas fa-users"></i> Usuarios
        </div>
        <div class="card" onclick="location.href='{{ route('reportes.index') }}'">
            <i class="fas fa-chart-bar"></i> Reportes
        </div>
        <div class="card" onclick="location.href='{{ route('informacion.index') }}'">
            <i class="fa-solid fa-circle-info"></i> Información
        </div>
    </div>

    <!-- ESTADÍSTICAS -->
<div class="stats">
    <div class="stat-card">
        <h2><i class="fas fa-chart-bar"></i> Productos Más Vendidos</h2>
        <canvas id="barChart"></canvas>
    </div>
    <div class="stat-card">
        <h2><i class="fas fa-chart-pie"></i> Gestión de Mesas</h2>
        <canvas id="pieChart"></canvas>
    </div>
    <div class="stat-card">
        <h2><i class="fas fa-chart-line"></i> Ventas Últimos 14 Días</h2>
        <canvas id="lineChart"></canvas>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const labelsBarras = {!! $labelsBarras !!};
const dataBarras = {!! $dataBarras !!};

const labelsTorta = {!! $labelsTorta !!};
const dataTorta = {!! $dataTorta !!};

const labelsLinea = {!! $labelsLinea !!};
const dataLinea = {!! $dataLinea !!};

// 🎨 Paleta de colores Coffeeology
const colors = ['#D6A75D', '#553312ff','#7B4B27', '#C89B6B', '#F1D5A5'];

// 🌟 Barras
new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels: labelsBarras,
        datasets: [{
            label: 'Productos más vendidos',
            data: dataBarras,
            backgroundColor: colors,
            borderColor: '#3B2A1A',
            borderWidth: 1,
            borderRadius: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { ticks: { color: '#FFFFFF', font: { weight: 'bold' } }, grid: { color: 'rgba(255,255,255,0.1)' } },
            y: { ticks: { color: '#FFFFFF' }, grid: { color: 'rgba(255,255,255,0.1)' }, beginAtZero: true }
        }
    }
});

new Chart(document.getElementById('pieChart'), {
    type: 'pie',
    data: {
        labels: labelsTorta,
        datasets: [{
            data: dataTorta,
            backgroundColor: ['#D6A75D', '#7B4B27', '#F1D5A5'], // Café claro, marrón medio, crema pastel
            borderColor: '#FFFFFF',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: '#FFFFFF',
                    font: { weight: 'bold' }
                },
                onClick: null // desactiva ocultar al click
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        let value = context.raw || 0;
                        return label + ': ' + value;
                    }
                }
            },
            datalabels: {
                color: '#3B2A1A', // color texto café oscuro
                font: { weight: 'bold', size: 14 },
                anchor: 'center', // centrado en la porción
                align: 'center',
                clamp: true, // evita que se salga de la porción
                formatter: function(value, context) {
                    return context.chart.data.labels[context.dataIndex]; // mostrar nombre del estado
                }
            }
        }
    },
    plugins: [ChartDataLabels]
});





// 📈 Línea
new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
        labels: labelsLinea,
        datasets: [{
            label: 'Ventas últimos 14 días',
            data: dataLinea,
            borderColor: '#D6A75D',
            backgroundColor: 'rgba(214,167,93,0.3)',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#F1D5A5',
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { ticks: { color: '#FFFFFF', font: { weight: 'bold' } }, grid: { color: 'rgba(255,255,255,0.1)' } },
            y: { ticks: { color: '#FFFFFF' }, grid: { color: 'rgba(255,255,255,0.1)' }, beginAtZero: true }
        }
    }
});

</script>
@endpush
