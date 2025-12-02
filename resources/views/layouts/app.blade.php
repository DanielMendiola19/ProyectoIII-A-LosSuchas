<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Control - Coffeeology')</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- Tipografías -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Lora&family=Montserrat&display=swap" rel="stylesheet">
    <!-- Font Awesome para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Plugin para mostrar etiquetas dentro de las gráficas -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

    <style>
        :root {
            --negro-carbon: #0D0D0D;
            --dorado: #E6B325;
            --blanco-hueso: #FAF9F6;
            --gris-suave: #C0C0C0;
            --cafe-espresso: #4B2E2E;
            --dorado-hover: #c9981f;
            --verde-exito: #5cb85c;
            --azul-info: #5bc0de;
        }

        /* RESET */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--cafe-espresso) 0%, var(--negro-carbon) 100%);
            color: var(--blanco-hueso);
        }

        /* BOTÓN MENÚ MÓVIL */
        .menu-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            background: #E6B325;
            color: #0D0D0D;
            border: none;
            border-radius: 8px;
            width: 45px;
            height: 45px;
            font-size: 1.3rem;
            cursor: pointer;
            z-index: 1001;
            box-shadow: 0 0 15px rgba(230,179,37,0.5);
            transition: all 0.3s ease;
        }

        .menu-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 0 20px rgba(230,179,37,0.7);
        }

        /* OVERLAY PARA MÓVIL */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 998;
        }

        /* SIDEBAR */
        .sidebar {
            width: 100%;
            background: linear-gradient(180deg, #0D0D0D, #4B2E2E);
            color: #FAF9F6;
            display: flex;
            flex-direction: column;
            padding: 15px;
            border-bottom: 2px solid #E6B325;
            box-shadow: inset 0 -4px 15px rgba(0,0,0,0.6), 0 4px 15px rgba(0,0,0,0.6);
            border-radius: 0 0 25px 25px;
            position: relative;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;      /* Permite scroll vertical */
            -webkit-overflow-scrolling: touch; /* Suaviza el scroll en móviles */
        }
        .sidebar::-webkit-scrollbar {
            width: 6px;  /* Ancho del scroll */
        }

        .sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(230,179,37,0.6);
            border-radius: 3px;
        }


        .logo-container {
            text-align: center;
            margin-bottom: 15px;
        }
        .sidebar-logo {
            width: 80px;
            height: auto;
            border-radius: 50%;
            border: 2px solid #E6B325;
            box-shadow: 0 0 15px rgba(230,179,37,0.5);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .sidebar-logo:hover {
            transform: scale(1.05) rotate(-3deg);
            box-shadow: 0 0 25px rgba(230,179,37,0.7);
        }

        .sidebar h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: #E6B325;
            text-align: center;
            text-shadow: 0 0 12px rgba(230,179,37,0.7);
        }

        .sidebar-nav {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
        }

        .sidebar a {
            font-family: 'Lora', serif;
            text-decoration: none;
            color: #FAF9F6;
            padding: 10px 12px;
            border-radius: 12px;
            transition: background 0.4s, color 0.3s, transform 0.2s, box-shadow 0.3s;
            position: relative;
            overflow: hidden;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .sidebar a::before {
            content: '';
            position: absolute;
            left: 0; top: 0;
            width: 4px; height: 100%;
            background: #E6B325;
            border-radius: 2px;
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .sidebar a:hover::before {
            transform: scaleY(1);
        }
        .sidebar a:hover {
            background: rgba(230,179,37,0.1);
            color: #E6B325;
            transform: translateY(-3px);
            box-shadow: 0 0 15px rgba(230,179,37,0.5);
        }

        .sidebar hr {
            border: 0;
            height: 1px;
            background: #E6B325;
            margin: 15px 0;
            opacity: 0.3;
            width: 100%;
        }

        /* CONTENIDO */
        .content {
            flex: 1;
            padding: 20px;
            background: linear-gradient(135deg, var(--cafe-espresso) 0%, var(--negro-carbon) 100%);
            overflow-y: auto;
            transition: all 0.3s ease;
            width: 100%;
        }

        .content h1 {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 2rem;
            color: var(--dorado);
            margin-bottom: 30px;
            text-shadow: 0 0 10px rgba(230,179,37,0.5);
            text-align: center;
            font-weight: bold;
        }

        /* TARJETAS */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        .card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 1.1rem;
            font-weight: bold;
            color: var(--blanco-hueso);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease, border 0.3s ease;
            border: 1px solid rgba(230, 179, 37, 0.2);
            position: relative;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
            border-color: rgba(230, 179, 37, 0.4);
        }

        /* ESTADÍSTICAS */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border: 1px solid rgba(230, 179, 37, 0.2);
        }
        .stat-card h2 {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 1.4rem;
            margin-bottom: 15px;
            color: var(--dorado);
            text-shadow: 0 0 6px rgba(230,179,37,0.5);
            font-weight: bold;
        }
        canvas { width: 100%; max-height: 300px; }

        /* ================= MEDIA QUERIES ================= */
        
        /* Tablets pequeñas y móviles grandes */
        @media (max-width: 767px) {
            .menu-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                height: 100vh;
                width: 85%;
                max-width: 300px;
                border-radius: 0 25px 25px 0;
                border-right: 2px solid #E6B325;
                border-bottom: none;
                box-shadow: inset -4px 0 15px rgba(0,0,0,0.6), 4px 0 15px rgba(0,0,0,0.6);
                transition: left 0.3s ease;
            }

            .sidebar.active {
                left: 0;
            }

            .sidebar-overlay.active {
                display: block;
            }

            .sidebar-nav {
                flex-direction: column;
                flex-wrap: nowrap;
                gap: 0;
            }

            .sidebar a {
                margin-bottom: 12px;
                padding: 12px 15px;
                font-size: 1rem;
            }

            .sidebar a:hover {
                transform: translateX(5px);
            }

            .content {
                padding-top: 80px; /* Espacio para el botón de menú */
            }

            .cards {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .card {
                padding: 20px;
                font-size: 1rem;
            }
        }

        /* Tablets */
        @media (min-width: 768px) {
            body {
                flex-direction: row;
            }
            
            .sidebar {
                width: 220px;
                height: 100vh;
                position: fixed;
                left: 0;
                top: 0;
                border-right: 2px solid #E6B325;
                border-bottom: none;
                box-shadow: inset -4px 0 15px rgba(0,0,0,0.6), 4px 0 15px rgba(0,0,0,0.6);
                border-radius: 0 25px 25px 0;
                padding: 20px;
            }
            
            .sidebar-nav {
                flex-direction: column;
                flex-wrap: nowrap;
                gap: 0;
            }
            
            .sidebar a {
                margin-bottom: 12px;
                padding: 12px 15px;
                font-size: 0.95rem;
            }
            
            .sidebar a:hover {
                transform: translateX(5px);
            }
            
            .sidebar-logo {
                width: 100px;
            }
            
            .sidebar h2 {
                font-size: 1.7rem;
                margin-bottom: 25px;
            }
            
            .sidebar hr {
                margin: 20px 0;
            }
            
            .content {
                margin-left: 220px;
                width: calc(100% - 220px);
                padding: 25px;
            }
        }

        /* Tablets específicas */
        @media (min-width: 768px) and (max-width: 1023px) {
            .content h1 {
                font-size: 2.3rem;
                margin-bottom: 35px;
            }
            
            .cards {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
                margin-bottom: 40px;
            }
            
            .card {
                padding: 25px;
                font-size: 1.1rem;
            }
            
            .stats {
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 25px;
            }
            
            .stat-card {
                padding: 22px;
            }
            
            .stat-card h2 {
                font-size: 1.5rem;
            }
        }

        /* Escritorio */
        @media (min-width: 1024px) {
            .sidebar {
                width: 250px;
                padding: 25px;
            }
            
            .sidebar a {
                font-size: 1rem;
            }
            
            .sidebar-logo {
                width: 120px;
            }
            
            .sidebar h2 {
                font-size: 2rem;
                margin-bottom: 30px;
            }
            
            .sidebar hr {
                margin: 25px 0;
            }
            
            .content {
                margin-left: 250px;
                width: calc(100% - 250px);
                padding: 30px;
            }
            
            .content h1 {
                font-size: 2.7rem;
                margin-bottom: 40px;
            }
            
            .cards {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 25px;
                margin-bottom: 50px;
            }
            
            .card {
                padding: 25px;
                font-size: 1.1rem;
            }
            
            .stats {
                grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
                gap: 30px;
            }
            
            .stat-card {
                padding: 25px;
            }
            
            .stat-card h2 {
                font-size: 1.6rem;
            }
        }

        /* Pantallas grandes */
        @media (min-width: 1200px) {
            .cards {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            }
        }

        /* Móviles pequeños */
        @media (max-width: 480px) {
            .sidebar {
                width: 85%;
                padding: 15px;
            }

            .sidebar h2 {
                font-size: 1.3rem;
                margin-bottom: 15px;
            }

            .sidebar a {
                padding: 10px 12px;
                font-size: 0.9rem;
            }

            .menu-toggle {
                top: 10px;
                left: 10px;
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }

            .content {
                padding: 15px;
                padding-top: 70px;
            }

            .content h1 {
                font-size: 1.7rem;
                margin-bottom: 25px;
            }

            .card {
                padding: 20px;
                font-size: 1rem;
            }
        }

        /* SOLUCIÓN PARA EL TEXTO CORTADO */
.sidebar a[href="{{ route('login.form') }}"] {
    white-space: normal !important;
    line-height: 1.3;
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    word-wrap: break-word;
}
    </style>

    @stack('styles')
</head>
<body>
    <!-- Botón menú móvil solo si hay sesión -->
    @auth
    <button class="menu-toggle" id="menuToggle">
        <i class="fas fa-bars"></i>
    </button>
    @endauth

    <!-- Overlay para móviles -->
    @auth
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
    @endauth

    <!-- SIDEBAR -->
    @auth
    <div class="sidebar" id="sidebar">
        <div class="logo-container">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Coffeeology" class="sidebar-logo">
        </div>

        <h2>Coffeeology</h2>
        <div class="sidebar-nav">
            <a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>

            {{-- ADMIN → Productos --}}
            @if(auth()->user()->rol->nombre === 'Administrador')
                <a href="{{ route('productos.index') }}"><i class="fas fa-coffee me-2"></i> Productos</a>
            @endif

            {{-- ADMIN + COCINERO → Inventario --}}
            @if(in_array(auth()->user()->rol->nombre, ['Administrador','Cocinero']))
                <a href="{{ route('inventario.index') }}"><i class="fas fa-boxes me-2"></i> Inventario</a>
            @endif

            {{-- TODOS → Menú --}}
            <a href="{{ route('menu.index') }}"><i class="fas fa-store me-2"></i> Menú</a>

            {{-- ADMIN + CAJERO → Pedidos --}}
            @if(in_array(auth()->user()->rol->nombre, ['Administrador','Cajero']))
                <a href="{{ route('pedido.index') }}"><i class="fas fa-shopping-bag me-2"></i> Pedido</a>
            @endif

            {{-- TODOS → Historial Pedidos --}}
            @if(in_array(auth()->user()->rol->nombre, ['Administrador','Cajero','Cocinero','Mesero']))
                <a href="{{ route('pedidos.historial') }}"><i class="fas fa-clock-rotate-left me-2"></i> Historial Pedidos</a>
            @endif

            {{-- ADMIN + Mesero → Mesas --}}
            @if(in_array(auth()->user()->rol->nombre, ['Administrador','Mesero']))
                <a href="{{ route('mesas.index') }}"><i class="fas fa-chair me-2"></i> Mesas</a>
            @endif

            {{-- SOLO ADMIN → Usuarios --}}
            @if(auth()->user()->rol->nombre === 'Administrador')
                <a href="{{ route('usuarios.index') }}"><i class="fas fa-users me-2"></i> Usuarios</a>
            @endif

            {{-- ADMIN + CAJERO → Reportes --}}
            @if(in_array(auth()->user()->rol->nombre, ['Administrador','Cajero']))
                <a href="{{ route('reportes.index') }}"><i class="fas fa-chart-bar me-2"></i> Reportes</a>
            @endif

            {{-- SOLO ADMIN → Información --}}
            @if(auth()->user()->rol->nombre === 'Administrador')
                <a href="{{ route('informacion.index') }}"><i class="fa-solid fa-circle-info me-2"></i> Información</a>
            @endif
        </div>

        <hr>

        <!-- Perfil usuario -->
        <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 12px;">
            <div style="
                background-color: #E6B325;
                border-radius: 50%;
                width: 60px;
                height: 60px;
                display: flex;
                justify-content: center;
                align-items: center;
                box-shadow: 0 0 12px rgba(230,179,37,0.5);
                transition: all 0.3s;">
                <i class="bi bi-person-fill" style="font-size: 2rem; color: #0D0D0D;"></i>
            </div>
        </div>

        <div style="margin-top: 15px; padding: 12px; border-radius: 12px; background: rgba(230,179,37,0.1); text-align: center; box-shadow: 0 0 15px rgba(230,179,37,0.2);">
            <p style="margin-bottom: 6px; font-weight: bold; color: #E6B325;">
                {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}
            </p>
            <p style="margin-bottom: 12px; font-size: 0.9rem; color: #C0C0C0;">
                {{ Auth::user()->correo }}
            </p>
            <form method="GET" action="{{ route('perfil.index') }}" style="margin-bottom: 12px">
                @csrf
                <button type="submit" style="background:#c9981f; color:#FAF9F6; border:none; border-radius:8px; padding:8px 14px; cursor:pointer; font-weight:bold; width:100%; transition: all 0.3s;">
                    Ver Perfil
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="background:#7A0F0F; color:#FAF9F6; border:none; border-radius:8px; padding:8px 14px; cursor:pointer; font-weight:bold; width:100%; transition: all 0.3s;">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
    @endauth

    <!-- CONTENIDO -->
    <div class="content" 
            @guest
            style="margin-left: 0; width: 100%;"
            @endguest>
        @yield('content')
    </div>

    {{-- Scripts --}}
    <script>
    @auth
        // Funcionalidad del menú móvil
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            function toggleSidebar() {
                sidebar.classList.toggle('active');
                sidebarOverlay.classList.toggle('active');
                
                const icon = menuToggle.querySelector('i');
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-times');
            }

            menuToggle.addEventListener('click', toggleSidebar);
            sidebarOverlay.addEventListener('click', toggleSidebar);

            if (window.innerWidth <= 767) {
                const sidebarLinks = sidebar.querySelectorAll('a');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', toggleSidebar);
                });
            }

            window.addEventListener('resize', function() {
                if (window.innerWidth > 767) {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                    const icon = menuToggle.querySelector('i');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        });
    @endauth

        window.addEventListener('pageshow', function(event) {
            if (event.persisted || (window.performance && window.performance.getEntriesByType("navigation")[0].type === "back_forward")) {
                window.location.reload();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>