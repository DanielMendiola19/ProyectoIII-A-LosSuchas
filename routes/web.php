<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\PasswordTokenController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\MesaController;
use App\Http\Controllers\HistorialPedidoController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\PerfilController;


// =========================
// RUTAS PÚBLICAS
// =========================

// Splash page
Route::get('/', fn() => view('splash'))->name('splash');

// Login / Signup
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::get('/signup', [AuthController::class, 'showSignUpForm'])->name('signup.form');
Route::post('/signup', [AuthController::class, 'signUp'])->name('signup');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/signup/validate', [AuthController::class, 'validateField'])->name('signup.validate');



// Menu y Dashboard (accesibles sin login)
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

// =========================
// RECUPERACIÓN DE CONTRASEÑA (pública)
// =========================
Route::get('/forgot-password', [PasswordTokenController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [PasswordTokenController::class, 'sendToken'])->name('password.send');
Route::get('/verify-code', [PasswordTokenController::class, 'showVerifyCode'])->name('password.verify.code.form');
Route::post('/verify-code', [PasswordTokenController::class, 'checkCode'])->name('password.check.code');
Route::get('/reset-password', [PasswordTokenController::class, 'showResetPassword'])->name('password.reset.form');
Route::post('/reset-password', [PasswordTokenController::class, 'resetPassword'])->name('password.reset');
Route::get('/generar-token', [PasswordTokenController::class, 'generateToken'])->name('token.generate');
Route::post('/resend-token', [PasswordTokenController::class, 'resendToken'])->name('password.resend');
Route::post('/forgot-password/clear', [PasswordTokenController::class, 'clearSession'])->name('password.clear.session');

// =========================
// RUTAS AUTENTICADAS
// =========================
Route::middleware(['auth'])->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Bienvenida
    Route::get('/bienvenida', fn() => view('bienvenida', ['usuario' => Auth::user()]))->name('bienvenida');

    // =========================
    // Productos e Información -> solo Admin
    // =========================
    Route::middleware('role:Administrador')->group(function () {
        Route::resource('productos', ProductoController::class)->except(['show', 'edit', 'create']);
        Route::get('/productos/eliminados', [ProductoController::class, 'eliminados'])->name('productos.eliminados');
        Route::post('/productos/restaurar/{id}', [ProductoController::class, 'restaurar'])->name('productos.restaurar');
        Route::get('/productos/verificar-nombre', [App\Http\Controllers\ProductoController::class, 'verificarNombre'])->name('productos.verificarNombre');


        Route::get('/informacion', fn() => view('informacion.index'))->name('informacion.index');
    });

    // =========================
    // Mesas -> Admin y Cajero
    // =========================
    Route::middleware('role:Administrador,Cajero')->group(function () {
        Route::resource('mesas', MesaController::class)->except(['show', 'edit', 'create']);
        Route::get('/mesas/verificar/{numero}', [MesaController::class, 'verificarNumero']);
        Route::post('/mesas/guardar-posiciones', [MesaController::class, 'guardarPosiciones'])->name('mesas.guardarPosiciones');
        Route::post('/mesas/{id}/actualizar-posicion', [MesaController::class, 'actualizarPosicion']);
        
        Route::post('/mesas/mantenimiento/{id}', [MesaController::class, 'mantenimiento'])->name('mesas.mantenimiento');
    });

    // =========================
    // Pedidos -> Admin y Cajero
    // =========================
    Route::middleware('role:Administrador,Cajero')->group(function () {
        Route::get('/pedido', [PedidoController::class, 'index'])->name('pedido.index');
        Route::post('/pedido', [PedidoController::class, 'store'])->name('pedido.store');
    });

    // =========================
    // Historial Pedidos -> Admin, Cajero, Cocinero, Mesero
    // =========================
    Route::middleware('role:Administrador,Cajero,Cocinero,Mesero')->group(function () {
        Route::get('/pedidos/historial', [HistorialPedidoController::class, 'index'])->name('pedidos.historial');
        Route::get('/pedidos/{id}/detalle', [HistorialPedidoController::class, 'show'])->name('pedidos.detalle');
        Route::put('/pedidos/{id}/estado', [HistorialPedidoController::class, 'updateEstado'])->name('pedidos.estado');
    });


    // =========================
    // Inventario -> Admin, Cocinero
    // =========================
    Route::middleware('role:Administrador,Cocinero')->group(function () {
        Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
    Route::post('/inventario/{producto}/aumentar', [InventarioController::class, 'aumentar'])->name('inventario.aumentar');
    Route::post('/inventario/{producto}/disminuir', [InventarioController::class, 'disminuir'])->name('inventario.disminuir');
    });
});

Route::middleware(['auth'])->group(function () {
    // Perfil de usuario
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil.index');
    Route::post('/perfil/cambiar-password', [PerfilController::class, 'cambiarPassword'])->name('perfil.cambiarPassword');  
});
