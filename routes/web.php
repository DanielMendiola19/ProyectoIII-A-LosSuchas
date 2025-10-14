<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\MesaController;



// Página inicial -> splash
Route::get('/', function () {
    return view('splash');
})->name('splash');


// Formulario
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::get('/signup', [AuthController::class, 'showSignUpForm'])->name('signup.form');

// Acción POST
Route::post('/signup', [AuthController::class, 'signUp'])->name('signup');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Validación en tiempo real
Route::post('/signup/validate', [AuthController::class, 'validateField'])->name('signup.validate');


Route::get('/bienvenida', function () {
    $usuario = Auth::user();
    return view('bienvenida', compact('usuario'));
})->middleware('auth')->name('bienvenida');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Dashboard (se redirige aquí después del splash)
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');


Route::get('/informacion', function () {
    return view('informacion.index');
})->name('informacion.index');


// CRUD de productos protegido
Route::get('/productos', [ProductoController::class, 'index'])
    ->middleware('auth')
    ->name('productos.index');

Route::post('/productos', [ProductoController::class, 'store'])
    ->middleware('auth')
    ->name('productos.store');

Route::put('/productos/{id}', [ProductoController::class, 'update'])
    ->middleware('auth')
    ->name('productos.update');

Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])
    ->middleware('auth')
    ->name('productos.destroy');
Route::get('/productos/eliminados', [ProductoController::class, 'eliminados'])->name('productos.eliminados');
Route::post('/productos/restaurar/{id}', [ProductoController::class, 'restaurar'])->name('productos.restaurar');

Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');

Route::middleware('auth')->group(function () {
    Route::get('/pedido', [PedidoController::class, 'index'])->name('pedido.index');
    Route::post('/pedido', [PedidoController::class, 'store'])->name('pedido.store');
});



Route::middleware('auth')->group(function () {
    Route::get('/mesas', [MesaController::class, 'index'])->name('mesas.index');
    Route::post('/mesas', [MesaController::class, 'store'])->name('mesas.store');
    Route::put('/mesas/{mesa}', [MesaController::class, 'update'])->name('mesas.update');
    Route::delete('/mesas/{mesa}', [MesaController::class, 'destroy'])->name('mesas.destroy');
    Route::get('/mesas/verificar/{numero}', [MesaController::class, 'verificarNumero']);
    Route::post('/mesas/guardar-posiciones', [MesaController::class, 'guardarPosiciones'])->name('mesas.guardarPosiciones');
    Route::post('/mesas/{id}/actualizar-posicion', [MesaController::class, 'actualizarPosicion']);

});
