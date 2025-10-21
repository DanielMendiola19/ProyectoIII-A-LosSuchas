<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    /** 
     * Mostrar productos activos (no eliminados)
     */
    public function index()
    {
        $productos = Producto::with('categoria')->get(); // por defecto no incluye los soft deleted
        $categorias = Categoria::all();
        return view('productos.index', compact('productos', 'categorias'));
    }

    /**
     * Guardar un nuevo producto
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'precio' => 'required|numeric',
            'stock' => 'required|integer',
            'categoria_id' => 'required|exists:categorias,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $data = $request->only(['nombre','precio','stock','categoria_id']);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        Producto::create($data);

        return back()->with('success', 'Producto agregado correctamente');
    }

    /**
     * Actualizar producto existente
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'precio' => 'required|numeric',
            'stock' => 'required|integer',
            'categoria_id' => 'required|exists:categorias,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $producto = Producto::findOrFail($id);
        $data = $request->only(['nombre','precio','stock','categoria_id']);

        if ($request->hasFile('imagen')) {
            if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($data);
        return back()->with('success', 'Producto actualizado correctamente');
    }

    /**
     * Eliminar producto (borrado lógico)
     */
    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->delete(); // 👈 ahora solo marca deleted_at, no elimina la imagen

        return back()->with('success', 'Producto eliminado correctamente (borrado lógico)');
    }

    /**
     * Mostrar productos eliminados lógicamente
     */
    public function eliminados()
    {
        $productos = Producto::onlyTrashed()->with('categoria')->get();
        return view('productos.eliminados', compact('productos'));
    }

    /**
     * Restaurar producto eliminado
     */
    public function restaurar($id)
    {
        $producto = Producto::onlyTrashed()->findOrFail($id);
        $producto->restore();

        return redirect()->route('productos.eliminados')
            ->with('success', 'Producto restaurado correctamente');
    }
}
