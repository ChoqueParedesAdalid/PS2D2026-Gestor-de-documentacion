<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\DirectorController;
use App\Http\Controllers\UserController;

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
    Route::get('/dashboard', [EstudianteController::class, 'index'])->name('dashboard');
    Route::get('/documentos', [EstudianteController::class, 'misDocumentos'])->name('documentos');
    Route::get('/tarea/{id}', [EstudianteController::class, 'verTarea'])->name('tarea.ver');
    Route::post('/tarea/{id}/entregar', [EstudianteController::class, 'subirEntrega'])->name('tarea.entregar');
    Route::put('/notificacion/{id}/leer', [EstudianteController::class, 'marcarNotificacionLeida'])->name('notificacion.leer');
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

/// ==================================================
// RUTAS PROTEGIDAS - DIRECTOR
// ==================================================

Route::middleware(['auth'])->prefix('director')->name('director.')->group(function () {
    
    // ✅ Dashboard
    Route::get('/dashboard', [DirectorController::class, 'index'])->name('dashboard');
    
    // ✅ Paralelos
    Route::get('/paralelos', [DirectorController::class, 'paralelos'])->name('paralelos');
    Route::get('/paralelos/crear', [DirectorController::class, 'crearParalelo'])->name('paralelos.crear');
    Route::post('/paralelos', [DirectorController::class, 'guardarParalelo'])->name('paralelos.guardar');
    // Rutas adicionales para Paralelos
Route::get('/paralelos/{id}/editar', [DirectorController::class, 'editarParalelo'])->name('paralelos.editar');
Route::put('/paralelos/{id}', [DirectorController::class, 'actualizarParalelo'])->name('paralelos.actualizar');
Route::get('/paralelos/{id}', [DirectorController::class, 'detalleParalelo'])->name('paralelos.detalle');
Route::get('/paralelos/{id}/estudiantes', [DirectorController::class, 'estudiantesPorParalelo'])->name('paralelos.estudiantes');
    // ✅ DOCENTES (AGREGA ESTAS)
    Route::get('/docentes', [DirectorController::class, 'docentes'])->name('docentes');
    Route::get('/docentes/crear', [DirectorController::class, 'crearDocente'])->name('docentes.crear');
    Route::post('/docentes', [DirectorController::class, 'guardarDocente'])->name('docentes.guardar');
    
    // ✅ ESTUDIANTES (AGREGA ESTAS)
    Route::get('/estudiantes', [DirectorController::class, 'estudiantes'])->name('estudiantes');
    Route::get('/estudiantes/crear', [DirectorController::class, 'crearEstudiante'])->name('estudiantes.crear');
    Route::post('/estudiantes', [DirectorController::class, 'guardarEstudiante'])->name('estudiantes.guardar');
    
});