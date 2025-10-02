<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with('categoria')->get();
        $categorias = Categoria::all();
        return view('productos.index', compact('productos', 'categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'precio' => 'required|numeric',
            'stock' => 'required|integer',
            'categoria_id' => 'required|exists:categorias,id'
        ]);

        Producto::create($request->all());

        return back()->with('success', 'Producto agregado correctamente');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'precio' => 'required|numeric',
            'stock' => 'required|integer',
            'categoria_id' => 'required|exists:categorias,id'
        ]);

        $producto = Producto::findOrFail($id);
        $producto->update($request->all());

        return back()->with('success', 'Producto actualizado correctamente');
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->delete();

        return back()->with('success', 'Producto eliminado correctamente');
    }
}
