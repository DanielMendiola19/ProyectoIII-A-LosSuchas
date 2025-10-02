<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Control - Coffeeology')</title>
    <!-- Tipografías -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Lora&family=Montserrat&display=swap" rel="stylesheet">
    <!-- Font Awesome para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* RESET */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Montserrat', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #0D0D0D;
            color: #FAF9F6;
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
            background: linear-gradient(to bottom right, #0D0D0D, #1a1a1a);
            overflow-y: auto;
            transition: all 0.3s ease;
            width: 100%;
        }

        .content h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: #E6B325;
            margin-bottom: 30px;
            text-shadow: 0 0 10px rgba(230,179,37,0.5);
            text-align: center;
        }

        /* TARJETAS */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .card {
            background: #1a1a1a;
            padding: 25px;
            border-radius: 18px;
            text-align: center;
            font-family: 'Lora', serif;
            font-size: 1.1rem;
            font-weight: bold;
            color: #FAF9F6;
            box-shadow: 0 4px 20px rgba(0,0,0,0.6);
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease, border 0.3s ease;
            border: 2px solid #4B2E2E;
            position: relative;
            overflow: hidden;
        }
        .card::after {
            content: "";
            position: absolute;
            top: 0; left: -100%;
            width: 200%; height: 100%;
            background: linear-gradient(120deg, transparent, rgba(230,179,37,0.2), transparent);
            transition: all 0.6s;
        }
        .card:hover::after {
            left: 100%;
        }
        .card:hover {
            transform: translateY(-7px);
            box-shadow: 0 8px 30px rgba(230,179,37,0.4);
            border-color: #E6B325;
        }

        /* ESTADÍSTICAS */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }
        .stat-card {
            background: #1a1a1a;
            padding: 20px;
            border-radius: 18px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.6);
            border: 2px solid #4B2E2E;
        }
        .stat-card h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            margin-bottom: 15px;
            color: #E6B325;
            text-shadow: 0 0 6px rgba(230,179,37,0.5);
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
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin-bottom: 40px;
            }
            
            .card {
                padding: 30px;
                font-size: 1.2rem;
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
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 25px;
                margin-bottom: 50px;
            }
            
            .card {
                padding: 35px;
                font-size: 1.3rem;
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
                grid-template-columns: repeat(4, 1fr);
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
                padding: 18px;
                font-size: 0.95rem;
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

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div class="logo-container">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Coffeeology" class="sidebar-logo">
        </div>

        <h2>Coffeeology</h2>
        <div class="sidebar-nav">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('productos.index') }}">Productos</a>
            <a href="#">Usuarios</a>
            <a href="#">Reportes</a>
            <a href="#">Configuración</a>
            <hr>
        </div>
    </div>

    <!-- CONTENIDO -->
    <div class="content">
        @yield('content')
    </div>

    <script>
        // Funcionalidad del menú móvil
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

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
            if (window.innerWidth <= 767) {
                const sidebarLinks = sidebar.querySelectorAll('a');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', toggleSidebar);
                });
            }

            // Ajustar en resize
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
    </script>

    @stack('scripts')
</body>
</html>