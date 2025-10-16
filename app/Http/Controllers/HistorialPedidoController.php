<?php

namespace App\Http\Controllers;

use App\Models\Pedido;

class HistorialPedidoController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Pedido::with(['detalles.producto', 'usuario']);

        // Filtro por estado
        if ($request->filled('estado') && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        // Filtro por fecha
        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->fecha);
        }

        $pedidos = $query->orderByDesc('created_at')->get();

        return view('pedidos.historial', compact('pedidos'));
    }



    public function updateEstado($id, \Illuminate\Http\Request $request)
    {
        $request->validate([
            'estado' => ['required','string']
        ]);

        $estado = strtolower(trim($request->estado));
        $validos = ['pendiente','en preparación','listo','entregado'];

        if (!in_array($estado, $validos, true)) {
            return redirect()->route('pedidos.historial')
            ->with('error', 'Estado no válido');
        }

        $pedido = Pedido::findOrFail($id);

        // valdiacion flujo 
        $flujo = [
            'pendiente'      => 'en preparación',
            'en preparación' => 'listo',
            'listo'          => 'entregado',
            'entregado'      => null
        ];

        $estadoActual = strtolower($pedido->estado);

        if ($flujo[$estadoActual] !== $estado) {
            return redirect()->route('pedidos.historial');
        }

        $pedido->estado = $estado;
        $pedido->save();

        return redirect()->route('pedidos.historial')
        ->with('success', 'Estado actualizado correctamente');
    }

    public function show($id)
    {
        $pedido = Pedido::with('detalles.producto')->findOrFail($id);
        return view('pedidos.detalle', compact('pedido'));
    }
    

}
