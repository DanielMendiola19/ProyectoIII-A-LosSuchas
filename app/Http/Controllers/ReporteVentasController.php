<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; 

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

    /**
     * Exportar a PDF el reporte de ventas (rango o diario).
     */
    public function exportPdf(Request $request)
    {
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        if (!$desde || !$hasta) {
            // si vienen vacíos, tomamos hoy
            $desde = $hasta = now()->toDateString();
        }

        [$rows, $resumen] = $this->getVentasData($desde, $hasta);

        $pdf = PDF::loadView('reportes.pdf_ventas', [
            'desde'   => $desde,
            'hasta'   => $hasta,
            'rows'    => $rows,
            'resumen' => $resumen,
        ])->setPaper('a4', 'portrait');

        $fileName = 'reporte_ventas_' . $desde . '_' . $hasta . '_' . now()->format('His') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Lógica central: agrupar ventas por producto en un rango de fechas.
     */
    protected function getVentasData(string $desde, string $hasta): array
    {
        $rows = DB::table('detalle_pedido as dp')
            ->join('pedidos as pe', 'dp.pedido_id', '=', 'pe.id')
            ->join('productos as p', 'dp.producto_id', '=', 'p.id')
            ->leftJoin('categorias as c', 'p.categoria_id', '=', 'c.id')
            // solo pedidos entregados cuentan como venta
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
}
