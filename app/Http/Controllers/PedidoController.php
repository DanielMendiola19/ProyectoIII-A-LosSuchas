<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Producto;
use App\Models\Mesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function index()
    {
        // Traer solo productos con stock > 0
        $productos = Producto::with('categoria')
            ->where('stock', '>', 0)
            ->get();

        // Traer mesas disponibles
        $mesas = Mesa::where('estado', 'disponible')->get();

        $metodosPago = ['Efectivo', 'QR'];

        return view('pedido.index', compact('productos', 'metodosPago', 'mesas'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $mesa = Mesa::findOrFail($request->mesa_id);

            if ($mesa->estado === 'ocupada') {
                return response()->json([
                    'success' => false,
                    'message' => "La mesa {$mesa->numero_mesa} ya está ocupada."
                ], 400);
            }

            $pedido = Pedido::create([
                'fecha_pedido' => now(),
                'estado' => 'Pendiente',
                'metodo_pago' => $request->metodo_pago,
                'total' => $request->total,
                'usuario_id' => Auth::id(),
                'mesa_id' => $mesa->id
            ]);

            foreach ($request->productos as $producto) {
                $prod = Producto::findOrFail($producto['id']);

                if ($producto['cantidad'] > $prod->stock) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuficiente para el producto: {$prod->nombre}"
                    ], 400);
                }

                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $producto['id'],
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $producto['precio']
                ]);

                $prod->decrement('stock', $producto['cantidad']);
            }

            // Cambiar mesa a ocupada
            $mesa->update(['estado' => 'ocupada']);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pedido exitoso']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
