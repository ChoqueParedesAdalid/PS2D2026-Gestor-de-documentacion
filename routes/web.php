<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
// Landing page principal
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rutas de autenticación (las activaremos después)
// Route::get('/login', function () { return view('auth.login'); })->name('login');
