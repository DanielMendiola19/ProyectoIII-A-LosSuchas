<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PerfilController extends Controller
{
    public function index()
    {
        return view('perfil.index');
    }

    public function cambiarPassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{}|;:\'",.<>\/?]).+$/',
                'confirmed'
            ],
        ], [
            'current_password.required' => 'Ingresa tu contraseña actual',
            'password.required' => 'Ingresa la nueva contraseña',
            'password.regex' => 'La contraseña debe tener mayúscula, minúscula, número y carácter especial',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->contrasena)) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta']);
        }

        $user->contrasena = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Contraseña actualizada correctamente');
    }
}