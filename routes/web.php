<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;
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
})->middleware('auth')->name('dashboard');

// 🔒 Rutas protegidas por rol
// Solo Administrador puede acceder a los módulos de productos
Route::middleware(['auth', 'role:Administrador'])->group(function () {
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::put('/productos/{id}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');
});