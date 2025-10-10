<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function index()
    {
        $productos = Producto::with('categoria')->get();

        $metodosPago = ['Efectivo', 'QR'];

        return view('pedido.index', compact('productos', 'metodosPago'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $pedido = Pedido::create([
                'fecha_pedido' => now(),
                'estado' => 'Pendiente',
                'metodo_pago' => $request->metodo_pago,
                'total' => $request->total,
                'usuario_id' => Auth::id(),
                'mesa_id' => null 
            ]);

            foreach ($request->productos as $producto) {
                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $producto['id'],
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $producto['precio']
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pedido exitoso']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
