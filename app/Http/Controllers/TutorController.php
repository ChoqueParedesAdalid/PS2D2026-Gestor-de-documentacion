<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Documento;
use App\Models\Observacion;
use App\Models\Inscripcion;
use App\Models\AsignacionTutor;
use App\Models\AsignacionTribunal; 
use App\Models\Tarea;
use App\Models\EstadoDocumento;
use App\Models\Notificacion;


class TutorController extends Controller
{
    /**
     * Display dashboard del tutor
     */
    public function index()
    {
        $tutor = Auth::user();

        if (!$tutor->esTutor()) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $inscripcionIds = AsignacionTutor::where('tutor_id', $tutor->id)
                                        ->where('activo', true)
                                        ->pluck('inscripcion_id');

        $totalTutorados = User::join('inscripciones', 'usuarios.id', '=', 'inscripciones.estudiante_id')
                              ->join('asignaciones_tutor', 'inscripciones.id', '=', 'asignaciones_tutor.inscripcion_id')
                              ->where('asignaciones_tutor.tutor_id', $tutor->id)
                              ->where('usuarios.activo', true)
                              ->where('asignaciones_tutor.activo', true)
                              ->where('usuarios.role_id', function($query) {
                                  $query->select('id')->from('roles')->where('nombre', 'estudiante');
                              })
                              ->distinct('usuarios.id')
                              ->count('usuarios.id');

        $pendientes = Documento::join('inscripciones', 'documentos.estudiante_id', '=', 'inscripciones.estudiante_id')
                               ->join('asignaciones_tutor', 'inscripciones.id', '=', 'asignaciones_tutor.inscripcion_id')
                               ->where('asignaciones_tutor.tutor_id', $tutor->id)
                               ->where('documentos.estado_id', 2)
                               ->where('asignaciones_tutor.activo', true)
                               ->count();

        $aprobados = Documento::join('inscripciones', 'documentos.estudiante_id', '=', 'inscripciones.estudiante_id')
                              ->join('asignaciones_tutor', 'inscripciones.id', '=', 'asignaciones_tutor.inscripcion_id')
                              ->where('asignaciones_tutor.tutor_id', $tutor->id)
                              ->whereIn('documentos.estado_id', [4, 5])
                              ->where('asignaciones_tutor.activo', true)
                              ->count();

        $enRevision = Documento::join('inscripciones', 'documentos.estudiante_id', '=', 'inscripciones.estudiante_id')
                               ->join('asignaciones_tutor', 'inscripciones.id', '=', 'asignaciones_tutor.inscripcion_id')
                               ->where('asignaciones_tutor.tutor_id', $tutor->id)
                               ->where('documentos.estado_id', 3)
                               ->where('asignaciones_tutor.activo', true)
                               ->count();

        $actividadReciente = Documento::with(['estudiante', 'tarea'])
                                      ->join('inscripciones', 'documentos.estudiante_id', '=', 'inscripciones.estudiante_id')
                                      ->join('asignaciones_tutor', 'inscripciones.id', '=', 'asignaciones_tutor.inscripcion_id')
                                      ->where('asignaciones_tutor.tutor_id', $tutor->id)
                                      ->where('asignaciones_tutor.activo', true)
                                      ->orderBy('documentos.created_at', 'desc')
                                      ->take(5)
                                      ->get();

        return view('tutor.dashboard', compact(
            'totalTutorados', 'pendientes', 'aprobados', 'enRevision', 'actividadReciente'
        ));
    }

    /**
     * Display list of students (tutorados)
     */
    public function tutorados(Request $request)
    {
        $tutor = Auth::user();

        if (!$tutor->esTutor()) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $query = User::select('usuarios.*', 'inscripciones.titulo_proyecto', 'inscripciones.estado_inscripcion')
                     ->join('inscripciones', 'usuarios.id', '=', 'inscripciones.estudiante_id')
                     ->join('asignaciones_tutor', 'inscripciones.id', '=', 'asignaciones_tutor.inscripcion_id')
                     ->join('roles', 'usuarios.role_id', '=', 'roles.id')
                     ->where('roles.nombre', 'estudiante')
                     ->where('asignaciones_tutor.tutor_id', $tutor->id)
                     ->where('usuarios.activo', true)
                     ->where('asignaciones_tutor.activo', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('usuarios.nombres', 'LIKE', "%{$search}%")
                  ->orWhere('usuarios.apellidos', 'LIKE', "%{$search}%")
                  ->orWhere('usuarios.email_institucional', 'LIKE', "%{$search}%");
            });
        }

        $tutorados = $query->paginate(10)->withQueryString();

        return view('tutor.tutorados', compact('tutorados'));
    }

    /**
     * Display list of documents - CON POLICY
     */
    public function documentos(Request $request)
    {
        $tutor = Auth::user();
        $filtro = $request->get('filtro', 'pendientes');

        if (!$tutor->esTutor()) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        // Query base con validación de pertenencia (Policy implícita)
        $query = Documento::with(['estudiante', 'tarea', 'estado', 'observaciones'])
                          ->join('inscripciones', 'documentos.estudiante_id', '=', 'inscripciones.estudiante_id')
                          ->join('asignaciones_tutor', 'inscripciones.id', '=', 'asignaciones_tutor.inscripcion_id')
                          ->where('asignaciones_tutor.tutor_id', $tutor->id)
                          ->where('asignaciones_tutor.activo', true);

        if ($filtro === 'pendientes') {
            $query->where('documentos.estado_id', 2);
        } elseif ($filtro === 'aprobados') {
            $query->whereIn('documentos.estado_id', [4, 5]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('documentos.archivo_nombre_original', 'LIKE', "%{$search}%")
                  ->orWhereHas('estudiante', function($q2) use ($search) {
                      $q2->where('nombres', 'LIKE', "%{$search}%")
                         ->orWhere('apellidos', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($request->filled('estudiante')) {
            $query->where('documentos.estudiante_id', $request->estudiante);
        }

        $documentos = $query->orderBy('documentos.entregado_en', 'desc')->paginate(10)->withQueryString();

        $tutorados = User::select('usuarios.id', 'usuarios.nombres', 'usuarios.apellidos')
                        ->join('inscripciones', 'usuarios.id', '=', 'inscripciones.estudiante_id')
                        ->join('asignaciones_tutor', 'inscripciones.id', '=', 'asignaciones_tutor.inscripcion_id')
                        ->where('asignaciones_tutor.tutor_id', $tutor->id)
                        ->where('asignaciones_tutor.activo', true)
                        ->get();

        return view('tutor.documentos', compact('documentos', 'filtro', 'tutorados'));
    }

    /**
 * Display document review page
 */
public function revisar($id)
{
    $tutor = Auth::user();

    // Validación básica de rol
    if (!$tutor->esTutor()) {
        abort(403, 'No tienes permiso para acceder a esta sección.');
    }

    // Obtener el documento con sus relaciones (SIN historial)
    $documento = Documento::with([
                    'estudiante', 
                    'tarea', 
                    'estado',
                    'observaciones' => function($q) { 
                        $q->orderBy('created_at', 'desc'); 
                    }
                ])
                ->join('inscripciones', 'documentos.estudiante_id', '=', 'inscripciones.estudiante_id')
                ->join('asignaciones_tutor', 'inscripciones.id', '=', 'asignaciones_tutor.inscripcion_id')
                ->where('documentos.id', $id)
                ->where('asignaciones_tutor.tutor_id', $tutor->id)
                ->where('asignaciones_tutor.activo', true)
                ->select('documentos.*')
                ->firstOrFail();

    //  Obtener TODAS las observaciones de TODOS los documentos de este estudiante en esta tarea
    $todasLasObservaciones = Observacion::join('documentos', 'observaciones.documento_id', '=', 'documentos.id')
                                        ->where('documentos.tarea_id', $documento->tarea_id)
                                        ->where('documentos.estudiante_id', $documento->estudiante_id)
                                        ->where('observaciones.rol_revisor', 'tutor')
                                        ->with('revisor')
                                        ->orderBy('observaciones.created_at', 'desc')
                                        ->select('observaciones.*')
                                        ->get();

    // USAR POLICY: Verificar si puede revisar este documento
    // if (!Gate::allows('view', $documento)) {
    //     abort(403, 'No tienes permiso para ver este documento.');
    // }

    return view('tutor.revisar', compact('documento', 'todasLasObservaciones'));
}

 /**
 * Store observation
 */
public function storeObservacion(Request $request)
{
    $tutor = Auth::user();

    if (!$tutor->esTutor()) {
        return response()->json(['success' => false, 'message' => 'No tienes permiso.'], 403);
    }

    $validated = $request->validate([
        'id_documento' => 'required|exists:documentos,id',
        'contenido' => 'required|string|max:1000|min:10',
        'seccion' => 'nullable|string|max:100',
    ], [
        'contenido.required' => 'La observación no puede estar vacía.',
        'contenido.min' => 'La observación debe tener al menos 10 caracteres.',
    ]);

    $documento = Documento::find($request->id_documento);

    //  Política de autorización
    // $this->authorize('observar', $documento);

    $observacion = Observacion::create([
        'documento_id' => $request->id_documento,
        'revisor_id' => $tutor->id,
        'rol_revisor' => 'tutor',
        'comentario' => $validated['contenido'],
        'seccion_documento' => $validated['seccion'] ?? 'General',
        'resuelta' => false,
        // ✅ Asegurar que created_at se guarde
        'created_at' => now(),
    ]);

    if ($documento->estado_id !== 3) {
        $documento->estado_id = 3; // con_observaciones
        $documento->save();

        // === NOTIFICACIÓN AL ESTUDIANTE ===
        Notificacion::crear(
            usuarioId: $documento->estudiante_id,
            titulo: '💬 Nueva observación de tu tutor',
            mensaje: 'Tu tutor ha dejado una nueva observación en tu documento. Por favor revisa y corrige.',
            tipo: 'nueva_observacion',
            entidadRelacionada: "documento:{$documento->id}"
        );
    }

    return response()->json([
        'success' => true,
        'message' => 'Observación agregada correctamente.',
        'observacion' => [
            'id' => $observacion->id,
            'comentario' => $observacion->comentario,
            'seccion_documento' => $observacion->seccion_documento,
            // ✅ CORRECCIÓN: Usar operador null-safe para diffForHumans
            'created_at' => $observacion->created_at?->diffForHumans() ?? 'Justo ahora',
        ]
    ]);
}
   /**
 * Marcar observación como corregida (AJAX)
 */
public function marcarObservacionCorregida($id)
{
    $tutor = Auth::user();

    if (!$tutor->esTutor()) {
        return response()->json(['success' => false, 'message' => 'No tienes permiso.'], 403);
    }

    $observacion = Observacion::findOrFail($id);

    // Política de autorización
    // $this->authorize('marcarCorregida', $observacion);

    $observacion->resuelta = true;
    $observacion->resuelta_en = now();
    $observacion->save();

    return response()->json(['success' => true, 'message' => 'Observación marcada como corregida.']);
}
/**
 * Eliminar observación (AJAX)
 */
public function eliminarObservacion($id)
{
    $tutor = Auth::user();

    if (!$tutor->esTutor()) {
        return response()->json(['success' => false, 'message' => 'No tienes permiso.'], 403);
    }

    $observacion = Observacion::findOrFail($id);

    // Política de autorización
    // $this->authorize('delete', $observacion);

    $observacion->delete();

    return response()->json(['success' => true, 'message' => 'Observación eliminada.']);
}
/**
 * Approve document
 */
public function aprobarDocumento($id)
{
    $tutor = Auth::user();

    if (!$tutor->esTutor()) {
        abort(403, 'No tienes permiso para realizar esta acción.');
    }

    $documento = Documento::findOrFail($id);

    // ✅ Verificar que el documento esté en estado válido para aprobar
    if (!in_array($documento->estado_id, [2, 3])) {
        // Si ya está aprobado, redirigir sin error
        if ($documento->estado_id == 4) {
            return redirect()->route('tutor.tareas-ver', $documento->tarea_id)
                            ->with('success', 'Documento ya aprobado.');
        }
        return redirect()->route('tutor.tareas-ver', $documento->tarea_id)
                        ->with('error', 'Este documento no puede ser aprobado en su estado actual.');
    }

    // Verificar observaciones pendientes
    $obsPendientes = Observacion::where('documento_id', $documento->id)
                                ->where('resuelta', false)
                                ->count();

    if ($obsPendientes > 0) {
        return redirect()->route('tutor.tareas-ver', $documento->tarea_id)
                        ->with('warning', "Hay {$obsPendientes} observaciones pendientes. ¿Estás seguro de aprobar?");
    }

    // Aprobar documento
    $documento->estado_id = 4; // visto_bueno
    $documento->save();

    // === NOTIFICACIÓN AL ESTUDIANTE ===
    Notificacion::crear(
        usuarioId: $documento->estudiante_id,
        titulo: '✅ Documento aprobado por tutor',
        mensaje: 'Tu documento ha sido aprobado por el tutor. Pasará a revisión de tribunal.',
        tipo: 'documento_aprobado',
        entidadRelacionada: "documento:{$documento->id}"
    );

    // === NOTIFICACIÓN A LOS TRIBUNALES ===
    $inscripcion = Inscripcion::where('estudiante_id', $documento->estudiante_id)->first();
    if ($inscripcion) {
        $tribunales = AsignacionTribunal::where('inscripcion_id', $inscripcion->id)
                                       ->where('activo', true)
                                       ->with('tribunal')
                                       ->get();
        
        foreach ($tribunales as $asignacion) {
            if ($asignacion->tribunal) {
                Notificacion::crear(
                    usuarioId: $asignacion->tribunal_id,
                    titulo: '📋 Documento listo para revisión',
                    mensaje: "{$documento->estudiante->nombres} {$documento->estudiante->apellidos} tiene un documento aprobado por tutor para tu revisión.",
                    tipo: 'nueva_entrega',
                    entidadRelacionada: "documento:{$documento->id}"
                );
            }
        }
    }

    //  Redirigir directamente a la vista de tareas (evita el mensaje rojo)
    return redirect()->route('tutor.tareas-ver', $documento->tarea_id)
                    ->with('success', 'Documento aprobado correctamente. Pasará a revisión de tribunal.');
}
 /**
 * Request corrections
 */
public function solicitarCorrecciones(Request $request, $id)
{
    $tutor = Auth::user();

    if (!$tutor->esTutor()) {
        abort(403, 'No tienes permiso para realizar esta acción.');
    }

    $documento = Documento::findOrFail($id);

    // Política de autorización
    // $this->authorize('observar', $documento);

    $validated = $request->validate([
        'observaciones' => 'nullable|string|max:2000',
    ]);

    if ($validated['observaciones']) {
        Observacion::create([
            'documento_id' => $documento->id,
            'revisor_id' => $tutor->id,
            'rol_revisor' => 'tutor',
            'comentario' => $validated['observaciones'],
            'seccion_documento' => 'General',
            'resuelta' => false,
            'created_at' => now(),
        ]);
    }

    $documento->estado_id = 3; // con_observaciones
    $documento->save();

    // === NOTIFICACIÓN AL ESTUDIANTE ===
    Notificacion::crear(
        usuarioId: $documento->estudiante_id,
        titulo: '💬 Documento requiere correcciones',
        mensaje: 'El tutor ha solicitado correcciones en tu documento. Por favor revisa las observaciones.',
        tipo: 'nueva_observacion',
        entidadRelacionada: "documento:{$documento->id}"
    );

    // ✅ CORRECCIÓN: Redirigir a tareas en lugar de documentos
    return redirect()->route('tutor.tareas-ver', $documento->tarea_id)
                     ->with('success', 'Correcciones solicitadas al estudiante.');
}
    /**
     * Display list of tasks for tutor's students
     */
    public function tareas(Request $request)
    {
        $tutor = Auth::user();

        if (!$tutor->esTutor()) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        // Obtener IDs de inscripciones de los tutorados
        $inscripcionIds = AsignacionTutor::where('tutor_id', $tutor->id)
                                        ->where('activo', true)
                                        ->pluck('inscripcion_id');

        // Obtener materias de los tutorados
        $materiaIds = Inscripcion::whereIn('id', $inscripcionIds)
                                ->pluck('materia_id');

        // Obtener tareas de esas materias
        $tareas = Tarea::whereIn('materia_id', $materiaIds)
                      ->with(['materia', 'creadaPor'])
                      ->orderBy('fecha_limite', 'asc')
                      ->get();

        return view('tutor.tareas', compact('tareas'));
    }

   /**
 * Display task details for tutor
 */
public function verTarea($tareaId)
{
    $tutor = Auth::user();
    if (!$tutor->esTutor()) abort(403);

    $tarea = Tarea::with([
        'materia',
        'creadaPor',
        'documentos' => function($q) {
            $q->with(['estudiante', 'estado', 'observaciones' => function($q2) {
                $q2->with('revisor')->orderBy('created_at', 'desc');
            }]);
        }
    ])->findOrFail($tareaId);

    // Verificar que la tarea sea de una materia de sus tutorados
    $inscripcionIds = AsignacionTutor::where('tutor_id', $tutor->id)
                                    ->where('activo', true)
                                    ->pluck('inscripcion_id');
    
    $materiaIds = Inscripcion::whereIn('id', $inscripcionIds)
                            ->pluck('materia_id')
                            ->toArray();
    
    if (!in_array($tarea->materia_id, $materiaIds)) {
        abort(403, 'No tienes permiso para ver esta tarea.');
    }

    // Obtener estudiantes que ya entregaron (estado_id = 2 'entregado')
    $entregados = $tarea->documentos->filter(function($doc) {
        return $doc->estado_id == 2; // entregado
    })->unique('estudiante_id');
    
    // Obtener estudiantes revisados/devueltos (estado_id = 3 'con_observaciones' o 4 'visto_bueno')
    $revisados = $tarea->documentos->filter(function($doc) {
        return in_array($doc->estado_id, [3, 4]); // con_observaciones o visto_bueno
    })->unique('estudiante_id');
    
    // Obtener todos los estudiantes de la materia (tutorados)
    $estudiantesMateria = Inscripcion::where('materia_id', $tarea->materia_id)
                                     ->whereIn('id', $inscripcionIds)
                                     ->where('estado_inscripcion', 'activo')
                                     ->with(['estudiante', 'tutores'])
                                     ->get();
    
    // Filtrar estudiantes que aún no han entregado
    $idsEntregaronORevisaron = $entregados->merge($revisados)->pluck('estudiante_id')->toArray();
    $pendientes = $estudiantesMateria->filter(function($ins) use ($idsEntregaronORevisaron) {
        return !in_array($ins->estudiante_id, $idsEntregaronORevisaron);
    })->pluck('estudiante');

    return view('tutor.tareas-ver', compact('tarea', 'entregados', 'revisados', 'pendientes', 'estudiantesMateria'));
}
    /**
     * Display student document history
     */
    public function historialDocumentos($estudianteId)
    {
        $tutor = Auth::user();
        if (!$tutor->esTutor()) abort(403);

        // Verificar que el estudiante sea tutorado del tutor
        $esTutorado = AsignacionTutor::join('inscripciones', 'asignaciones_tutor.inscripcion_id', '=', 'inscripciones.id')
                                    ->where('asignaciones_tutor.tutor_id', $tutor->id)
                                    ->where('asignaciones_tutor.activo', true)
                                    ->where('inscripciones.estudiante_id', $estudianteId)
                                    ->exists();
        
        if (!$esTutorado) {
            abort(403, 'Este estudiante no es tu tutorado.');
        }

        $estudiante = User::findOrFail($estudianteId);
        
        $documentos = Documento::where('estudiante_id', $estudianteId)
                              ->with(['tarea', 'estado', 'observaciones'])
                              ->orderBy('entregado_en', 'desc')
                              ->get();

        return view('tutor.historial-documentos', compact('estudiante', 'documentos'));
    }

    /**
     * Generate PDF report for student
     */
    public function generarReportePDF($estudianteId)
    {
        $tutor = Auth::user();
        if (!$tutor->esTutor()) abort(403);

        // Verificar que el estudiante sea tutorado
        $esTutorado = AsignacionTutor::join('inscripciones', 'asignaciones_tutor.inscripcion_id', '=', 'inscripciones.id')
                                    ->where('asignaciones_tutor.tutor_id', $tutor->id)
                                    ->where('asignaciones_tutor.activo', true)
                                    ->where('inscripciones.estudiante_id', $estudianteId)
                                    ->exists();
        
        if (!$esTutorado) {
            abort(403, 'Este estudiante no es tu tutorado.');
        }

        $estudiante = User::findOrFail($estudianteId);
        
        $documentos = Documento::where('estudiante_id', $estudianteId)
                              ->with(['tarea', 'estado'])
                              ->orderBy('entregado_en', 'desc')
                              ->get();

        // Generar PDF (usaremos una vista simple por ahora)
        $pdf = \PDF::loadView('tutor.reporte-pdf', compact('estudiante', 'documentos'));
        
        return $pdf->download('reporte_' . $estudiante->nombres . '_' . $estudiante->apellidos . '.pdf');
    }
}