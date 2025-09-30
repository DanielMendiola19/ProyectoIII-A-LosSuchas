<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Mostrar el formulario
    public function showSignUpForm()
    {
        $roles = Rol::all(); // traemos los roles para el select
        return view('signup', compact('roles'));
    }

    // Procesar registro
    public function signUp(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:80',
            'apellido' => 'required|string|max:100',
            'correo' => 'required|email|unique:usuarios,correo',
            'password' => 'required|string|min:6',
            'rol_id' => 'required|exists:roles,id',
        ]);

        Usuario::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'correo' => $request->correo,
            'contrasena' => Hash::make($request->password),
            'rol_id' => $request->rol_id,
        ]);

        return redirect()->route('login.form')->with('success', 'Cuenta creada correctamente.');
    }
}
