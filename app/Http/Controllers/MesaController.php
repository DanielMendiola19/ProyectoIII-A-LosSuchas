<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use Illuminate\Http\Request;

class MesaController extends Controller
{
    // Mostrar todas las mesas
    public function index()
    {
        $mesas = Mesa::all();
        $ocupadas = Mesa::where('estado', 'ocupada')->count();
        $disponibles = Mesa::where('estado', 'disponible')->count();

        return view('mesas.index', compact('mesas', 'ocupadas', 'disponibles'));
    }

    // Crear nueva mesa
    public function store(Request $request)
    {
        $request->validate([
            'numero_mesa' => 'required|integer|unique:mesas,numero_mesa',
            'capacidad' => 'required|integer|min:1|max:6',
            'estado' => 'required|in:ocupada,disponible',
            'pos_x' => 'nullable|integer',
            'pos_y' => 'nullable|integer',
        ]);

        $mesa = Mesa::create([
            'numero_mesa' => $request->numero_mesa,
            'capacidad' => $request->capacidad,
            'estado' => $request->estado,
            'pos_x' => $request->pos_x ?? 50,
            'pos_y' => $request->pos_y ?? 50,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mesa registrada correctamente',
            'mesa' => $mesa
        ]);
    }

    // Actualizar mesa existente
    public function update(Request $request, Mesa $mesa)
    {
        $request->validate([
            'numero_mesa' => 'required|integer|unique:mesas,numero_mesa,' . $mesa->id,
            'capacidad' => 'required|integer|min:1|max:6',
            'estado' => 'required|in:ocupada,disponible',
            'pos_x' => 'nullable|integer',
            'pos_y' => 'nullable|integer',
        ]);

        $mesa->update([
            'numero_mesa' => $request->numero_mesa,
            'capacidad' => $request->capacidad,
            'estado' => $request->estado,
            'pos_x' => $request->pos_x ?? $mesa->pos_x,
            'pos_y' => $request->pos_y ?? $mesa->pos_y,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mesa actualizada correctamente',
            'mesa' => $mesa
        ]);
    }

    // Eliminar mesa
    public function destroy(Mesa $mesa)
    {
        $mesa->delete();
        return response()->json([
            'success' => true,
            'message' => 'Mesa eliminada correctamente'
        ]);
    }

    // Verificar número de mesa en tiempo real (AJAX)
    public function verificarNumero($numero)
    {
        $existe = Mesa::where('numero_mesa', $numero)->exists();
        return response()->json(['existe' => $existe]);
    }

    public function guardarPosiciones(Request $request)
    {
        foreach ($request->mesas as $mesaData) {
            Mesa::where('id', $mesaData['id'])->update([
                'pos_x' => $mesaData['pos_x'],
                'pos_y' => $mesaData['pos_y'],
            ]);
        }

        return response()->json(['success' => true]);
    }

        public function actualizarPosicion(Request $request, $id)
    {
        $mesa = Mesa::findOrFail($id);
        $mesa->pos_x = $request->input('pos_x');
        $mesa->pos_y = $request->input('pos_y');
        $mesa->save();

        return response()->json(['success' => true]);
    }


}
