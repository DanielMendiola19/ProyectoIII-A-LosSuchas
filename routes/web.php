<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;

// Página inicial -> splash
Route::get('/', function () {
    return view('splash');
})->name('splash');

// Dashboard (se redirige aquí después del splash)
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// CRUD de productos
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
Route::put('/productos/{id}', [ProductoController::class, 'update'])->name('productos.update');
Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');
