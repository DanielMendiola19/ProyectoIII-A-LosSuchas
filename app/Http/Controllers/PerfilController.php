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

    $intentos = session()->get('password_attempts', 0);

    if (!Hash::check($request->current_password, $user->contrasena)) {
        $intentos++;
        session(['password_attempts' => $intentos]);

        if ($intentos >= 3) {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Se ha cerrado sesión por 3 intentos fallidos de contraseña.');
        }

        return back()->with('error_modal', "No se pudo cambiar la contraseña. Intento $intentos de 3.");
    }

    session()->forget('password_attempts');

    $user->contrasena = Hash::make($request->password);
    $user->save();

    return back()->with('success_modal', '¡Felicidades! La contraseña se cambió correctamente.');
}

}