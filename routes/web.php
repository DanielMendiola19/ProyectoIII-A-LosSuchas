<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\HistorialPedidoController;



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

Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');

Route::middleware('auth')->group(function () {
    Route::get('/pedido', [PedidoController::class, 'index'])->name('pedido.index');
    Route::post('/pedido', [PedidoController::class, 'store'])->name('pedido.store');
});



//Historial Pedidos
Route::get('/pedidos/historial', [HistorialPedidoController::class, 'index'])
    ->name('pedidos.historial');
Route::put('/pedidos/{id}/estado', [HistorialPedidoController::class, 'updateEstado'])
     ->name('pedidos.estado');
//ruta detalle pedido
Route::get('/pedidos/{id}/detalle', [HistorialPedidoController::class, 'show'])
    ->name('pedidos.detalle');

