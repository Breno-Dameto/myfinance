<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestEmail;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'sendOtp'])->name('login.send');
Route::get('/verify', [AuthController::class, 'showVerify'])->name('auth.verify');
Route::post('/verify', [AuthController::class, 'verifyOtp'])->name('login.verify');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// App Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/export/csv', [TransactionController::class, 'exportCsv'])->name('transactions.export.csv');
    Route::get('/transactions/export/xlsx', [TransactionController::class, 'exportXlsx'])->name('transactions.export.xlsx');
});

// Test Email Route
Route::get('/send-test-email', function () {
    Mail::to('brenodameto1@gmail.com')->send(new TestEmail());
    return 'Email sent';
});
