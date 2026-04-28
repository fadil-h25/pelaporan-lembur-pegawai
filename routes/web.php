<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'auth.login')->name('login');
Route::view('/dashboard', 'pages::dashboard')->name('dashboard');