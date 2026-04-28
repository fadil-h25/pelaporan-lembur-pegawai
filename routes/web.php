<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LemburController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [LemburController::class, 'index'])->name('dashboard');
    Route::post('/lembur/store', [LemburController::class, 'store'])->name('lembur.store');
    Route::get('/lembur/edit/{id}', [LemburController::class, 'edit'])->name('lembur.edit');
    Route::put('/lembur/update/{id}', [LemburController::class, 'update'])->name('lembur.update');
    Route::delete('/lembur/{id}', [LemburController::class, 'destroy'])->name('lembur.destroy');
    Route::get('/cetak/{type}/{id}/{nomor}', [LemburController::class, 'cetak'])->name('lembur.cetak');
    Route::post('/pegawai/store', [LemburController::class, 'storePegawai'])->name('pegawai.store');
    Route::get('/pegawai/edit/{id}', [LemburController::class, 'editPegawai'])->name('pegawai.edit');
    Route::put('/pegawai/update/{id}', [LemburController::class, 'updatePegawai'])->name('pegawai.update');
    Route::delete('/pegawai/{id}', [LemburController::class, 'destroyPegawai'])->name('pegawai.destroy');
});
