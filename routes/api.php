<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\CoffeeTypeController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\CooperativeController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\CashController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Usuarios
    Route::get('/users', [UserController::class, 'index']);
    
    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    
    // Tipos de café
    Route::get('/coffee-types', [CoffeeTypeController::class, 'index']);
    Route::get('/coffee-types/{coffeeType}', [CoffeeTypeController::class, 'show']);
    
    // CRUD de tipos de café (solo administradores)
    Route::middleware('admin')->group(function () {
        Route::post('/coffee-types', [CoffeeTypeController::class, 'store']);
        Route::put('/coffee-types/{coffeeType}', [CoffeeTypeController::class, 'update']);
        Route::delete('/coffee-types/{coffeeType}', [CoffeeTypeController::class, 'destroy']);
    });
    
    // Cooperativas
    Route::get('/cooperatives', [CooperativeController::class, 'index']);
    Route::get('/cooperatives/{cooperative}', [CooperativeController::class, 'show']);
    
    // CRUD de cooperativas (solo administradores)
    Route::middleware('admin')->group(function () {
        Route::post('/cooperatives', [CooperativeController::class, 'store']);
        Route::put('/cooperatives/{cooperative}', [CooperativeController::class, 'update']);
        Route::delete('/cooperatives/{cooperative}', [CooperativeController::class, 'destroy']);
    });
    
    // Compras
    Route::get('/purchases', [PurchaseController::class, 'index']);
    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show']);
    
    // CRUD de compras (solo administradores)
    Route::middleware('admin')->group(function () {
        Route::post('/purchases', [PurchaseController::class, 'store']);
        Route::put('/purchases/{purchase}', [PurchaseController::class, 'update']);
        Route::delete('/purchases/{purchase}', [PurchaseController::class, 'destroy']);
    });
    
    // Ventas (solo administradores)
    Route::middleware('admin')->group(function () {
        Route::get('/sales', [SaleController::class, 'index']);
        Route::get('/sales/{sale}', [SaleController::class, 'show']);
        Route::post('/sales', [SaleController::class, 'store']);
        Route::put('/sales/{sale}', [SaleController::class, 'update']);
        Route::delete('/sales/{sale}', [SaleController::class, 'destroy']);
    });
    
    // Facturas
    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show']);
    
    // Pagos (solo administradores)
    Route::middleware('admin')->group(function () {
        Route::get('/payments', [PaymentController::class, 'index']);
        Route::get('/payments/{id}', [PaymentController::class, 'show']);
        Route::post('/payments', [PaymentController::class, 'store']);
        Route::put('/payments/{id}', [PaymentController::class, 'update']);
        Route::delete('/payments/{id}', [PaymentController::class, 'destroy']);
    });
    
    // Gestión de Caja (solo administradores)
    Route::middleware('admin')->group(function () {
        Route::get('/cash', [CashController::class, 'index']);
        Route::post('/cash/open', [CashController::class, 'open']);
        Route::post('/cash/close', [CashController::class, 'close']);
    });
});

