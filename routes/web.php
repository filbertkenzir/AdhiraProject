<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MethodController;
use App\Http\Controllers\AdminController;

// Auth Routes
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Cashier Routes (accessible by Cashier, Admin, or Guests)
Route::get('/', [CashierController::class, 'index'])->name('cashier.index');
Route::get('/cashier', [CashierController::class, 'index']);
Route::post('/transactions', [CashierController::class, 'storeTransaction'])->name('transactions.store');

// Admin Routes (Protected for Admin role)
Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    
    Route::get('/item', [ItemController::class, 'index'])->name('items.index');
    Route::post('/item', [ItemController::class, 'store'])->name('items.store');
    
    Route::get('/method', [MethodController::class, 'index'])->name('methods.index');
    Route::post('/method', [MethodController::class, 'store'])->name('methods.store');
});