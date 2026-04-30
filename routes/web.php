<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::redirect('/', '/login');
    Volt::route('/login', 'auth.login')->name('login');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::view('/dashboard', 'pages::dashboard')->name('dashboard');
    
    // Management User
    Volt::route('/management-user', 'management-user')->name('management-user');
    
    // Manajemen Dokumen Lembur
    Route::prefix('lembur')->name('lembur.')->group(function () {
        Volt::route('/', 'lembur.index')->name('index');
        Volt::route('/create', 'lembur.create')->name('create');
        Volt::route('/{lembur}/edit', 'lembur.edit')->name('edit');
    });

    // Profile
    Volt::route('/profile', 'profile')->name('profile');

    // Logout
    Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
});
