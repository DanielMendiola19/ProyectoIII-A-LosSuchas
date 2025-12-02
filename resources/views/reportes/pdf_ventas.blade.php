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
            <td class="pdf-header-logo">
                <img src="{{ public_path('images/logo.png') }}" alt="Logo">
            </td>

            <td class="pdf-header-title">
                <h1>Reporte de Ventas</h1>
                <p class="pdf-fechas">
                    <span class="lbl">Desde:</span> {{ $desde }}
                    <span class="lbl">Hasta:</span> {{ $hasta }}
                </p>

                @isset($agrupacion)
                    <p class="pdf-fechas">
                        <span class="lbl">Agrupación:</span> {{ ucfirst($agrupacion) }}
                    </p>
                @endisset
            </td>

            <td class="pdf-header-spacer"></td>
        </tr>
    </table>

    <div class="pdf-body">
        <div class="pdf-watermark">
            <img src="{{ public_path('images/logo.png') }}" alt="Watermark">
        </div>

        {{-- Versión con agrupación por periodo --}}
        @if(isset($ventasPorPeriodo) && $ventasPorPeriodo instanceof \Illuminate\Support\Collection && $ventasPorPeriodo->isNotEmpty())
            
            @foreach($ventasPorPeriodo as $periodo => $items)
                <h2 class="pdf-subtitulo">
                    @if(($agrupacion ?? null) === 'dia')
                        Día: {{ $periodo }}
                    @elseif(($agrupacion ?? null) === 'mes')
                        Mes: {{ $periodo }}
                    @else
                        {{ $periodo }} {{-- Semana 1 de Octubre 2025, etc. --}}
                    @endif
                </h2>

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
                    @foreach($items as $row)
                        <tr>
                            <td>{{ $row->producto }}</td>
                            <td>{{ $row->categoria }}</td>
                            <td>{{ $row->cantidad }}</td>
                            <td>{{ number_format($row->total, 2) }}</td>
                        </tr>
                    @endforeach

                    @php
                        $subtotalProductos = $items->count();
                        $subtotalCantidad  = $items->sum('cantidad');
                        $subtotalMonto     = $items->sum('total');
                    @endphp

                    <tr class="pdf-totales">
                        <td>Productos: {{ $subtotalProductos }}</td>
                        <td></td>
                        <td>Cantidad: {{ $subtotalCantidad }}</td>
                        <td>Subtotal: {{ number_format($subtotalMonto, 2) }} Bs</td>
                    </tr>
                    </tbody>
                </table>
            @endforeach

            {{-- Resumen general (total de totales) --}}
            <table class="pdf-table pdf-resumen">
                <tbody>
                <tr class="pdf-totales">
                    <td>
                        @if(($agrupacion ?? null) === 'dia')
                            Días: {{ $resumen['periodos'] ?? 0 }}
                        @elseif(($agrupacion ?? null) === 'semana')
                            Semanas: {{ $resumen['periodos'] ?? 0 }}
                        @elseif(($agrupacion ?? null) === 'mes')
                            Meses: {{ $resumen['periodos'] ?? 0 }}
                        @else
                            Periodos: {{ $resumen['periodos'] ?? 0 }}
                        @endif
                    </td>
                    <td>Productos distintos: {{ $resumen['productos'] ?? 0 }}</td>
                    <td>Cantidad total: {{ $resumen['cantidad'] ?? 0 }}</td>
                    <td>Total vendido: {{ number_format($resumen['monto'] ?? 0, 2) }} Bs</td>
                </tr>
                </tbody>
            </table>

        @else
            {{-- Versión original sin agrupación (reporte simple) --}}
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
                @forelse($rows ?? [] as $row)
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

                <tr class="pdf-totales">
                    <td>Productos: {{ $resumen['productos'] ?? 0 }}</td>
                    <td>Cantidad total: {{ $resumen['cantidad'] ?? 0 }}</td>
                    <td colspan="2">Total vendido: {{ number_format($resumen['monto'] ?? 0, 2) }} Bs</td>
                </tr>
                </tbody>
            </table>
        @endif

    </div>
</div>
</body>
</html>
