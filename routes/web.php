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

    // DEPRECATED: Pengaturan Sistem - sekarang menggunakan config file
    // Route ini tetap ada untuk backward compatibility tapi tidak direkomendasikan
    Volt::route('/pengaturan-sistem', 'pengaturan-sistem')->name('pengaturan-sistem')->middleware('admin-only');

    // Private Files Route
    Route::get('/private/dokumentasi/{filename}', function ($filename) {
        $path = 'dokumentasi/' . $filename;
        if (!Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
            abort(404);
        }
        return Illuminate\Support\Facades\Storage::disk('local')->response($path);
    })->name('private.dokumentasi');

    // Logout
    Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
});
