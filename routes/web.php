<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'auth.login')->name('login');
Route::view('/dashboard', 'pages::dashboard')->name('dashboard');
Volt::route('/management-user', 'management-user')->name('management-user');
Volt::route('/lembur', 'lembur.index')->name('lembur.index');
Volt::route('/lembur/create', 'lembur.create')->name('lembur.create');
Volt::route('/lembur/{lembur}/edit', 'lembur.edit')->name('lembur.edit');
