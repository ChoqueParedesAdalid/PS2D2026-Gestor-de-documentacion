<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Documento;
use App\Models\Observacion;
use App\Models\Tarea;
use App\Models\Inscripcion;
use App\Models\AsignacionTutor;
use App\Models\AsignacionTribunal;
use App\Models\EstadoDocumento;
use App\Models\Notificacion;

class EstudianteController extends Controller
{
    /**
     * Display dashboard del estudiante
     */
    public function index()
    {
        $estudiante = Auth::user();

        // Validar rol con Policy
        if (!$estudiante->esEstudiante()) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        // Obtener inscripción activa del estudiante
        $inscripcion = Inscripcion::where('estudiante_id', $estudiante->id)
                                  ->where('estado_inscripcion', 'activo')
                                  ->with(['materia', 'docenteCargo'])
                                  ->first();

        // Obtener tutor asignado
        $tutor = null;
        if ($inscripcion) {
            $asignacionTutor = AsignacionTutor::where('inscripcion_id', $inscripcion->id)
                                              ->where('activo', true)
                                              ->with('tutor')
                                              ->first();
            if ($asignacionTutor) {
                $tutor = $asignacionTutor->tutor;
            }
        }

        // Obtener tribunales asignados
        $tribunales = [];
        if ($inscripcion) {
            $tribunales = AsignacionTribunal::where('inscripcion_id', $inscripcion->id)
                                           ->where('activo', true)
                                           ->with('tribunal')
                                           ->get()
                                           ->pluck('tribunal');
        }

        // Obtener tareas del estudiante (Policy: solo ve tareas de su materia)
        $tareas = Tarea::whereHas('materia', function($query) use ($inscripcion) {
                        $query->where('id', $inscripcion->materia_id ?? 0);
                    })
                    ->with(['documentos' => function($q) use ($estudiante) {
                        $q->where('estudiante_id', $estudiante->id)
                          ->orderBy('version', 'desc')
                          ->take(1);
                    }])
                    ->orderBy('fecha_limite', 'asc')
                    ->get();

        // Contadores para las cards
        $tareasPendientes = $tareas->filter(function($tarea) {
            return $tarea->documentos->isEmpty() && $tarea->fecha_limite > now();
        })->count();

        $tareasCompletadas = $tareas->filter(function($tarea) {
            return $tarea->documentos->isNotEmpty();
        })->count();

        $tareasVencidas = $tareas->filter(function($tarea) {
            return $tarea->documentos->isEmpty() && $tarea->fecha_limite < now();
        })->count();

        return view('estudiante.dashboard', compact(
            'tutor',
            'tribunales',
            'tareas',
            'tareasPendientes',
            'tareasCompletadas',
            'tareasVencidas',
            'inscripcion'
        ));
    }

   
/**
 * Display tareas del estudiante
 */
public function tareas(Request $request)
{
    $estudiante = Auth::user();
    if (!$estudiante->esEstudiante()) abort(403);

    $filtro = $request->get('filtro', 'proximamente');

    // Obtener inscripciones del estudiante
    $inscripcionIds = Inscripcion::where('estudiante_id', $estudiante->id)
                                ->where('estado_inscripcion', 'activo')
                                ->pluck('id');

    // Query base de tareas
    $query = Tarea::whereIn('materia_id', function($q) use ($inscripcionIds) {
                    $q->select('materia_id')->from('inscripciones')
                     ->whereIn('id', $inscripcionIds);
                })
                ->with(['materia', 'documentos' => function($q) use ($estudiante) {
                    $q->where('estudiante_id', $estudiante->id)
                      ->with(['observaciones.revisor', 'estado'])
                      ->orderBy('version', 'desc');
                }]);

    // Aplicar filtros (PRIORIZANDO COMPLETADO)
    if ($filtro === 'completado') {
        // Completado: Tiene documento entregado (sin importar fecha)
        $query->whereHas('documentos', function($q) use ($estudiante) {
                    $q->where('estudiante_id', $estudiante->id);
                })
              ->orderBy('fecha_limite', 'desc');
              
    } elseif ($filtro === 'vencida') {
        // Vencida: NO tiene documento Y fecha <= now
        $query->whereDoesntHave('documentos', function($q) use ($estudiante) {
                    $q->where('estudiante_id', $estudiante->id);
                })
              ->where('fecha_limite', '<=', now())
              ->orderBy('fecha_limite', 'desc');
              
    } else { 
        
        $query->whereDoesntHave('documentos', function($q) use ($estudiante) {
                    $q->where('estudiante_id', $estudiante->id);
                })
              ->where('fecha_limite', '>', now())
              ->orderBy('fecha_limite', 'asc');
    }

    $tareas = $query->get();

    return view('estudiante.tareas', compact('tareas', 'filtro'));
}

/**
 * Ver detalle de tarea
 */
public function verTarea($tareaId)
{
    $estudiante = Auth::user();
    if (!$estudiante->esEstudiante()) abort(403);

    $tarea = Tarea::with([
        'materia',
        'documentos' => function($q) use ($estudiante) {
            $q->where('estudiante_id', $estudiante->id)
              ->with(['observaciones.revisor', 'estado'])
              ->orderBy('version', 'desc');
        }
    ])->findOrFail($tareaId);

    // Verificar que el estudiante esté inscrito en la materia
    $inscripcion = Inscripcion::where('estudiante_id', $estudiante->id)
                             ->where('materia_id', $tarea->materia_id)
                             ->where('estado_inscripcion', 'activo')
                             ->first();
    
    if (!$inscripcion) {
        abort(403, 'No estás inscrito en esta materia.');
    }

    $ultimoDocumento = $tarea->documentos->first();

    return view('estudiante.tareas-ver', compact('tarea', 'ultimoDocumento'));
}
    /**
     * Subir documento para una tarea - CON POLICY
     */
    public function subirDocumento(Request $request, $tareaId)
    {
        $estudiante = Auth::user();

        // Validar rol
        if (!$estudiante->esEstudiante()) {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para subir documentos.'], 403);
        }

        $tarea = Tarea::findOrFail($tareaId);

        // USAR POLICY: Verificar si puede crear documento para esta tarea
        if (!Gate::allows('create', Documento::class)) {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para subir documentos.'], 403);
        }

        // Verificar que puede ver esta tarea (Policy de Tarea)
        if (!Gate::allows('view', $tarea)) {
            return response()->json(['success' => false, 'message' => 'No tienes acceso a esta tarea.'], 403);
        }

        $validated = $request->validate([
            'archivo' => 'required|file|mimes:pdf|max:51200',
        ], [
            'archivo.required' => 'Debes seleccionar un archivo PDF.',
            'archivo.mimes' => 'El archivo debe ser un PDF.',
            'archivo.max' => 'El archivo no debe superar los 50MB.',
        ]);

        $inscripcion = Inscripcion::where('estudiante_id', $estudiante->id)
                                  ->where('materia_id', $tarea->materia_id)
                                  ->where('estado_inscripcion', 'activo')
                                  ->firstOrFail();

        $ultimaVersion = Documento::where('tarea_id', $tarea->id)
                                  ->where('estudiante_id', $estudiante->id)
                                  ->max('version') ?? 0;

        $nuevaVersion = $ultimaVersion + 1;

        if ($nuevaVersion > 10) {
            return redirect()->back()->with('error', 'Has alcanzado el límite de versiones permitidas (10).');
        }

        $archivo = $request->file('archivo');
        $nombreOriginal = $archivo->getClientOriginalName();
        $nombreSeguro = time() . '_' . $estudiante->id . '_' . $tareaId . '_v' . $nuevaVersion . '.pdf';
        
        $rutaArchivo = $archivo->storeAs(
            'documentos/' . date('Y-m'),
            $nombreSeguro,
            'public'
        );

        $estadoEntregado = EstadoDocumento::where('nombre', 'entregado')->first();

        $documento = Documento::create([
            'tarea_id' => $tarea->id,
            'estudiante_id' => $estudiante->id,
            'version' => $nuevaVersion,
            'archivo_ruta' => $rutaArchivo,
            'archivo_nombre_original' => $nombreOriginal,
            'archivo_tamaño' => $archivo->getSize(),
            'archivo_hash' => hash_file('sha256', $archivo->getRealPath()),
            'estado_id' => $estadoEntregado->id,
            'entregado_en' => now(),
        ]);

        // === NOTIFICACIÓN AL TUTOR ===
        $asignacionTutor = AsignacionTutor::where('inscripcion_id', $inscripcion->id)
                                          ->where('activo', true)
                                          ->with('tutor')
                                          ->first();
        
        if ($asignacionTutor && $asignacionTutor->tutor) {
            Notificacion::crear(
                usuarioId: $asignacionTutor->tutor_id,
                titulo: '📄 Nuevo documento entregado',
                mensaje: "{$estudiante->nombres} {$estudiante->apellidos} ha subido un nuevo documento (v{$nuevaVersion}) para: {$tarea->titulo}",
                tipo: 'nueva_entrega',
                entidadRelacionada: "documento:{$documento->id}"
            );
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Documento subido correctamente (versión ' . $nuevaVersion . ')',
                'documento' => [
                    'id' => $documento->id,
                    'version' => $documento->version,
                    'archivo_nombre_original' => $documento->archivo_nombre_original,
                    'entregado_en' => $documento->entregado_en->format('d/m/Y H:i'),
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Documento subido correctamente (v' . $nuevaVersion . ')');
    }

    /**
     * Ver observaciones de un documento - CON POLICY
     */
    public function verObservaciones($documentoId)
    {
        $estudiante = Auth::user();

        if (!$estudiante->esEstudiante()) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $documento = Documento::where('id', $documentoId)
                              ->where('estudiante_id', $estudiante->id)
                              ->with(['observaciones.revisor', 'tarea', 'estado'])
                              ->firstOrFail();

        // USAR POLICY: Verificar si puede ver este documento
        $this->authorize('view', $documento);

        return view('estudiante.observaciones', compact('documento'));
    }

    /**
     * Descargar documento propio - CON POLICY
     */
    public function descargarDocumento($documentoId)
    {
        $estudiante = Auth::user();

        if (!$estudiante->esEstudiante()) {
            abort(403, 'No tienes permiso para descargar este documento.');
        }

        $documento = Documento::where('id', $documentoId)
                              ->where('estudiante_id', $estudiante->id)
                              ->firstOrFail();

        // USAR POLICY
        $this->authorize('view', $documento);

        if (!Storage::disk('public')->exists($documento->archivo_ruta)) {
            return redirect()->back()->with('error', 'El archivo no existe en el servidor.');
        }

        return Storage::disk('public')->download($documento->archivo_ruta, $documento->archivo_nombre_original);
    }
}