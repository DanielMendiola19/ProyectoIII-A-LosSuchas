<?php

namespace App\Http\Controllers;

use App\Models\Producto;   // Usamos tu modelo existente
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    public function index()
    {
        $productos = Producto::select('id','nombre','imagen','stock')
                    ->orderBy('nombre')
                    ->get();

        return view('inventario.index', compact('productos'));
    }

    public function aumentar(Producto $producto, Request $request)
    {
        $data = $request->validate(['cantidad' => ['required','integer','min:1']]);

        $maxAumentar = 50 - (int)$producto->stock;
        if ($maxAumentar <= 0)
            return back()->with('error', 'Este producto ya tiene el máximo permitido (50).');

        if ($data['cantidad'] > $maxAumentar)
            return back()->with('error', "Solo puedes aumentar hasta {$maxAumentar} unidades.");

        DB::transaction(function() use ($producto, $data) {
            $producto->increment('stock', $data['cantidad']);
        });

        return back()->with('success', "Se aumentó el inventario en {$data['cantidad']} unidad(es).");
    }

    public function disminuir(Producto $producto, Request $request)
    {
        $data = $request->validate(['cantidad' => ['required','integer','min:1']]);

        $maxDisminuir = (int)$producto->stock;
        if ($maxDisminuir <= 0)
            return back()->with('error', 'No puedes disminuir: el inventario ya está en 0.');

        if ($data['cantidad'] > $maxDisminuir)
            return back()->with('error', "No puedes disminuir más de {$maxDisminuir} unidad(es).");

        DB::transaction(function() use ($producto, $data) {
            $producto->decrement('stock', $data['cantidad']);
        });

        return back()->with('success', "Se disminuyó el inventario en {$data['cantidad']} unidad(es).");
    }
}
