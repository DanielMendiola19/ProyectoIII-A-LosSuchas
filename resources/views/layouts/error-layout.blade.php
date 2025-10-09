<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title') | Coffeeology</title>

  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Lora&family=Montserrat&display=swap" rel="stylesheet">

  <style>
    :root {
      --negro-carbon: #0D0D0D;
      --dorado: #E6B325;
      --blanco-hueso: #FAF9F6;
      --gris-suave: #C0C0C0;
      --cafe-espresso: #4B2E2E;
      --borrar-terracota: #A85C50;
      --borrar-oxido: #9C4A3D;
      --borrar-vino: #8B3A35;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: var(--negro-carbon);
      overflow: hidden;
      font-family: 'Montserrat', sans-serif;
      color: var(--blanco-hueso);
      position: relative;
    }

    /* ======= EFECTO DE FONDO ======= */
    .background-container {
      position: absolute;
      inset: 0;
      overflow: hidden;
      z-index: 0;
    }

    .blob {
      position: absolute;
      border-radius: 50%;
      opacity: 0.4;
      filter: blur(80px);
      animation: moveBlob 12s infinite alternate ease-in-out;
    }

    .blob:nth-child(1) { background: var(--dorado); width: 400px; height: 400px; top: -100px; left: -80px; animation-delay: 0s; }
    .blob:nth-child(2) { background: var(--cafe-espresso); width: 450px; height: 450px; bottom: -120px; right: -100px; animation-delay: 3s; }
    .blob:nth-child(3) { background: var(--blanco-hueso); width: 300px; height: 300px; top: 50%; left: 60%; animation-delay: 5s; }

    @keyframes moveBlob {
      0% { transform: translate(0,0) scale(1); }
      25% { transform: translate(40px,-30px) scale(1.1); }
      50% { transform: translate(-50px,30px) scale(1.2); }
      75% { transform: translate(30px,-20px) scale(1.05); }
      100% { transform: translate(0,0) scale(1); }
    }

    /* ======= 4XX PARPADEANDO ======= */
    .error-code {
      font-family: 'Playfair Display', serif;
      font-size: 6rem;
      font-weight: bold;
      color: var(--dorado);
      animation: blink 1s infinite;
    }

    @keyframes blink {
      0%, 100% { opacity: 1; text-shadow: 0 0 10px var(--dorado), 0 0 20px var(--dorado); }
      50% { opacity: 0.6; text-shadow: 0 0 5px var(--dorado), 0 0 10px var(--dorado); }
    }

    /* ======= CONTENIDO ======= */
    .error-container {
      position: relative;
      z-index: 2;
      text-align: center;
      padding: 2rem;
      max-width: 600px;
    }

    .error-message {
      font-size: 1.3rem;
      margin: 1rem 0 2rem;
      color: var(--gris-suave);
      animation: fadeIn 1.5s ease forwards;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .btn-dashboard {
      display: inline-block;
      background: var(--dorado);
      color: var(--negro-carbon);
      padding: 0.75rem 1.5rem;
      font-weight: bold;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      text-decoration: none; /* quitamos subrayado */
      transition: background 0.3s ease, transform 0.2s;
    }

    .btn-dashboard:hover {
      background: #c79c25;
      transform: scale(1.05);
    }

  </style>
</head>

<body>
  <div class="background-container">
    <div class="blob"></div>
    <div class="blob"></div>
    <div class="blob"></div>
  </div>

  <div class="error-container">
    <div class="error-code">@yield('code')</div>
    <h2 class="fw-semibold">@yield('title')</h2>
    <p class="error-message">@yield('message')</p>
    <a href="{{ url('/dashboard') }}" class="btn-dashboard">Volver al Dashboard</a>
  </div>
</body>
</html>
