<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;

// Landing page principal
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.process');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ============================================
// DOCENTE A CARGO (TODAS LAS VISTAS DE ESTE ROL)
// ============================================
Route::middleware('auth')->group(function () {
    Route::get('/docente/dashboard', [DocenteController::class, 'dashboard'])->name('docente.dashboard');
    Route::get('/docente/estudiantes', [DocenteController::class, 'estudiantes'])->name('docente.estudiantes');
    Route::get('/docente/proyectos', [DocenteController::class, 'proyectos'])->name('docente.proyectos');
    Route::get('/docente/tareas', [DocenteController::class, 'tareas'])->name('docente.tareas');
});

// Rutas de TUTOR
Route::middleware(['auth'])->prefix('tutor')->name('tutor.')->group(function () {
    Route::get('/', [TutorController::class, 'index'])->name('dashboard');
    Route::get('/tutorados', [TutorController::class, 'tutorados'])->name('tutorados');
    Route::get('/documentos', [TutorController::class, 'documentos'])->name('documentos');
    Route::get('/revisar/{id?}', [TutorController::class, 'revisar'])->name('revisar');
    Route::post('/observacion', [TutorController::class, 'storeObservacion'])->name('observacion.store');
    Route::post('/documento/{id}/aprobar', [TutorController::class, 'aprobarDocumento'])->name('documento.aprobar');
    Route::post('/documento/{id}/solicitar-correcciones', [TutorController::class, 'solicitarCorrecciones'])->name('documento.corregir');
});

// Rutas de TRIBUNAL
Route::middleware(['auth'])->prefix('tribunal')->name('tribunal.')->group(function () {
    Route::get('/', function () {
        return view('tribunal.dashboard');
    })->name('dashboard');
});

// ============================================
// DIRECTOR (Issue #19) - Módulo completo
// ============================================
Route::middleware(['auth'])->prefix('director')->name('director.')->group(function () {
    Route::get('/', [App\Http\Controllers\DirectorController::class, 'index'])->name('dashboard');
    Route::get('/paralelos', [App\Http\Controllers\DirectorController::class, 'paralelos'])->name('paralelos');
    Route::get('/paralelos/crear', [App\Http\Controllers\DirectorController::class, 'crearParalelo'])->name('paralelos.crear');
    Route::post('/paralelos', [App\Http\Controllers\DirectorController::class, 'guardarParalelo'])->name('paralelos.guardar');
    Route::get('/paralelos/{id}/editar', [App\Http\Controllers\DirectorController::class, 'editarParalelo'])->name('paralelos.editar');
    Route::put('/paralelos/{id}', [App\Http\Controllers\DirectorController::class, 'actualizarParalelo'])->name('paralelos.actualizar');
    Route::get('/paralelos/{id}', [App\Http\Controllers\DirectorController::class, 'detalleParalelo'])->name('paralelos.detalle');
    Route::get('/paralelos/{id}/estudiantes', [App\Http\Controllers\DirectorController::class, 'estudiantesPorParalelo'])->name('paralelos.estudiantes');
    Route::get('/docentes', [App\Http\Controllers\DirectorController::class, 'docentes'])->name('docentes');
    Route::get('/docentes/crear', [App\Http\Controllers\DirectorController::class, 'crearDocente'])->name('docentes.crear');
    Route::post('/docentes', [App\Http\Controllers\DirectorController::class, 'guardarDocente'])->name('docentes.guardar');
    Route::get('/estudiantes', [App\Http\Controllers\DirectorController::class, 'estudiantes'])->name('estudiantes');
    Route::get('/estudiantes/crear', [App\Http\Controllers\DirectorController::class, 'crearEstudiante'])->name('estudiantes.crear');
    Route::post('/estudiantes', [App\Http\Controllers\DirectorController::class, 'guardarEstudiante'])->name('estudiantes.guardar');
});