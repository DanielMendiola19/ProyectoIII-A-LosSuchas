<?php

namespace App\Http\Controllers;

use App\Models\Pedido;

class HistorialPedidoController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Pedido::with([
            'detalles.producto', // 🔹 Esto ahora incluirá productos eliminados gracias a la relación modificada
            'usuario'
        ]);

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
            return response()->json(['error' => 'Estado no válido'], 422);
        }

        $pedido = Pedido::findOrFail($id);

        // Validación del flujo
        $flujo = [
            'pendiente'      => 'en preparación',
            'en preparación' => 'listo',
            'listo'          => 'entregado',
            'entregado'      => null
        ];

        $estadoActual = strtolower($pedido->estado);

        if ($flujo[$estadoActual] !== $estado) {
            return response()->json(['error' => 'Transición de estado no válida'], 422);
        }

        $pedido->estado = $estado;
        $pedido->save();

        return response()->json(['success' => 'Estado actualizado correctamente']);
    }

    public function show($id)
    {
        // 🔹 CONSULTA MODIFICADA: Incluir productos eliminados en el detalle
        $pedido = Pedido::with([
            'detalles' => function($query) {
                $query->with(['producto' => function($q) {
                    $q->withTrashed(); // 🔹 Esto fuerza a incluir productos eliminados
                }]);
            },
            'usuario'
        ])->findOrFail($id);
        
        return view('pedidos.detalle', compact('pedido'));
    }

    public function apiHistorial()
    {
        $hoy = now()->toDateString();

        $pedidos = Pedido::with([
            'detalles.producto' => function ($q) {
                $q->withTrashed(); // incluir productos eliminados
            },
            'usuario',
            'mesa' // <-- agregamos mesa
        ])
        ->whereDate('created_at', $hoy)
        ->orderByDesc('created_at')
        ->get();

        // 🔹 Formato para la app móvil: incluir número de mesa explícitamente
        $pedidosFormateados = $pedidos->map(function($pedido) {
            return [
                'id' => $pedido->id,
                'estado' => $pedido->estado,
                'numero_mesa' => $pedido->mesa ? $pedido->mesa->numero_mesa : null,
                'detalles' => $pedido->detalles
            ];
        });

        return response()->json($pedidosFormateados);
    }

    public function apiUpdateEstado($id, \Illuminate\Http\Request $request)
    {
        $request->validate([
            'estado' => 'required|string'
        ]);

        $estado = strtolower(trim($request->estado));

        $validos = ['pendiente', 'en preparación', 'listo', 'entregado'];

        if (!in_array($estado, $validos)) {
            return response()->json(['error' => 'Estado no válido'], 422);
        }

        $pedido = Pedido::findOrFail($id);

        $flujo = [
            'pendiente'      => 'en preparación',
            'en preparación' => 'listo',
            'listo'          => 'entregado',
            'entregado'      => null
        ];

        $estadoActual = strtolower($pedido->estado);

        if ($flujo[$estadoActual] !== $estado) {
            return response()->json(['error' => 'Transición no permitida'], 422);
        }

        $pedido->estado = $estado;
        $pedido->save();

        return response()->json(['success' => 'Estado actualizado']);
    }


}