<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gestión de Productos')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS propio -->
    <link rel="stylesheet" href="{{ asset('css/productos/app.css') }}">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="main-container">
        @yield('content')
    </div>

    <!-- JS propio -->
    <script src="{{ asset('js/productos/app.js') }}"></script>
    
    @yield('scripts')
</body>
</html>
