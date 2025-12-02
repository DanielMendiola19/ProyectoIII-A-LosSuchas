@extends('layouts.app')

@section('title', 'Dashboard - Coffeeology')

@section('content')

@auth
    <h1>Panel de Control</h1>

    <!-- TARJETAS -->
    <div class="cards">

        {{-- ADMIN → Productos --}}
        @if(auth()->user()->rol->nombre === 'Administrador')
            <div class="card" onclick="location.href='{{ route('productos.index') }}'">
                <i class="fas fa-coffee"></i> Productos
            </div>
        @endif

        {{-- ADMIN + COCINERO → Inventario --}}
        @if(in_array(auth()->user()->rol->nombre, ['Administrador','Cocinero']))
            <div class="card" onclick="location.href='{{ route('inventario.index') }}'">
                <i class="fas fa-boxes"></i> Inventario
            </div>
        @endif

        {{-- TODOS → Menú --}}
        <div class="card" onclick="location.href='{{ route('menu.index') }}'">
            <i class="fas fa-store"></i> Menú
        </div>

        {{-- ADMIN + CAJERO → Pedidos --}}
        @if(in_array(auth()->user()->rol->nombre, ['Administrador','Cajero']))
            <div class="card" onclick="location.href='{{ route('pedido.index') }}'">
                <i class="fas fa-shopping-bag"></i> Pedidos
            </div>
        @endif

        {{-- TODOS → Historial pedidos --}}
        @if(in_array(auth()->user()->rol->nombre, ['Administrador','Cajero','Cocinero','Mesero']))
            <div class="card" onclick="location.href='{{ route('pedidos.historial') }}'">
                <i class="fas fa-clock-rotate-left"></i> Historial Pedidos
            </div>
        @endif

        {{-- ADMIN + Mesero → Mesas --}}
        @if(in_array(auth()->user()->rol->nombre, ['Administrador','Mesero']))
            <div class="card" onclick="location.href='{{ route('mesas.index') }}'">
                <i class="fas fa-chair"></i> Mesas
            </div>
        @endif

        {{-- SOLO ADMIN → Usuarios --}}
        @if(auth()->user()->rol->nombre === 'Administrador')
            <div class="card" onclick="location.href='{{ route('usuarios.index') }}'">
                <i class="fas fa-users"></i> Usuarios
            </div>
        @endif

        {{-- ADMIN + CAJERO → Reportes --}}
        @if(in_array(auth()->user()->rol->nombre, ['Administrador','Cajero']))
            <div class="card" onclick="location.href='{{ route('reportes.index') }}'">
                <i class="fas fa-chart-bar"></i> Reportes
            </div>
        @endif

        {{-- SOLO ADMIN → Información --}}
        @if(auth()->user()->rol->nombre === 'Administrador')
            <div class="card" onclick="location.href='{{ route('informacion.index') }}'">
                <i class="fa-solid fa-circle-info"></i> Información
            </div>
        @endif

    </div>

    <!-- ESTADÍSTICAS -->
    <div class="stats">
        <div class="stat-card">
            <h2><i class="fas fa-chart-bar"></i> Productos Más Vendidos</h2>
            <canvas id="barChart"></canvas>
        </div>
        <div class="stat-card">
            <h2><i class="fas fa-chart-pie"></i> Ventas por Categoría</h2>
            <canvas id="pieChart"></canvas>
        </div>
        <div class="stat-card">
            <h2><i class="fas fa-chart-line"></i> Ventas Últimos 14 Días</h2>
            <canvas id="lineChart"></canvas>
        </div>
    </div>

@else
    {{-- CENTRADO COMPLETO --}}
    <div style="
        display: flex; 
        flex-direction: column; 
        justify-content: center; 
        align-items: center; 
        height: 80vh; 
        text-align: center; 
        gap: 30px;
        padding: 20px;
    ">
        {{-- LOGO ARRIBA --}}
        <div class="logo-container" style="margin-bottom: 20px;">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Coffeeology" class="sidebar-logo" 
                 style="width:170px; height:auto; border-radius:50%; border:2px solid #E6B325; box-shadow: 0 0 15px rgba(230,179,37,0.5);">
        </div>

        <h1 style="
            font-size: 2rem; 
            color: #fff; 
            max-width: 600px;
            line-height: 1.2;
        ">
            Debes iniciar sesión para acceder al Panel de Control
        </h1>

        {{-- BOTÓN IGUAL QUE SIDEBAR --}}
        <a href="{{ route('login.form') }}" 
        style="
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background-color: #E6B325;
            color: #0D0D0D;
            padding: 15px 30px;
            border-radius: 12px;
            font-weight: bold;
            font-family: 'Lora', serif;
            font-size: 1.2rem;
            box-shadow: 0 0 20px rgba(230,179,37,0.6);
            transition: all 0.3s;
            text-decoration: none;
        "
        onmouseover="this.style.boxShadow='0 0 30px rgba(230,179,37,0.8)';"
        onmouseout="this.style.boxShadow='0 0 20px rgba(230,179,37,0.6)';"
        >
            <i class="bi bi-person-fill" style="font-size:1.8rem;"></i>
            Iniciar Sesión
        </a>
    </div>
@endauth


@endsection

@push('scripts')
@auth
<script>
    const labelsBarras = {!! $labelsBarras !!};
    const dataBarras = {!! $dataBarras !!};

    const labelsTorta = {!! $labelsTorta !!};
    const dataTorta = {!! $dataTorta !!};

    const labelsLinea = {!! $labelsLinea !!};
    const dataLinea = {!! $dataLinea !!};

    const colors = ['#D6A75D', '#553312ff','#7B4B27', '#C89B6B', '#F1D5A5'];

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
            plugins: { legend: { display: false } }
        }
    });

    new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: {
            labels: labelsTorta,
            datasets: [{
                data: dataTorta,
                backgroundColor: colors,
                borderColor: '#FFFFFF',
                borderWidth: 2
            }]
        },
        options: { 
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });

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
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });
</script>
@endauth
@endpush
