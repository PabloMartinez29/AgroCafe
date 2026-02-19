<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\CoffeeTypeController;
use App\Http\Controllers\Admin\CooperativeController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\PriceAnalysisController;
use App\Http\Controllers\Admin\CashController;
use App\Http\Controllers\Peasant\DashboardController as PeasantDashboardController;
use App\Http\Controllers\Peasant\PurchaseController as PeasantPurchaseController;
use App\Http\Controllers\Peasant\PriceAnalysisController as PeasantPriceAnalysisController;
use App\Http\Controllers\Peasant\InvoiceController as PeasantInvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Redirigir usuarios autenticados a sus respectivos dashboards
Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('peasant.dashboard');
})->middleware(['auth', 'verified', 'active'])->name('dashboard');

// Rutas de administrador
Route::middleware(['auth', 'verified', 'active', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Usuarios
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
    
    // Compras
    Route::resource('purchases', PurchaseController::class);
    
    // Ventas
    Route::resource('sales', SaleController::class);
    
    // Tipos de café
    Route::resource('coffee-types', CoffeeTypeController::class);
    
    // Cooperativas
    Route::resource('cooperatives', CooperativeController::class);
    
    // Facturas
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
    Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::get('/invoices/{invoice}/generate', [InvoiceController::class, 'generate'])->name('invoices.generate');
    Route::post('/invoices/{invoice}/send-email', [InvoiceController::class, 'sendEmail'])->name('invoices.send-email');
    
    // Pagos
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    
    // Bodega
    Route::get('/warehouse', [WarehouseController::class, 'index'])->name('warehouse.index');
    Route::get('/warehouse/movements/create', [WarehouseController::class, 'createMovement'])->name('warehouse.movements.create');
    Route::post('/warehouse/movements', [WarehouseController::class, 'storeMovement'])->name('warehouse.movements.store');
    
    // Análisis de Precios
    Route::get('/price-analysis', [PriceAnalysisController::class, 'index'])->name('price-analysis.index');
    Route::get('/price-analysis/realtime', [PriceAnalysisController::class, 'getRealTimeData'])->name('price-analysis.realtime');
    
    // Gestión de Caja
    Route::get('/cash', [CashController::class, 'index'])->name('cash.index');
    Route::post('/cash/open', [CashController::class, 'open'])->name('cash.open');
    Route::post('/cash/close', [CashController::class, 'close'])->name('cash.close');
});

// Rutas de campesino
Route::middleware(['auth', 'verified', 'active', 'peasant'])->prefix('peasant')->name('peasant.')->group(function () {
    Route::get('/dashboard', [PeasantDashboardController::class, 'index'])->name('dashboard');
    
    // Compras
    Route::get('/purchases', [PeasantPurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/{purchase}', [PeasantPurchaseController::class, 'show'])->name('purchases.show');
    
    // Facturas
    Route::get('/invoices', [PeasantInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [PeasantInvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/details', [PeasantInvoiceController::class, 'details'])->name('invoices.details');
    Route::get('/invoices/{invoice}/download', [PeasantInvoiceController::class, 'download'])->name('invoices.download');

    // Análisis de Precios (campesino)
    Route::get('/price-analysis', [PeasantPriceAnalysisController::class, 'index'])->name('price-analysis.index');
});

// Rutas de perfil (para todos los usuarios autenticados)
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
