<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DocenteACargo\DocenteController;

// Landing page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.process');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Docente a cargo
Route::middleware('auth.session')->group(function () {
    Route::get('/docente/dashboard', [DocenteController::class, 'dashboard'])->name('docente.dashboard');
    Route::get('/docente/estudiantes', [DocenteController::class, 'estudiantes'])->name('docente.estudiantes');
    Route::get('/docente/proyectos', [DocenteController::class, 'proyectos'])->name('docente.proyectos');
    Route::get('/docente/tareas', [DocenteController::class, 'tareas'])->name('docente.tareas');
    });

// Dashboards temporales otros roles
Route::get('/director/dashboard', function () {
    if (!session('usuario') || session('usuario')['rol'] !== 'director')
        return redirect()->route('login');
    return inertia('Director/Dashboard');
})->name('director.dashboard');

Route::get('/tutor/dashboard', function () {
    if (!session('usuario') || session('usuario')['rol'] !== 'tutor')
        return redirect()->route('login');
    return inertia('Tutor/Dashboard');
})->name('tutor.dashboard');

Route::get('/jurado/dashboard', function () {
    if (!session('usuario') || session('usuario')['rol'] !== 'jurado')
        return redirect()->route('login');
    return inertia('Jurado/Dashboard');
})->name('jurado.dashboard');

Route::get('/estudiante/dashboard', function () {
    if (!session('usuario') || session('usuario')['rol'] !== 'estudiante')
        return redirect()->route('login');
    return inertia('Estudiante/Dashboard');
})->name('estudiante.dashboard');