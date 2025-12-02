<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; 
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ReporteVentasController extends Controller
{
    public function index()
    {
        $fechasDiarias = DB::table('pedidos')
            ->selectRaw('DATE(created_at) as fecha')
            ->whereIn('estado', ['Entregado'])
            ->distinct()
            ->orderByDesc('fecha')
            ->limit(15)
            ->get();

        return view('reportes.index', compact('fechasDiarias'));
    }

    public function data(Request $request)
    {
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        if (!$desde || !$hasta) {
            return response()->json([
                'rows'    => [],
                'resumen' => [
                    'productos' => 0,
                    'cantidad'  => 0,
                    'monto'     => 0,
                ],
            ]);
        }

        try {
            [$rows, $resumen] = $this->getVentasData($desde, $hasta);

            return response()->json([
                'rows'    => $rows,
                'resumen' => $resumen,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error'   => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

   
    public function exportPdf(Request $request)
    {
        $desde      = $request->input('desde');
        $hasta      = $request->input('hasta');
        $agrupacion = $request->input('agrupacion'); 

        if (!$desde || !$hasta) {
            $desde = $hasta = now()->toDateString();
        }

        if ($agrupacion) {
            [$ventasPorPeriodo, $resumen, $agrupacionFinal] =
                $this->getVentasDataPorPeriodo($desde, $hasta, $agrupacion);

            $pdf = Pdf::loadView('reportes.pdf_ventas', [
                'desde'            => $desde,
                'hasta'            => $hasta,
                'ventasPorPeriodo' => $ventasPorPeriodo,
                'resumen'          => $resumen,
                'agrupacion'       => $agrupacionFinal,
            ])->setPaper('a4', 'portrait');
        } else {
            [$rows, $resumen] = $this->getVentasData($desde, $hasta);

            $pdf = Pdf::loadView('reportes.pdf_ventas', [
                'desde'   => $desde,
                'hasta'   => $hasta,
                'rows'    => $rows,
                'resumen' => $resumen,
            ])->setPaper('a4', 'portrait');
        }

        $fileName = 'reporte_ventas_' . $desde . '_' . $hasta . '_' . now()->format('His') . '.pdf';

        return $pdf->download($fileName);
    }

   
    protected function getVentasData(string $desde, string $hasta): array
    {
        $rows = DB::table('detalle_pedido as dp')
            ->join('pedidos as pe', 'dp.pedido_id', '=', 'pe.id')
            ->join('productos as p', 'dp.producto_id', '=', 'p.id')
            ->leftJoin('categorias as c', 'p.categoria_id', '=', 'c.id')
            ->whereIn('pe.estado', ['Entregado'])
            ->whereDate('pe.created_at', '>=', $desde)
            ->whereDate('pe.created_at', '<=', $hasta)
            ->groupBy('p.id', 'p.nombre', 'c.nombre')
            ->selectRaw('
                p.nombre as producto,
                COALESCE(c.nombre, "Sin categoría") as categoria,
                SUM(dp.cantidad) as cantidad,
                SUM(dp.cantidad * dp.precio_unitario) as total
            ')
            ->orderBy('p.nombre')
            ->get();

        $resumen = [
            'productos' => $rows->count(),
            'cantidad'  => $rows->sum('cantidad'),
            'monto'     => $rows->sum('total'),
        ];

        return [$rows, $resumen];
    }

   
    protected function getVentasDataPorPeriodo(string $desde, string $hasta, string $agrupacion = 'dia'): array
    {
        // Establecer idioma de Carbon a español
        \Carbon\Carbon::setLocale('es');
        setlocale(LC_TIME, 'es_ES.UTF-8'); // para sistemas Linux/macOS
        setlocale(LC_TIME, 'Spanish_Spain.1252'); // para Windows

        // Normalizamos el tipo
        switch ($agrupacion) {
            case 'mes':
                $periodoSelect   = "DATE_FORMAT(pe.created_at, '%Y-%m') as periodo";
                $periodoGroup    = DB::raw("DATE_FORMAT(pe.created_at, '%Y-%m')");
                $agrupacionFinal = 'mes';
                break;

            case 'semana':
                $periodoSelect   = "YEARWEEK(pe.created_at, 1) as periodo";
                $periodoGroup    = DB::raw("YEARWEEK(pe.created_at, 1)");
                $agrupacionFinal = 'semana';
                break;

            case 'dia':
            default:
                $periodoSelect   = "DATE(pe.created_at) as periodo";
                $periodoGroup    = DB::raw("DATE(pe.created_at)");
                $agrupacionFinal = 'dia';
                break;
        }

        $rows = DB::table('detalle_pedido as dp')
            ->join('pedidos as pe', 'dp.pedido_id', '=', 'pe.id')
            ->join('productos as p', 'dp.producto_id', '=', 'p.id')
            ->leftJoin('categorias as c', 'p.categoria_id', '=', 'c.id')
            ->whereIn('pe.estado', ['Entregado'])
            ->whereDate('pe.created_at', '>=', $desde)
            ->whereDate('pe.created_at', '<=', $hasta)
            ->groupBy($periodoGroup, 'p.id', 'p.nombre', 'c.nombre')
            ->selectRaw("
                $periodoSelect,
                p.nombre as producto,
                COALESCE(c.nombre, 'Sin categoría') as categoria,
                SUM(dp.cantidad) as cantidad,
                SUM(dp.cantidad * dp.precio_unitario) as total
            ")
            ->orderBy($periodoGroup)
            ->orderBy('p.nombre')
            ->get();

        // Agrupamos en Laravel para formatear el nombre del periodo
        $ventasPorPeriodo = $rows->groupBy(function ($r) use ($agrupacionFinal) {
            if ($agrupacionFinal === 'semana') {
                // periodo viene como YEARWEEK: 202542
                $year = (int) substr($r->periodo, 0, 4);
                $week = (int) substr($r->periodo, 4);

                $monday = Carbon::now()->setISODate($year, $week)->startOfWeek();
                $monthName = ucfirst($monday->translatedFormat('F')); // Octubre, Noviembre, etc.
                $day = (int) $monday->format('j');
                $weekOfMonth = intdiv($day - 1, 7) + 1; // 1 al 4 (a veces 5)

                return "Semana {$weekOfMonth} de {$monthName} {$year}";
            }

            if ($agrupacionFinal === 'mes') {
                $year  = (int) substr($r->periodo, 0, 4);
                $month = (int) substr($r->periodo, 5, 2);

                $date = Carbon::createFromDate($year, $month, 1);
                $monthName = ucfirst($date->translatedFormat('F'));

                return "{$monthName} {$year}";
            }

            return $r->periodo; // YYYY-MM-DD
        });

        $resumen = [
            'productos' => $rows->unique('producto')->count(),
            'cantidad'  => $rows->sum('cantidad'),
            'monto'     => $rows->sum('total'),
            'periodos'  => $ventasPorPeriodo->count(), // cantidad de días / semanas / meses
        ];

        return [$ventasPorPeriodo, $resumen, $agrupacionFinal];
    }
    public function apiUltimos7(): JsonResponse
{
    // Fecha límite: hoy menos 7 días (incluye hoy)
    $desde = now()->subDays(6)->toDateString(); // incluye hoy y 6 días atrás -> 7 días
    $hasta = now()->toDateString();

    // Obtener las fechas con pedidos entregados dentro del rango
    $fechas = DB::table('pedidos')
        ->selectRaw('DATE(created_at) as fecha')
        ->where('estado', 'Entregado')
        ->whereBetween(DB::raw('DATE(created_at)'), [$desde, $hasta])
        ->distinct()
        ->orderByDesc('fecha')
        ->get()
        ->pluck('fecha'); // devuelve colección de strings YYYY-MM-DD

    // Normalizar a array simple
    return response()->json([
        'desde'  => $desde,
        'hasta'  => $hasta,
        'fechas' => $fechas->values(), // array de fechas
    ]);
}
}
