<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Coffeeology')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tipografías -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Lora&family=Montserrat&display=swap" rel="stylesheet">

    <!-- CSS propio -->
    <link rel="stylesheet" href="{{ asset('css/productos/app.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* ================= RESET ================= */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Montserrat', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #0D0D0D;
            color: #FAF9F6;
        }

        /* ================= SIDEBAR ================= */
        .sidebar {
            position: fixed;
            top: 0;
            left: -100%;
            height: 100vh;
            width: 280px;
            background: linear-gradient(180deg, #0D0D0D, #4B2E2E);
            color: #FAF9F6;
            display: flex;
            flex-direction: column;
            padding: 25px;
            border-right: 2px solid #E6B325;
            box-shadow: inset -4px 0 15px rgba(0,0,0,0.6), 4px 0 15px rgba(0,0,0,0.6);
            border-radius: 0 25px 25px 0;
            z-index: 1000;
            transition: left 0.3s ease;
        }

        .sidebar.active {
            left: 0;
        }

        .logo-container { 
            text-align: center; 
            margin-bottom: 20px; 
        }

        .sidebar-logo {
            width: 120px;
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
            font-size: 2rem;
            margin-bottom: 30px;
            color: #E6B325;
            text-align: center;
            text-shadow: 0 0 12px rgba(230,179,37,0.7);
        }

        .sidebar a {
            font-family: 'Lora', serif;
            text-decoration: none;
            color: #FAF9F6;
            padding: 12px 15px;
            border-radius: 12px;
            margin-bottom: 12px;
            transition: background 0.4s, color 0.3s, transform 0.2s, box-shadow 0.3s;
            display: block;
            position: relative;
            overflow: hidden;
        }

        .sidebar a::before {
            content: '';
            position: absolute;
            left: 0; 
            top: 0;
            width: 4px; 
            height: 100%;
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
            transform: translateX(5px);
            box-shadow: 0 0 15px rgba(230,179,37,0.5);
        }

        .sidebar hr {
            border: 0;
            height: 1px;
            background: #E6B325;
            margin: 25px 0;
            opacity: 0.3;
        }

        /* Botón menú móvil */
        .menu-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #E6B325;
            color: #0D0D0D;
            border: none;
            border-radius: 8px;
            width: 45px;
            height: 45px;
            font-size: 1.5rem;
            cursor: pointer;
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 15px rgba(230,179,37,0.5);
            transition: all 0.3s ease;
        }

        .menu-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 0 20px rgba(230,179,37,0.7);
        }

        /* Overlay para móviles */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* CONTENIDO */
        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 30px;
            overflow-y: auto;
            min-height: 100vh;
            width: 100%;
            transition: margin-left 0.3s ease;
        }

        /* ================= MEDIA QUERIES ================= */
        /* Pantallas grandes (escritorio) */
        @media (min-width: 1024px) {
            body {
                flex-direction: row;
            }

            .sidebar {
                position: fixed;
                left: 0;
                width: 250px;
            }

            .menu-toggle {
                display: none;
            }

            .sidebar-overlay {
                display: none;
            }

            .content {
                margin-left: 250px;
                width: calc(100% - 250px);
            }
        }

        /* Tablets */
        @media (min-width: 768px) and (max-width: 1023px) {
            .sidebar {
                width: 240px;
            }
        }

        /* Móviles */
        @media (max-width: 767px) {
            .sidebar {
                width: 85%;
                max-width: 300px;
            }

            .sidebar h2 {
                font-size: 1.7rem;
            }

            .sidebar-logo {
                width: 100px;
            }

            .content {
                padding: 20px 15px;
            }
        }

        /* Móviles pequeños */
        @media (max-width: 480px) {
            .sidebar {
                padding: 20px 15px;
            }

            .sidebar h2 {
                font-size: 1.5rem;
                margin-bottom: 20px;
            }

            .sidebar a {
                padding: 10px 12px;
                font-size: 0.9rem;
            }

            .menu-toggle {
                top: 15px;
                left: 15px;
                width: 40px;
                height: 40px;
                font-size: 1.3rem;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Botón menú móvil -->
    <button class="menu-toggle" id="menuToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Overlay para móviles -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ================= SIDEBAR ================= -->
    <div class="sidebar" id="sidebar">
        <div class="logo-container">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Coffeeology" class="sidebar-logo">
        </div>

        <h2>Coffeeology</h2>
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('productos.index') }}">Productos</a>
        <a href="#">Usuarios</a>
        <a href="#">Reportes</a>
        <a href="#">Configuración</a>
        <hr>
    </div>

    <!-- ================= CONTENIDO ================= -->
    <div class="content" id="mainContent">
        @yield('content')
    </div>

    <!-- JS propio -->
    <script src="{{ asset('js/productos/app.js') }}"></script>

    <script>
        // Funcionalidad del menú móvil
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mainContent = document.getElementById('mainContent');

            function toggleSidebar() {
                sidebar.classList.toggle('active');
                sidebarOverlay.classList.toggle('active');
                
                // Cambiar ícono
                const icon = menuToggle.querySelector('i');
                if (sidebar.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }

            menuToggle.addEventListener('click', toggleSidebar);
            sidebarOverlay.addEventListener('click', toggleSidebar);

            // Cerrar sidebar al hacer clic en un enlace (en móviles)
            if (window.innerWidth < 1024) {
                const sidebarLinks = sidebar.querySelectorAll('a');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', toggleSidebar);
                });
            }

            // Ajustar contenido en resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                    const icon = menuToggle.querySelector('i');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>