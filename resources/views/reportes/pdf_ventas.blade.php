<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de ventas</title>
    <link rel="stylesheet" href="{{ public_path('css/reportes/pdf.css') }}">
</head>
<body>
<div class="pdf-page">

    <table class="pdf-header">
        <tr>
            {{-- Logo izquierda --}}
            <td class="pdf-header-logo">
                <img src="{{ public_path('images/logo.png') }}" alt="Logo">
            </td>

            {{-- Título + fechas (centro) --}}
            <td class="pdf-header-title">
                <h1>Reporte de Ventas</h1>
                <p class="pdf-fechas">
                    <span class="lbl">Desde:</span> {{ $desde }}
                    <span class="lbl">Hasta:</span> {{ $hasta }}
                </p>
            </td>
            {{-- Columna vacía derecha (mismo ancho que el logo) --}}
            <td class="pdf-header-spacer"></td>
        </tr>
    </table>
    <div class="pdf-body">
        {{-- Marca de agua --}}
        <div class="pdf-watermark">
            <img src="{{ public_path('images/logo.png') }}" alt="Watermark">
        </div>

        {{-- Tabla principal --}}
        <table class="pdf-table">
            <thead>
            <tr>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Cantidad</th>
                <th>Total (Bs)</th>
            </tr>
            </thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row->producto }}</td>
                    <td>{{ $row->categoria }}</td>
                    <td>{{ $row->cantidad }}</td>
                    <td>{{ number_format($row->total, 2) }}</td>
                </tr>
            @empty
                <tr class="sin-datos">
                    <td colspan="4">Sin resultados para el rango seleccionado.</td>
                </tr>
            @endforelse

            {{-- Totales --}}
            <tr class="pdf-totales">
                <td>Productos: {{ $resumen['productos'] }}</td>
                <td>Cantidad total: {{ $resumen['cantidad'] }}</td>
                <td colspan="2">Total vendido: {{ number_format($resumen['monto'], 2) }} Bs</td>
            </tr>
            </tbody>
        </table>

    </div>
</div>
</body>
</html>
