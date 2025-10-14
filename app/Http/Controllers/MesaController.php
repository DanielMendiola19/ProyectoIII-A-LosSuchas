<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use Illuminate\Http\Request;

class MesaController extends Controller
{
    public function index()
    {
        $mesas = Mesa::all();
        $ocupadas = Mesa::where('estado', 'ocupada')->count();
        $disponibles = Mesa::where('estado', 'disponible')->count();

        return view('mesas.index', compact('mesas', 'ocupadas', 'disponibles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero_mesa' => 'required|integer|unique:mesas,numero_mesa',
            'capacidad' => 'required|integer|min:1',
            'estado' => 'required|in:ocupada,disponible'
        ]);

        Mesa::create($request->all());

        return response()->json(['success' => true, 'message' => 'Mesa registrada correctamente']);
    }

    public function update(Request $request, Mesa $mesa)
    {
        $request->validate([
            'numero_mesa' => 'required|integer|unique:mesas,numero_mesa,' . $mesa->id,
            'capacidad' => 'required|integer|min:1',
            'estado' => 'required|in:ocupada,disponible'
        ]);

        $mesa->update($request->all());

        return response()->json(['success' => true, 'message' => 'Mesa actualizada correctamente']);
    }

    public function destroy(Mesa $mesa)
    {
        $mesa->delete();
        return response()->json(['success' => true, 'message' => 'Mesa eliminada correctamente']);
    }

    // Verificar número de mesa en tiempo real (AJAX)
    public function verificarNumero($numero)
    {
        $existe = Mesa::where('numero_mesa', $numero)->exists();
        return response()->json(['existe' => $existe]);
    }
}
