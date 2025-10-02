<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control - Coffeeology</title>
    <!-- Tipografías -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Lora&family=Montserrat&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* RESET */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Montserrat', sans-serif;
            display: flex;
            height: 100vh;
            background-color: #0D0D0D;
            color: #FAF9F6;
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #0D0D0D, #4B2E2E);
            color: #FAF9F6;
            display: flex;
            flex-direction: column;
            padding: 25px;
            border-right: 2px solid #E6B325;
            box-shadow: inset -4px 0 15px rgba(0,0,0,0.6), 4px 0 15px rgba(0,0,0,0.6);
            border-radius: 0 25px 25px 0;
        }

        /* LOGO */
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar-logo {
            width: 120px;
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

        /* CONTENIDO */
        .content {
            flex: 1;
            padding: 30px;
            background: linear-gradient(to bottom right, #0D0D0D, #1a1a1a);
            overflow-y: auto;
        }
        .content h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.7rem;
            color: #E6B325;
            margin-bottom: 40px;
            text-shadow: 0 0 10px rgba(230,179,37,0.5);
            text-align: center; /* CENTRADO */
        }

        /* TARJETAS */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
            margin-bottom: 50px;
        }
        .card {
            background: #1a1a1a;
            padding: 35px;
            border-radius: 18px;
            text-align: center;
            font-family: 'Lora', serif;
            font-size: 1.3rem;
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
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
        }
        .stat-card {
            background: #1a1a1a;
            padding: 25px;
            border-radius: 18px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.6);
            border: 2px solid #4B2E2E;
        }
        .stat-card h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            margin-bottom: 15px;
            color: #E6B325;
            text-shadow: 0 0 6px rgba(230,179,37,0.5);
        }
        canvas {
            width: 100%;
            max-height: 300px;
        }
    </style>
</head>
<body>
    <!-- SIDEBAR -->
    <div class="sidebar">
        <!-- LOGO -->
        <div class="logo-container">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Coffeeology" class="sidebar-logo">
        </div>

        <h2>Coffeeology</h2>
        <a href="{{ route('productos.index') }}">Productos</a>
        <a href="#">Usuarios</a>
        <a href="#">Reportes</a>
        <a href="#">Configuración</a>
        <hr>
    </div>

    <!-- CONTENIDO -->
    <div class="content">
        <h1>Panel de Control</h1>

        <!-- TARJETAS -->
        <div class="cards">
            <div class="card" onclick="location.href='{{ route('productos.index') }}'">📦 Productos</div>
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
    </div>

    <!-- SCRIPT CHART.JS -->
    <script>
        // Barras
        new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: { labels: [], datasets: [{ label: 'Datos', data: [], backgroundColor: '#E6B325' }] },
            options: { responsive: true }
        });

        // Torta
        new Chart(document.getElementById('pieChart'), {
            type: 'pie',
            data: { labels: [], datasets: [{ data: [], backgroundColor: ['#E6B325','#4B2E2E','#C0C0C0'] }] },
            options: { responsive: true }
        });

        // Línea
        new Chart(document.getElementById('lineChart'), {
            type: 'line',
            data: { labels: [], datasets: [{ label: 'Evolución', data: [], borderColor: '#E6B325', backgroundColor: 'rgba(230,179,37,0.2)' }] },
            options: { responsive: true }
        });
    </script>
</body>
</html>
