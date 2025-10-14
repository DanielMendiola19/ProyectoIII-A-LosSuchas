<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\PasswordTokenController;
use Illuminate\Support\Facades\Auth;

// Página inicial -> splash
Route::get('/', function () {
    return view('splash');
})->name('splash');

// Formularios de autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::get('/signup', [AuthController::class, 'showSignUpForm'])->name('signup.form');

// Acciones POST
Route::post('/signup', [AuthController::class, 'signUp'])->name('signup');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Validación en tiempo real
Route::post('/signup/validate', [AuthController::class, 'validateField'])->name('signup.validate');

// Página de bienvenida (solo usuarios logueados)
Route::get('/bienvenida', function () {
    $usuario = Auth::user();
    return view('bienvenida', compact('usuario'));
})->middleware('auth')->name('bienvenida');

// Cerrar sesión
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard (accesible solo a usuarios autenticados)
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// 🔒 Rutas protegidas por rol
Route::middleware(['auth', 'role:Administrador'])->group(function () {
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::put('/productos/{id}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');
});




// =========================
// Recuperación de contraseña
// =========================
Route::get('/forgot-password', [PasswordTokenController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [PasswordTokenController::class, 'sendToken'])->name('password.send');

Route::get('/verify-code', [PasswordTokenController::class, 'showVerifyCode'])->name('password.verify.code.form');
Route::post('/verify-code', [PasswordTokenController::class, 'checkCode'])->name('password.check.code');

Route::get('/reset-password', [PasswordTokenController::class, 'showResetPassword'])->name('password.reset.form');
Route::post('/reset-password', [PasswordTokenController::class, 'resetPassword'])->name('password.reset');


//Generar token
Route::get('/generar-token', [PasswordTokenController::class, 'generateToken'])->name('token.generate');

//Reenviar token
Route::post('/resend-token', [PasswordTokenController::class, 'resendToken'])
    ->name('password.resend');

// Clear session data
Route::post('/forgot-password/clear', [PasswordTokenController::class, 'clearSession'])
    ->name('password.clear.session');


