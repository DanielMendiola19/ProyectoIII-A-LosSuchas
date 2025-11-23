<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetallePedido;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
{
    // 1️⃣ Productos más vendidos (TOP 5)
    $masVendidos = DetallePedido::select('producto_id', DB::raw('SUM(cantidad) as total_vendidos'))
        ->groupBy('producto_id')
        ->orderByDesc('total_vendidos')
        ->take(5)
        ->with('producto')
        ->get();

    $labelsBarras = $masVendidos->pluck('producto.nombre')->toArray();
    $dataBarras = $masVendidos->pluck('total_vendidos')->toArray();

    // 2️⃣ Ventas por categoría (para gráfico de torta)
    $ventasCategorias = DetallePedido::join('productos', 'detalle_pedido.producto_id', '=', 'productos.id')
        ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
        ->select('categorias.nombre', DB::raw('SUM(detalle_pedido.cantidad) as total'))
        ->groupBy('categorias.nombre')
        ->get();

    $labelsTorta = $ventasCategorias->pluck('nombre')->toArray();
    $dataTorta = $ventasCategorias->pluck('total')->toArray();

    // 3️⃣ Evolución de ventas por día de la última semana (para gráfico de línea)
    $hoy = now();
    $hace14Dias = $hoy->copy()->subDays(13); // 13 + hoy = 14 días

    $ventasPorDia = collect();

    for ($i = 0; $i < 14; $i++) {
        $fecha = $hace14Dias->copy()->addDays($i)->format('Y-m-d');
        $total = DetallePedido::whereDate('created_at', $fecha)->sum('cantidad');
        $ventasPorDia->push([
            'dia' => $fecha,
            'total' => $total
        ]);
    }

    $labelsLinea = $ventasPorDia->pluck('dia')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))->toArray();
    $dataLinea = $ventasPorDia->pluck('total')->toArray();

    return view('dashboard', [
        'labelsBarras' => json_encode($labelsBarras),
        'dataBarras' => json_encode($dataBarras),
        'labelsTorta' => json_encode($labelsTorta),
        'dataTorta' => json_encode($dataTorta),
        'labelsLinea' => json_encode($labelsLinea),
        'dataLinea' => json_encode($dataLinea),
    ]);
}

}
