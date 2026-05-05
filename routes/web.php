<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TutorController;

// Landing page principal
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.process');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::get('/tutor', [TutorController::class, 'index'])->name('tutor.dashboard');
Route::get('/tutor/revisar', [TutorController::class, 'revisar'])->name('tutor.revisar');
Route::get('/tutor/tutorados', [TutorController::class, 'tutorados'])->name('tutor.tutorados');
Route::get('/tutor/documentos', [TutorController::class, 'documentos'])->name('tutor.documentos');