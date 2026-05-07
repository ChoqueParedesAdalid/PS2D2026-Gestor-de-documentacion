<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TutorController;

// ==================================================
// RUTAS PÚBLICAS
// ==================================================

// Landing page principal
Route::get('/', [HomeController::class, 'index'])->name('home');

// Login / Logout
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.process');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ==================================================
// RUTAS PROTEGIDAS - TUTOR
// ==================================================

Route::middleware(['auth'])->prefix('tutor')->name('tutor.')->group(function () {
    
    // Dashboard principal del tutor
    Route::get('/', [TutorController::class, 'index'])->name('dashboard');
    
    // Lista de estudiantes tutorados
    Route::get('/tutorados', [TutorController::class, 'tutorados'])->name('tutorados');
    
    // Lista de documentos (pendientes/aprobados)
    Route::get('/documentos', [TutorController::class, 'documentos'])->name('documentos');
    
    // Vista de revisión de documento (con parámetro opcional)
    Route::get('/revisar/{id?}', [TutorController::class, 'revisar'])->name('revisar');
    
    // ==================================================
    // ACCIONES POST - FORMULARIOS
    // ==================================================
    
    // Guardar nueva observación
    Route::post('/observacion', [TutorController::class, 'storeObservacion'])->name('observacion.store');
    
    // Aprobar documento (cambiar estado a "visto_bueno")
    Route::post('/documento/{id}/aprobar', [TutorController::class, 'aprobarDocumento'])->name('documento.aprobar');
    
    // Solicitar correcciones al estudiante
    Route::post('/documento/{id}/solicitar-correcciones', [TutorController::class, 'solicitarCorrecciones'])->name('documento.corregir');
});

// ==================================================
// RUTAS PROTEGIDAS - ESTUDIANTE (Placeholder)
// ==================================================

Route::middleware(['auth'])->prefix('estudiante')->name('estudiante.')->group(function () {
    Route::get('/', function () {
        return view('estudiante.dashboard');
    })->name('dashboard');
});

// ==================================================
// RUTAS PROTEGIDAS - DOCENTE CARGO (Placeholder)
// ==================================================

Route::middleware(['auth'])->prefix('docente')->name('docente.')->group(function () {
    Route::get('/', function () {
        return view('docente.dashboard');
    })->name('dashboard');
});
// ==================================================
// RUTAS PROTEGIDAS - TRIBUNAL (Placeholder)
// ==================================================

Route::middleware(['auth'])->prefix('tribunal')->name('tribunal.')->group(function () {
    Route::get('/', function () {
        // Vista placeholder - Se implementará después
        return view('tribunal.dashboard');
    })->name('dashboard');
});