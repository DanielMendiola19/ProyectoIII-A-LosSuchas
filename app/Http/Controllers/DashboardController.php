<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetallePedido;
use App\Models\Mesa;
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

// 2️⃣ Mesas por estado (para gráfico de torta)
    $mesasPorEstado = Mesa::select('estado', DB::raw('COUNT(*) as total'))
        ->groupBy('estado')
        ->get();

    $labelsTorta = $mesasPorEstado->pluck('estado')->toArray(); // ["Disponible","Ocupada","Mantenimiento"]
    $dataTorta   = $mesasPorEstado->pluck('total')->toArray();


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
