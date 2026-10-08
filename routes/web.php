<?php

use App\Http\Controllers\Admin\AdminSessionController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\CustomQuoteController as AdminCustomQuoteController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FilamentController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\CustomQuoteController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductCatalogController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

// Public Storefront & Catalog Routes
Route::get('/', StorefrontController::class)->name('storefront');
Route::get('/products', [ProductCatalogController::class, 'index'])->name('catalog.index');
Route::get('/products/{product}', [ProductCatalogController::class, 'show'])->name('catalog.show');

// Public Action Routes
Route::post('/orders', [OrderController::class, 'store'])->middleware('throttle:20,1')->name('orders.store');
Route::post('/custom-quotes', [CustomQuoteController::class, 'store'])->middleware('throttle:15,1')->name('custom_quotes.store');

// InfinityFree / Shared Hosting Web Setup Helper
Route::get('/setup', SetupController::class)->name('setup');

// Admin Backoffice Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminSessionController::class, 'create'])->middleware('guest')->name('login');
    Route::post('/login', [AdminSessionController::class, 'store'])->middleware(['guest', 'throttle:5,1'])->name('login.store');

    Route::middleware(['auth', 'trio.admin'])->group(function () {
        // Executive Overview Dashboard
        Route::get('/', DashboardController::class)->name('dashboard');

        // Products Management (requires 'products' permission)
        Route::middleware('permission:products')->group(function () {
            Route::get('/products', [ProductController::class, 'index'])->name('products.index');
            Route::post('/products', [ProductController::class, 'store'])->name('products.store');
            Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
            Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
            Route::patch('/products/{product}/toggle', [ProductController::class, 'toggle'])->name('products.toggle');
            Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        });

        // Filaments & Materials Management (requires 'filaments' permission)
        Route::middleware('permission:filaments')->group(function () {
            Route::get('/filaments', [FilamentController::class, 'index'])->name('filaments.index');
            Route::post('/filaments', [FilamentController::class, 'store'])->name('filaments.store');
            Route::put('/filaments/{filament}', [FilamentController::class, 'update'])->name('filaments.update');
            Route::delete('/filaments/{filament}', [FilamentController::class, 'destroy'])->name('filaments.destroy');
        });

        // Orders & Shipping Management (requires 'orders' permission)
        Route::middleware('permission:orders')->group(function () {
            Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
            Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
        });

        // Custom 3D Model Quotes (requires 'quotes' permission)
        Route::middleware('permission:quotes')->group(function () {
            Route::get('/custom-quotes', [AdminCustomQuoteController::class, 'index'])->name('custom_quotes.index');
            Route::patch('/custom-quotes/{quote}/status', [AdminCustomQuoteController::class, 'update'])->name('custom_quotes.status');
            Route::delete('/custom-quotes/{quote}', [AdminCustomQuoteController::class, 'destroy'])->name('custom_quotes.destroy');
        });

        // Admin Team & Roles (Super Admin only)
        Route::middleware('permission:super')->group(function () {
            Route::get('/admins', [AdminUserController::class, 'index'])->name('admins.index');
            Route::post('/admins', [AdminUserController::class, 'store'])->name('admins.store');
            Route::put('/admins/{user}', [AdminUserController::class, 'update'])->name('admins.update');
            Route::delete('/admins/{user}', [AdminUserController::class, 'destroy'])->name('admins.destroy');
        });

        Route::post('/logout', [AdminSessionController::class, 'destroy'])->name('logout');
    });
});
