<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Documento;
use App\Models\Observacion;
use App\Models\Inscripcion;
use App\Models\AsignacionTutor;

class TutorController extends Controller
{
    /**
     * Display dashboard del tutor
     */
    public function index()
    {
        $tutor = Auth::user();

        // Obtener IDs de inscripciones donde este tutor está asignado
        $inscripcionIds = AsignacionTutor::where('tutor_id', $tutor->id)
                                        ->where('activo', true)
                                        ->pluck('inscripcion_id');

        // Estadísticas para las cards
        $totalTutorados = User::join('inscripciones', 'usuarios.id', '=', 'inscripciones.estudiante_id')
                              ->join('asignaciones_tutor', 'inscripciones.id', '=', 'asignaciones_tutor.inscripcion_id')
                              ->where('asignaciones_tutor.tutor_id', $tutor->id)
                              ->where('usuarios.activo', true)
                              ->where('asignaciones_tutor.activo', true)
                              ->distinct('usuarios.id')
                              ->count('usuarios.id');

        $pendientes = Documento::join('tareas', 'documentos.tarea_id', '=', 'tareas.id')
                               ->join('inscripciones', 'documentos.estudiante_id', '=', 'inscripciones.estudiante_id')
                               ->join('asignaciones_tutor', 'inscripciones.id', '=', 'asignaciones_tutor.inscripcion_id')
                               ->where('asignaciones_tutor.tutor_id', $tutor->id)
                               ->where('documentos.estado_id', 2) // 'entregado'
                               ->where('asignaciones_tutor.activo', true)
                               ->count();

        $aprobados = Documento::join('tareas', 'documentos.tarea_id', '=', 'tareas.id')
                              ->join('inscripciones', 'documentos.estudiante_id', '=', 'inscripciones.estudiante_id')
                              ->join('asignaciones_tutor', 'inscripciones.id', '=', 'asignaciones_tutor.inscripcion_id')
                              ->where('asignaciones_tutor.tutor_id', $tutor->id)
                              ->whereIn('documentos.estado_id', [4, 5]) // 'visto_bueno', 'aprobado_tribunal'
                              ->where('asignaciones_tutor.activo', true)
                              ->count();

        $enRevision = Documento::join('tareas', 'documentos.tarea_id', '=', 'tareas.id')
                               ->join('inscripciones', 'documentos.estudiante_id', '=', 'inscripciones.estudiante_id')
                               ->join('asignaciones_tutor', 'inscripciones.id', '=', 'asignaciones_tutor.inscripcion_id')
                               ->where('asignaciones_tutor.tutor_id', $tutor->id)
                               ->where('documentos.estado_id', 3) // 'con_observaciones'
                               ->where('asignaciones_tutor.activo', true)
                               ->count();

        // Actividad reciente (últimos 5 documentos entregados por sus tutorados)
        $actividadReciente = Documento::with(['estudiante', 'tarea'])
                                      ->join('inscripciones', 'documentos.estudiante_id', '=', 'inscripciones.estudiante_id')
                                      ->join('asignaciones_tutor', 'inscripciones.id', '=', 'asignaciones_tutor.inscripcion_id')
                                      ->where('asignaciones_tutor.tutor_id', $tutor->id)
                                      ->where('asignaciones_tutor.activo', true)
                                      ->orderBy('documentos.created_at', 'desc')
                                      ->take(5)
                                      ->get();

        return view('tutor.dashboard', compact(
            'totalTutorados',
            'pendientes',
            'aprobados',
            'enRevision',
            'actividadReciente'
        ));
    }

    /**
     * Display list of students (tutorados)
     */
    public function tutorados(Request $request)
    {
        $tutor = Auth::user();

        // Query base para obtener estudiantes de este tutor
        $query = User::select('usuarios.*', 'inscripciones.titulo_proyecto', 'inscripciones.estado_inscripcion')
                     ->join('inscripciones', 'usuarios.id', '=', 'inscripciones.estudiante_id')
                     ->join('asignaciones_tutor', 'inscripciones.id', '=', 'asignaciones_tutor.inscripcion_id')
                     ->join('roles', 'usuarios.role_id', '=', 'roles.id')
                     ->where('roles.nombre', 'estudiante')
                     ->where('asignaciones_tutor.tutor_id', $tutor->id)
                     ->where('usuarios.activo', true)
                     ->where('asignaciones_tutor.activo', true);

        // Búsqueda por nombre o código
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('usuarios.nombres', 'LIKE', "%{$search}%")
                  ->orWhere('usuarios.apellidos', 'LIKE', "%{$search}%")
                  ->orWhere('usuarios.email_institucional', 'LIKE', "%{$search}%");
            });
        }

        // Paginación
        $tutorados = $query->paginate(10)->withQueryString();

        return view('tutor.tutorados', compact('tutorados'));
    }

    /**
     * Display list of documents
     */
    public function documentos(Request $request)
    {
        $tutor = Auth::user();
        $filtro = $request->get('filtro', 'pendientes'); // 'pendientes' o 'aprobados'

        // Query base
        $query = Documento::with(['estudiante', 'tarea', 'estado', 'observaciones'])
                          ->join('tareas', 'documentos.tarea_id', '=', 'tareas.id')
                          ->join('inscripciones', 'documentos.estudiante_id', '=', 'inscripciones.estudiante_id')
                          ->join('asignaciones_tutor', 'inscripciones.id', '=', 'asignaciones_tutor.inscripcion_id')
                          ->where('asignaciones_tutor.tutor_id', $tutor->id)
                          ->where('asignaciones_tutor.activo', true);

        // Filtrar por estado
        if ($filtro === 'pendientes') {
            $query->where('documentos.estado_id', 2); // 'entregado'
        } elseif ($filtro === 'aprobados') {
            $query->whereIn('documentos.estado_id', [4, 5]); // 'visto_bueno', 'aprobado_tribunal'
        }

        // Búsqueda
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

        $documentos = $query->orderBy('documentos.entregado_en', 'desc')->paginate(10)->withQueryString();

        return view('tutor.documentos', compact('documentos', 'filtro'));
    }

    /**
     * Display document review page
     */
    public function revisar($id)
    {
        $tutor = Auth::user();

        // Obtener documento con relaciones, validando que pertenezca a un tutorado
        $documento = Documento::with([
                        'estudiante',
                        'tarea',
                        'estado',
                        'observaciones' => function($q) {
                            $q->orderBy('created_at', 'desc');
                        },
                        'historial'
                    ])
                    ->join('inscripciones', 'documentos.estudiante_id', '=', 'inscripciones.estudiante_id')
                    ->join('asignaciones_tutor', 'inscripciones.id', '=', 'asignaciones_tutor.inscripcion_id')
                    ->where('documentos.id', $id)
                    ->where('asignaciones_tutor.tutor_id', $tutor->id)
                    ->where('asignaciones_tutor.activo', true)
                    ->select('documentos.*')
                    ->firstOrFail();

        return view('tutor.revisar', compact('documento'));
    }

    /**
     * Store observation
     */
    public function storeObservacion(Request $request)
    {
        $request->validate([
            'id_documento' => 'required|exists:documentos,id',
            'contenido' => 'required|string|max:1000',
            'seccion' => 'nullable|string|max:100',
        ]);

        $tutor = Auth::user();

        // Verificar que el documento pertenece a un tutorado de este tutor
        $pertenece = Documento::join('inscripciones', 'documentos.estudiante_id', '=', 'inscripciones.estudiante_id')
                              ->join('asignaciones_tutor', 'inscripciones.id', '=', 'asignaciones_tutor.inscripcion_id')
                              ->where('documentos.id', $request->id_documento)
                              ->where('asignaciones_tutor.tutor_id', $tutor->id)
                              ->where('asignaciones_tutor.activo', true)
                              ->exists();

        if (!$pertenece) {
            return back()->with('error', 'No tienes permiso para observar este documento.');
        }

        Observacion::create([
            'documento_id' => $request->id_documento,
            'revisor_id' => $tutor->id,
            'rol_revisor' => 'tutor',
            'comentario' => $request->contenido,
            'seccion_documento' => $request->seccion,
            'resuelta' => false,
        ]);

        // Actualizar estado del documento a "con_observaciones" si no lo estaba
        $documento = Documento::find($request->id_documento);
        if ($documento->estado_id !== 3) { // si no es 'con_observaciones'
            $documento->estado_id = 3;
            $documento->save();
        }

        // TODO: Crear notificación para el estudiante

        return back()->with('success', 'Observación agregada correctamente.');
    }

    /**
     * Approve document
     */
    public function aprobarDocumento($id)
    {
        $tutor = Auth::user();

        $documento = Documento::join('inscripciones', 'documentos.estudiante_id', '=', 'inscripciones.estudiante_id')
                              ->join('asignaciones_tutor', 'inscripciones.id', '=', 'asignaciones_tutor.inscripcion_id')
                              ->where('documentos.id', $id)
                              ->where('asignaciones_tutor.tutor_id', $tutor->id)
                              ->where('asignaciones_tutor.activo', true)
                              ->select('documentos.*')
                              ->firstOrFail();

        // Actualizar estado a "visto_bueno" (id=4)
        $documento->estado_id = 4;
        $documento->save();

        // TODO: Crear notificación para el estudiante

        return redirect()->route('tutor.documentos')->with('success', 'Documento aprobado correctamente. Pasa a revisión de tribunal.');
    }

    /**
     * Request corrections
     */
    public function solicitarCorrecciones(Request $request, $id)
    {
        $request->validate([
            'observaciones' => 'nullable|string|max:2000',
        ]);

        $tutor = Auth::user();

        $documento = Documento::join('inscripciones', 'documentos.estudiante_id', '=', 'inscripciones.estudiante_id')
                              ->join('asignaciones_tutor', 'inscripciones.id', '=', 'asignaciones_tutor.inscripcion_id')
                              ->where('documentos.id', $id)
                              ->where('asignaciones_tutor.tutor_id', $tutor->id)
                              ->where('asignaciones_tutor.activo', true)
                              ->select('documentos.*')
                              ->firstOrFail();

        // Si hay observaciones generales, guardarlas
        if ($request->filled('observaciones')) {
            Observacion::create([
                'documento_id' => $documento->id,
                'revisor_id' => $tutor->id,
                'rol_revisor' => 'tutor',
                'comentario' => $request->observaciones,
                'seccion_documento' => 'General',
                'resuelta' => false,
            ]);
        }

        // Mantener estado en "con_observaciones" (id=3)
        $documento->estado_id = 3;
        $documento->save();

        // TODO: Crear notificación para el estudiante

        return redirect()->route('tutor.documentos')->with('success', 'Correcciones solicitadas al estudiante.');
    }
}