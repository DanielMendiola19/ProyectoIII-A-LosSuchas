<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HistorialPedidoController;
use App\Http\Controllers\ReporteVentasController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/productos', [ProductoController::class, 'apiProductos']);
Route::post('/login', [AuthController::class, 'loginApi']);

Route::get('/historial', [HistorialPedidoController::class, 'apiHistorial']);
Route::post('/pedido/{id}/estado', [HistorialPedidoController::class, 'apiUpdateEstado']);

Route::get('/reportes/ultimos7', [ReporteVentasController::class, 'apiUltimos7']);
