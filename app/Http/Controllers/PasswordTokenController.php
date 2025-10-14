<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PasswordToken;
use App\Models\Usuario;
use Carbon\Carbon;
use App\Jobs\ExpireTokenJob;
use App\Mail\SendTokenMail;
use Illuminate\Support\Facades\Mail;


class PasswordTokenController extends Controller
{
    /**
     * Mostrar formulario de recuperación
     */
    public function showForgotPassword()
    {
        if (session()->has('correo_recuperacion') && Carbon::now()->lessThan(session('correo_expira'))) {
            return redirect()->route('password.verify.code.form');
        }
        return view('auth.forgot-password');
    }


    /**
     * Validar correo y generar token
     */
    public function sendToken(Request $request)
    {
        $request->validate([
            'correo' => ['required', 'email', 'exists:usuarios,correo'],
        ], [
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'Formato de correo inválido.',
            'correo.exists' => 'El correo no está registrado.',
        ]);

        $correo = $request->correo;

        // Expirar tokens anteriores
        PasswordToken::where('correo', $correo)
            ->where('estado', 'activo')
            ->update(['estado' => 'expirado']);

        // Generar token de 6 dígitos
        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = Carbon::now()->addMinutes(3);

        $tokenModel = PasswordToken::create([
            'correo' => $correo,
            'token' => $token,
            'estado' => 'activo',
            'expires_at' => $expiresAt,
        ]);
        Mail::to($correo)->send(new SendTokenMail($correo, $token));
        ExpireTokenJob::dispatch($tokenModel)->delay(now()->addMinutes(3));

        // Guardar correo y expiración de sesión
        session([
            'correo_recuperacion' => $correo,
            'correo_expira' => Carbon::now()->addMinutes(10) // sesión válida 10min
        ]);

        return redirect()->route('password.verify.code.form')
                        ->with('success', 'Se generó un código de verificación. (Temporal)');
    }

    /**
     * Mostrar formulario para ingresar el código
     */
    public function showVerifyCode()
    {
        // Si no existe sesión → acceso denegado (sin mensaje)
        if (!session()->has('correo_recuperacion')) {
            return redirect()->route('password.request');
        }

        // Si expiró → limpiar y mostrar mensaje
        if (Carbon::now()->greaterThan(session('correo_expira'))) {
            session()->forget(['correo_recuperacion', 'correo_expira']);
            return redirect()->route('password.request')
                            ->with('error', 'El tiempo de espera acabó. Ingresa nuevamente tu correo.');
        }

        $correo = session('correo_recuperacion');

        $ultimoToken = PasswordToken::where('correo', $correo)
                            ->orderByDesc('created_at')
                            ->first();

        if ($ultimoToken && $ultimoToken->estado === 'usado') {
            return redirect()->route('password.reset.form')
                            ->with('success', 'Ya verificaste el código. Cambia tu contraseña.');
        }

        return view('auth.verify-code');
    }



    /**
     * Validar token ingresado
     */
    public function checkCode(Request $request)
    {
        // Validar inputs del código
        $request->validate([
            'codigo1' => 'required|digits:1',
            'codigo2' => 'required|digits:1',
            'codigo3' => 'required|digits:1',
            'codigo4' => 'required|digits:1',
            'codigo5' => 'required|digits:1',
            'codigo6' => 'required|digits:1',
        ], [
            'required' => 'Todos los campos son obligatorios.',
            'digits' => 'Cada campo debe tener un solo dígito.',
        ]);

        // Concatenar el código
        $codigo = implode('', [
            $request->codigo1, $request->codigo2, $request->codigo3,
            $request->codigo4, $request->codigo5, $request->codigo6
        ]);

        $correo = session('correo_recuperacion');

        // Buscar token activo
        $token = PasswordToken::where('correo', $correo)
                    ->where('estado', 'activo')
                    ->orderByDesc('created_at')
                    ->first();

        if (!$token) {
            return back()->with('error', 'No hay un código activo. Genera uno nuevo.');
        }

        // Verificar expiración
        if ($token->isExpired()) {
            $token->markAsExpired();
            session()->forget(['correo_recuperacion', 'correo_expira']);
            return redirect()->route('password.request')
                            ->with('error', 'El token expiró. Ingresa nuevamente tu correo.');
        }

        // Verificar coincidencia
        if ($token->token !== $codigo) {
            return back()->with('error', 'El código ingresado es incorrecto.');
        }

        // Marcar token como usado
        $token->markAsUsed();

        // Redirigir a restablecer contraseña
        return redirect()->route('password.reset.form')
                        ->with('success', 'Código verificado correctamente. Ahora puedes cambiar tu contraseña.');
    }

    /**
     * Reenviar token
     */
    public function resendToken(Request $request)
    {
        $correo = session('correo_recuperacion');

        if (!$correo) {
            return response()->json(['error' => 'No hay sesión de correo activa.'], 422);
        }

        // Expirar tokens anteriores
        PasswordToken::where('correo', $correo)
            ->where('estado', 'activo')
            ->update(['estado' => 'expirado']);

        // Generar nuevo token
        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = Carbon::now()->addMinutes(3);

        $nuevoToken = PasswordToken::create([
            'correo' => $correo,
            'token' => $token,
            'estado' => 'activo',
            'expires_at' => $expiresAt,
        ]);
        Mail::to($correo)->send(new SendTokenMail($correo, $token));
        ExpireTokenJob::dispatch($nuevoToken)->delay(now()->addMinutes(3));

        return response()->json([
            'success' => true,
            'token' => $nuevoToken->token,
            'expires_at' => $nuevoToken->expires_at->toDateTimeString(),
        ]);
    }


    public function clearSession(Request $request)
    {
        session()->forget(['correo_recuperacion', 'correo_expira']);

        return redirect()->route('password.request')
                        ->with('info', 'Has cerrado la sesión de recuperación. Puedes ingresar otro correo.');
    }

    public function showResetPassword()
    {
        // Si no hay sesión → acceso denegado directo sin mensaje
        if (!session()->has('correo_recuperacion')) {
            return redirect()->route('password.request');
        }

        // Si expiró → limpiar y mostrar mensaje
        if (Carbon::now()->greaterThan(session('correo_expira'))) {
            session()->forget(['correo_recuperacion', 'correo_expira']);
            return redirect()->route('password.request')
                            ->with('error', 'El tiempo de sesión expiró. Ingresa nuevamente tu correo.');
        }

        $correo = session('correo_recuperacion');

        $ultimoToken = PasswordToken::where('correo', $correo)
                        ->orderByDesc('created_at')
                        ->first();

        if (!$ultimoToken || $ultimoToken->estado !== 'usado') {
            return redirect()->route('password.verify.code.form')
                            ->with('error', 'Primero debes verificar tu código de seguridad.');
        }

        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $rules = [
            'password' => [
                'required',
                'string',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{}|;:\'",.<>\/?]).{8,}$/',
                'confirmed',
            ],
        ];

        $messages = [
            'password.required' => 'Ingresa una nueva contraseña.',
            'password.regex' => 'La contraseña debe incluir mayúsculas, minúsculas, números y símbolos.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ];

        $validated = $request->validate($rules, $messages);

        $correo = session('correo_recuperacion');

        if (!$correo) {
            return redirect()->route('password.request')
                            ->with('error', 'No hay una sesión activa de recuperación.');
        }

        $usuario = Usuario::where('correo', $correo)->first();

        if (!$usuario) {
            return redirect()->route('password.request')
                            ->with('error', 'No se encontró el usuario asociado a este correo.');
        }

        try {
            // Actualizar contraseña
            $usuario->update([
                'contrasena' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            ]);

            // Limpiar sesión de recuperación
            session()->forget(['correo_recuperacion', 'correo_expira']);

            return redirect()->route('login.form')->with('success', 'Tu contraseña fue restablecida correctamente. Inicia sesión.');

        } catch (\Exception $e) {
            \Log::error('Error al restablecer contraseña: '.$e->getMessage());
            return back()->with('error', 'Ocurrió un error al actualizar la contraseña. Inténtalo nuevamente.');
        }
    }


}
