<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class AuthController extends Controller
{
    public function showSignUpForm()
    {
        $roles = Rol::where('nombre', '!=', 'Administrador')->get();
        return view('signup', compact('roles'));
    }

    public function signUp(Request $request)
    {
        $rules = [
            'nombre' => ['required', 'string', 'min:2', 'max:80', 'regex:/^[A-Za-zÀ-ÿ]+(?: [A-Za-zÀ-ÿ]+)*$/'],
            'apellido' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[A-Za-zÀ-ÿ]+(?: [A-Za-zÀ-ÿ]+)*$/'],
            'correo' => ['required', 'email', 'max:100', 'unique:usuarios,correo'],
            'password' => ['required', 'string', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{}|;:\'",.<>\/?]).{8,}$/'],
            'rol_id' => ['required', 'exists:roles,id'],
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

            'correo.required' => 'Ingresa tu correo',
            'correo.email' => 'Correo no válido',
            'correo.max' => 'El correo no puede superar 100 caracteres',
            'correo.unique' => 'El correo ya está registrado',

            'rol_id.required' => 'Selecciona un rol',
            'rol_id.exists' => 'Rol inválido',

            'password.required' => 'Ingresa una contraseña',
        ];

        $validated = $request->validate($rules, $messages);

        $nombre = htmlspecialchars($validated['nombre']);
        $apellido = htmlspecialchars($validated['apellido']);
        $correo = htmlspecialchars($validated['correo']);

        try {
            Usuario::create([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'correo' => $correo,
                'contrasena' => Hash::make($validated['password']),
                'rol_id' => $validated['rol_id'],
            ]);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Cuenta creada correctamente'], 200);
            }

            return redirect()->route('login.form')->with('success', 'Cuenta creada correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al crear usuario: '.$e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Ocurrió un error al registrar el usuario.',
                    'details' => $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['general' => 'Ocurrió un error al registrar el usuario.']);
        }
    }

    public function validateField(Request $request)
    {
        $fields = array_diff(array_keys($request->all()), ['_token']);
        $field = array_shift($fields);

        $rules = [
            'nombre' => ['required','string','min:2','max:80','regex:/^[A-Za-zÀ-ÿ]+(?: [A-Za-zÀ-ÿ]+)*$/'],
            'apellido' => ['required','string','min:2','max:100','regex:/^[A-Za-zÀ-ÿ]+(?: [A-Za-zÀ-ÿ]+)*$/'],
            'rol_id' => ['required','exists:roles,id'],
            'correo' => ['required','email','max:100','unique:usuarios,correo'],
            'password' => ['required','string','regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{}|;:\'",.<>\/?]).{8,}$/'],
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

            'correo.required' => 'Ingresa tu correo',
            'correo.email' => 'Correo no válido',
            'correo.max' => 'El correo no puede superar 100 caracteres',
            'correo.unique' => 'El correo ya está registrado',

            'rol_id.required' => 'Selecciona un rol',
            'rol_id.exists' => 'Rol inválido',

            'password.required' => 'Ingresa una contraseña',
        ];

        if (!$field || !array_key_exists($field, $rules)) {
            return response()->json(['errors' => ['general' => 'Campo inválido']], 422);
        }

        $validator = \Validator::make($request->all(), [$field => $rules[$field]], $messages);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();

            // Para password.regex, mostrar mensaje general si existe y no está vacío
            if(isset($errors['password']) && !empty($request->input('password'))) {
                $errors['password'] = ['La contraseña no cumple con los requisitos'];
            }

            return response()->json(['errors' => $errors], 422);
        }

        return response()->json(['message' => 'ok'], 200);
    }





    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'password' => 'required|string',
        ], [
            'correo.required' => 'Ingresa tu correo',
            'correo.email' => 'Correo no válido',
            'password.required' => 'Ingresa tu contraseña'
        ]);

        $usuario = Usuario::where('correo', $request->correo)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->contrasena)) {
            $message = 'Credenciales incorrectas';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 401);
            }
            return back()->withErrors(['general' => $message])->withInput();
        }

        // Iniciar sesión
        Auth::login($usuario);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Inicio de sesión exitoso',
                'usuario' => $usuario
            ], 200);
        }
        //return redirect('/bienvenida'); // Redirige al dashboard
        return redirect('/');
    }

    public function loginApi(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'password' => 'required|string',
        ]);

        $usuario = Usuario::where('correo', $request->correo)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->contrasena)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'usuario' => $usuario
        ], 200);
    }

    

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

}