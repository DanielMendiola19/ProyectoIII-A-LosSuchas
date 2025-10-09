<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Splash - Coffeeology</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Lora&family=Montserrat&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            height: 100vh;
            width: 100vw;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #0D0D0D; 
            font-family: 'Montserrat', sans-serif;
            overflow: hidden;
            position: relative;
        }

        /* Contenedor de manchas y partículas */
        .background-container {
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }

        /* Manchas dinámicas */
        .blob {
            position: absolute;
            border-radius: 50%;
            opacity: 0.5;
            filter: blur(60px);
            animation: moveBlob 12s infinite alternate ease-in-out;
        }

        .blob:nth-child(1) { width: 300px; height: 300px; top: -80px; left: -80px; background: #E6B325; animation-delay: 0s; }
        .blob:nth-child(2) { width: 400px; height: 400px; bottom: -120px; right: -100px; background: #4B2E2E; animation-delay: 3s; }
        .blob:nth-child(3) { width: 250px; height: 250px; top: 50%; left: 70%; background: #FAF9F6; animation-delay: 5s; }

        @keyframes moveBlob {
            0% { transform: translate(0,0) scale(1) rotate(0deg);}
            25% { transform: translate(30px,-20px) scale(1.1) rotate(20deg);}
            50% { transform: translate(-40px,30px) scale(1.2) rotate(-15deg);}
            75% { transform: translate(20px,-30px) scale(1.05) rotate(10deg);}
            100% { transform: translate(0,0) scale(1) rotate(0deg);}
        }

        /* Estrellitas / partículas */
        .star {
            position: absolute;
            width: 4px;
            height: 4px;
            background: #FAF9F6;
            border-radius: 50%;
            opacity: 0.8;
            animation: floatStar 6s linear infinite;
        }

        @keyframes floatStar {
            0% { transform: translateY(0) rotate(0deg); opacity: 0.8; }
            50% { transform: translateY(-40px) rotate(180deg); opacity: 0.5; }
            100% { transform: translateY(0) rotate(360deg); opacity: 0.8; }
        }

        /* Contenido central */
        .splash-content {
            position: relative;
            text-align: center;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: fadeInContent 2s ease forwards;
        }

        @keyframes fadeInContent {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        .splash-logo {
            width: 160px;
            height: 160px;
            margin-bottom: 20px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            color: #E6B325;
            text-shadow: 0 0 10px #E6B325, 0 0 20px #E6B325;
            animation: fadeInText 2s ease forwards;
        }

        p {
            font-family: 'Lora', serif;
            font-size: 1.3rem;
            color: #C0C0C0;
            margin-top: 10px;
            animation: fadeInText 2s ease 1s forwards;
            opacity: 0;
        }

        @keyframes fadeInText {
            from { opacity: 0; transform: translateY(20px);}
            to { opacity: 1; transform: translateY(0);}
        }

        /* Responsive */
        @media (max-width: 600px) {
            h1 { font-size: 2rem; }
            p { font-size: 1rem; }
            .splash-logo { width: 120px; height: 120px; }
        }

    </style>
</head>
<body>
    <div class="background-container">
        <!-- Manchas -->
        <div class="blob"></div>
        <div class="blob"></div>
        <div class="blob"></div>

        <!-- Estrellitas / partículas de química -->
        <div class="star" style="top:10%; left:15%; animation-delay:0s;"></div>
        <div class="star" style="top:30%; left:80%; animation-delay:1s;"></div>
        <div class="star" style="top:50%; left:40%; animation-delay:2s;"></div>
        <div class="star" style="top:70%; left:60%; animation-delay:3s;"></div>
        <div class="star" style="top:80%; left:20%; animation-delay:4s;"></div>
    </div>
    <!-- Contenido central -->
    <div class="splash-content">
        <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Logo Coffeeology" class="splash-logo">
        <h1>¡Bienvenido a Coffeeology!</h1>
        <p>Descubre la ciencia del café y la quimica de cada sorbo</p>
    </div>
</body>
<script>
    // Redirección automática a los 6 segundos
    let redirectTimeout = setTimeout(() => {
        window.location.href = "<?php echo e(route('dashboard')); ?>";
    }, 6000);

    // Si el usuario presiona la barra espaciadora, se redirige antes
    document.addEventListener('keydown', function(event) {
        if (event.code === 'Space') {
            clearTimeout(redirectTimeout); 
            window.location.href = "<?php echo e(route('dashboard')); ?>";
        }
    });
</script>

</html>
<?php /**PATH C:\laragon\www\ProyectoIII-A-LosSuchas\resources\views/splash.blade.php ENDPATH**/ ?>