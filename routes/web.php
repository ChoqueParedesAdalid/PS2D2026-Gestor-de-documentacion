<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\TribunalController;
use App\Http\Controllers\DirectorController;
use App\Http\Controllers\DocenteCargoController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\ProfileController;

// ==================================================
// RUTAS PÚBLICAS
// ==================================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.process');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rutas de Selección de Rol (Para usuarios con doble rol)
Route::middleware(['auth'])->group(function () {
    Route::get('/seleccionar-rol', [LoginController::class, 'showRoleSelect'])->name('auth.role.select');
    Route::post('/seleccionar-rol', [LoginController::class, 'selectRole'])->name('auth.role.process');
});

// Ruta para verificar autenticación vía AJAX
Route::middleware(['auth'])->get('/api/auth/check', function() {
    return response()->json(['authenticated' => true, 'user' => auth()->user()->nombres]);
})->name('auth.check');

// Rutas de Perfil de Usuario (para todos los roles)
Route::middleware(['auth'])->group(function () {
    Route::get('/mi-perfil', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/mi-perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/mi-perfil/contrasena', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// ==================================================
// RUTAS DE NOTIFICACIONES (Globales para todos los roles)
// ==================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/api/notificaciones/obtener', [NotificacionController::class, 'obtenerNotificaciones'])->name('notificaciones.api.obtener');
    Route::post('/notificaciones/{id}/leida', [NotificacionController::class, 'marcarComoLeida'])->name('notificaciones.marcarLeida');
    Route::post('/notificaciones/marcar-todas-leidas', [NotificacionController::class, 'marcarTodasLeidas'])->name('notificaciones.marcarTodasLeidas');
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
});

// ==================================================
// RUTAS PROTEGIDAS - TUTOR
// ==================================================
Route::middleware(['auth'])->prefix('tutor')->name('tutor.')->group(function () {
    Route::get('/', [TutorController::class, 'index'])->name('dashboard');
    Route::get('/tutorados', [TutorController::class, 'tutorados'])->name('tutorados');
    Route::get('/documentos', [TutorController::class, 'documentos'])->name('documentos');
    Route::get('/revisar/{id}', [TutorController::class, 'revisar'])->name('revisar');
    
    Route::post('/observacion', [TutorController::class, 'storeObservacion'])->name('observacion.store');
    Route::post('/observacion/{id}/corregida', [TutorController::class, 'marcarObservacionCorregida'])->name('observacion.corregida');
    Route::delete('/observacion/{id}', [TutorController::class, 'eliminarObservacion'])->name('observacion.eliminar');
    Route::post('/documento/{id}/aprobar', [TutorController::class, 'aprobarDocumento'])->name('documento.aprobar');
    Route::post('/documento/{id}/solicitar-correcciones', [TutorController::class, 'solicitarCorrecciones'])->name('documento.corregir');

    // Tareas
    Route::get('/tareas', [TutorController::class, 'tareas'])->name('tareas');
    Route::get('/tareas/ver/{tareaId}', [TutorController::class, 'verTarea'])->name('tareas-ver');

    // Historial de documentos y reporte PDF
    Route::get('/historial-documentos/{estudianteId}', [TutorController::class, 'historialDocumentos'])->name('historial-documentos');
    Route::get('/reporte-pdf/{estudianteId}', [TutorController::class, 'generarReportePDF'])->name('reporte-pdf');
});

// ==================================================
// RUTAS PROTEGIDAS - ESTUDIANTE
// ==================================================
Route::middleware(['auth'])->prefix('estudiante')->name('estudiante.')->group(function () {
    Route::get('/', [EstudianteController::class, 'index'])->name('dashboard');
    
    // Tareas (SIN DUPLICADOS)
    Route::get('/tareas', [EstudianteController::class, 'tareas'])->name('tareas');
    Route::get('/tareas/ver/{tareaId}', [EstudianteController::class, 'verTarea'])->name('tareas.ver');
    Route::post('/tareas/{tareaId}/subir', [EstudianteController::class, 'subirDocumento'])->name('tareas.subir');
    
    // Documentos
    Route::get('/documentos/{documentoId}/observaciones', [EstudianteController::class, 'verObservaciones'])->name('documentos.observaciones');
    Route::get('/documentos/{documentoId}/descargar', [EstudianteController::class, 'descargarDocumento'])->name('documentos.descargar');
});

// ==================================================
// RUTAS PROTEGIDAS - TRIBUNAL
// ==================================================
Route::middleware(['auth'])->prefix('tribunal')->name('tribunal.')->group(function () {
    Route::get('/', [TribunalController::class, 'index'])->name('dashboard');
    Route::get('/estudiantes', [TribunalController::class, 'estudiantes'])->name('estudiantes');
    Route::get('/tareas', [TribunalController::class, 'tareas'])->name('tareas');
    Route::get('/tareas/ver/{tareaId}', [TribunalController::class, 'verTarea'])->name('tareas-ver');
    Route::get('/documentos', [TribunalController::class, 'documentos'])->name('documentos');
    Route::get('/revisar/{id}', [TribunalController::class, 'revisar'])->name('revisar');
    Route::post('/observacion', [TribunalController::class, 'storeObservacion'])->name('observacion.store');
    Route::post('/observacion/{id}/corregida', [TribunalController::class, 'marcarObservacionCorregida'])->name('observacion.corregida');
    Route::delete('/observacion/{id}', [TribunalController::class, 'eliminarObservacion'])->name('observacion.eliminar');
    Route::post('/documento/{id}/aprobar', [TribunalController::class, 'aprobarDocumento'])->name('documento.aprobar');
    Route::post('/documento/{id}/solicitar-correcciones', [TribunalController::class, 'solicitarCorrecciones'])->name('documento.corregir');
});

// ==================================================
// RUTAS PROTEGIDAS - DIRECTOR
// ==================================================
Route::middleware(['auth'])->prefix('director')->name('director.')->group(function () {
    
    // Dashboard
    Route::get('/', [DirectorController::class, 'index'])->name('dashboard');
    
    // === MATERIAS ===
    Route::get('/materias', [DirectorController::class, 'materias'])->name('materias');
    Route::post('/materias/crear', [DirectorController::class, 'crearMateria'])->name('materias.crear');
    Route::get('/materias/{id}', [DirectorController::class, 'verMateria'])->name('materias.ver');
    Route::get('/materias/{id}/editar', [DirectorController::class, 'editarMateria'])->name('materias.editar');
    Route::put('/materias/{id}', [DirectorController::class, 'actualizarMateria'])->name('materias.actualizar');
    
    // === DOCENTES ===
    Route::get('/docentes', [DirectorController::class, 'docentes'])->name('docentes');
    Route::post('/docentes/crear', [DirectorController::class, 'crearDocente'])->name('docentes.crear');
    Route::put('/docentes/{id}/actualizar-rol', [DirectorController::class, 'actualizarRolDocente'])->name('actualizarRolDocente');
    Route::get('/docentes/{id}', [DirectorController::class, 'verDocente'])->name('docentes.ver');
    Route::get('/docentes/{id}/editar', [DirectorController::class, 'editarDocente'])->name('docentes.editar');
    Route::put('/docentes/{id}', [DirectorController::class, 'actualizarDocente'])->name('docentes.actualizar');
    Route::delete('/docentes/{id}', [DirectorController::class, 'eliminarDocente'])->name('docentes.eliminar');
    
    // === ESTUDIANTES ===
    Route::get('/estudiantes', [DirectorController::class, 'estudiantes'])->name('estudiantes');
    Route::get('/estudiantes/buscar', [DirectorController::class, 'buscarEstudiantes'])->name('estudiantes.buscar');
    Route::get('/estudiantes/{id}', [DirectorController::class, 'verEstudiante'])->name('estudiantes.ver');
    Route::delete('/estudiantes/{id}', [DirectorController::class, 'eliminarEstudiante'])->name('estudiantes.eliminar');
    
    // === DOCUMENTOS ===
    Route::get('/documentos', [DirectorController::class, 'documentos'])->name('documentos');
    
    // === ASIGNACIONES ===
    Route::post('/asignar-tutor', [DirectorController::class, 'asignarTutor'])->name('asignarTutor');
    Route::post('/asignar-tribunal', [DirectorController::class, 'asignarTribunal'])->name('asignarTribunal');
    
    // === REPORTES ===
    Route::get('/reportes', [DirectorController::class, 'reportes'])->name('reportes');
    Route::get('/reportes/exportar/{tipo}', [DirectorController::class, 'exportarReporte'])->name('reportes.exportar');
});

// ==================================================
// RUTAS PROTEGIDAS - DOCENTE A CARGO
// ==================================================
Route::middleware(['auth'])->prefix('docente-cargo')->name('docente.')->group(function () {
    
    Route::get('/', [DocenteCargoController::class, 'index'])->name('dashboard');
    
    // Gestionar estudiantes
    Route::get('/materia/{materiaId}/estudiantes', [DocenteCargoController::class, 'estudiantes'])->name('estudiantes');
    Route::post('/estudiantes/registrar', [DocenteCargoController::class, 'registrarEstudiante'])->name('estudiantes.registrar');
    
    Route::post('/estudiantes/{inscripcionId}/asignar-tutor', [DocenteCargoController::class, 'asignarTutor'])->name('asignarTutor');
    Route::post('/estudiantes/{inscripcionId}/asignar-tribunal', [DocenteCargoController::class, 'asignarTribunal'])->name('asignarTribunal');
    Route::post('/estudiantes/{inscripcionId}/actualizar-proyecto', [DocenteCargoController::class, 'actualizarProyecto'])->name('actualizarProyecto');
    
    // Gestionar tareas
    Route::get('/materia/{materiaId}/tareas', [DocenteCargoController::class, 'tareas'])->name('tareas');
    Route::post('/tareas/crear', [DocenteCargoController::class, 'crearTarea'])->name('tareas.crear');
    Route::get('/tareas/ver/{tareaId}', [DocenteCargoController::class, 'verTarea'])->name('tareas-ver');
    Route::put('/tareas/{tareaId}', [DocenteCargoController::class, 'actualizarTarea'])->name('tareas.actualizar');
});