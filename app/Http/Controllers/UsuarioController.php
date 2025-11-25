<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $rolFiltro = $request->rol;

        // Solo usuarios que no están eliminados
        $usuarios = Usuario::with('rol')
            ->whereNull('deleted_at')
            ->whereHas('rol', fn($q) => $q->where('nombre', '!=', 'Administrador'))
            ->when($rolFiltro, fn($q) => $q->where('rol_id', $rolFiltro))
            ->orderBy('id', 'asc')
            ->get();

        $roles = Rol::where('nombre', '!=', 'Administrador')->get();

        return view('usuarios.index', compact('usuarios', 'roles', 'rolFiltro'));
    }

    public function edit($id)
    {
        $usuario = Usuario::with('rol')->whereNull('deleted_at')->findOrFail($id);

        if ($usuario->rol->nombre === 'Administrador') {
            return redirect()->route('usuarios.index')
                            ->with('error', 'No se puede editar al Administrador.');
        }

        $roles = Rol::where('nombre', '!=', 'Administrador')->get();
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::whereNull('deleted_at')->findOrFail($id);

        if($usuario->rol->nombre === 'Administrador'){
            return redirect()->route('usuarios.index')
                            ->with('error', 'No se puede actualizar al Administrador.');
        }

        $rules = [
            'nombre'   => ['required','string','min:2','max:80','regex:/^[A-Za-zÀ-ÿ]+(?: [A-Za-zÀ-ÿ]+)*$/'],
            'apellido' => ['required','string','min:2','max:100','regex:/^[A-Za-zÀ-ÿ]+(?: [A-Za-zÀ-ÿ]+)*$/'],
            'rol_id'   => ['required','exists:roles,id'],
        ];

        $messages = [
            'nombre.required' => 'Ingresa tu nombre',
            'nombre.regex' => 'El nombre solo puede contener letras y un espacio entre palabras',
            'nombre.min' => 'El nombre debe tener al menos 2 caracteres',
            'nombre.max' => 'El nombre no puede superar 80 caracteres',

            'apellido.required' => 'Ingresa tu apellido',
            'apellido.regex' => 'El apellido solo puede contener letras y un espacio entre palabras',
            'apellido.min' => 'El apellido debe tener al menos 2 caracteres',
            'apellido.max' => 'El apellido no puede superar 100 caracteres',

            'rol_id.required' => 'Selecciona un rol',
            'rol_id.exists' => 'Rol inválido',
        ];

        try {
            $validated = $request->validate($rules, $messages);

            $usuario->update([
                'nombre' => htmlspecialchars($validated['nombre']),
                'apellido' => htmlspecialchars($validated['apellido']),
                'rol_id' => $validated['rol_id'],
            ]);

            return redirect()->route('usuarios.edit', $usuario->id)
                                ->with('success', 'Usuario actualizado correctamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                            ->withErrors($e->validator)
                            ->withInput();
        }
    }

    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);

        if ($usuario->rol->nombre === 'Administrador') {
            return redirect()->route('usuarios.index')
                            ->with('error', 'No se puede eliminar al Administrador.');
        }

        // Eliminado lógico
        $usuario->delete();

        return back()->with('success', 'Usuario eliminado correctamente');
    }

    public function eliminados()
    {
        $usuarios = Usuario::onlyTrashed()
            ->with('rol')
            ->whereHas('rol', fn($q) => $q->where('nombre', '!=', 'Administrador'))
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('usuarios.eliminados', compact('usuarios'));
    }

    public function restaurar($id)
    {
        $usuario = Usuario::onlyTrashed()->findOrFail($id);
        $usuario->restore();

        return back()->with('success', 'Usuario restaurado correctamente');
    }

}
