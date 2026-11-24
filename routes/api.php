<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\VentaController;
use Illuminate\Support\Facades\Route;

// Públicas
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

// Protegidas con token (Sanctum)
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Categorías (solo consultar): Administrador y Almacenero
    Route::middleware('role:Administrador,Almacenero')->group(function () {
        Route::get('/categorias', [CategoriaController::class, 'index']);
    });

    // Productos: consultar -> los 3 roles
    Route::middleware('role:Administrador,Almacenero,Vendedor')->group(function () {
        Route::get('/productos', [ProductoController::class, 'index']);
        Route::get('/productos/{producto}', [ProductoController::class, 'show'])->whereNumber('producto');
    });

    // Productos: crear y editar -> Administrador y Almacenero
    Route::middleware('role:Administrador,Almacenero')->group(function () {
        Route::post('/productos', [ProductoController::class, 'store']);
        Route::match(['put', 'patch'], '/productos/{producto}', [ProductoController::class, 'update'])->whereNumber('producto');
    });

    // Productos: desactivar -> solo Administrador
    Route::middleware('role:Administrador')->group(function () {
        Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->whereNumber('producto');
    });

    // Clientes (consultar y crear, sin eliminar): Administrador y Vendedor
    Route::middleware('role:Administrador,Vendedor')->group(function () {
        Route::get('/clientes', [ClienteController::class, 'index']);
        Route::post('/clientes', [ClienteController::class, 'store']);
    });

    // Ventas (consultar y cobrar): Administrador y Vendedor
    Route::middleware('role:Administrador,Vendedor')->group(function () {
        Route::get('/ventas', [VentaController::class, 'index']);
        Route::get('/ventas/{venta}', [VentaController::class, 'show'])->whereNumber('venta');
        Route::post('/ventas', [VentaController::class, 'store']);
    });
});
