<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use App\Models\Materia;
use App\Models\Inscripcion;
use App\Models\Tarea;
use App\Models\AsignacionTutor;
use App\Models\AsignacionTribunal;
use App\Models\Notificacion;

class DocenteCargoController extends Controller
{
    public function index()
    {
        
        $docente = Auth::user();
        if (!$docente->esDocenteCargo()) abort(403);

        // El docente solo ve las materias donde él es el docente_cargo_id
        $materias = Materia::where('docente_cargo_id', $docente->id)
                    ->with(['gestion', 'inscripciones' => function($q) {
                        $q->with(['estudiante', 'tutores', 'tribunales'])->where('estado_inscripcion', 'activo');
                    }])
                    ->get();

        $totalEstudiantes = Inscripcion::where('docente_cargo_id', $docente->id)->where('estado_inscripcion', 'activo')->count();
        $totalTutores = AsignacionTutor::whereHas('inscripcion', fn($q) => $q->where('docente_cargo_id', $docente->id))->distinct('tutor_id')->count('tutor_id');
        $totalTribunales = AsignacionTribunal::whereHas('inscripcion', fn($q) => $q->where('docente_cargo_id', $docente->id))->distinct('tribunal_id')->count('tribunal_id');
        $totalTareas = Tarea::whereIn('materia_id', $materias->pluck('id'))->count();
        $actividadReciente = \App\Models\Documento::with(['estudiante', 'tarea'])->whereHas('tarea', function($q) use ($materias) {
            $q->whereIn('materia_id', $materias->pluck('id'));
        })->orderBy('created_at', 'desc')->take(5)->get();

        return view('docente.dashboard', compact('materias', 'totalEstudiantes', 'totalTutores', 'totalTribunales', 'totalTareas', 'actividadReciente'));
    }

    public function estudiantes($materiaId)
    {
    $docente = Auth::user();
    if (!$docente->esDocenteCargo()) abort(403);

    $materia = Materia::findOrFail($materiaId);
    
    // Verificar que la materia sea de este docente
    if ($materia->docente_cargo_id !== $docente->id) {
        abort(403, 'No tienes permiso para gestionar esta materia.');
    }
    
    // Cargar relaciones correctamente
    $inscripciones = Inscripcion::where('materia_id', $materiaId)
                               ->where('docente_cargo_id', $docente->id)
                               ->where('estado_inscripcion', 'activo')
                               ->with([
                                   'estudiante',
                                   'tutores' => function($q) { $q->wherePivot('activo', true); },
                                   'tribunales' => function($q) { $q->wherePivot('activo', true); },
                                   'documentos' => function($q) { $q->orderBy('version', 'desc')->take(1); }
                               ])
                               ->get();

    // Obtener lista de docentes disponibles para asignar
    $docentesDisponibles = User::whereIn('role_id', function($q) {
                                $q->select('id')->from('roles')
                                 ->whereIn('nombre', ['docente', 'docente_cargo', 'tutor', 'tribunal']);
                            })
                            ->where('activo', true)
                            ->orderBy('nombres')
                            ->get();

    return view('docente.estudiantes', compact('materia', 'inscripciones', 'docentesDisponibles'));
    }

    public function registrarEstudiante(Request $request)
    {
     
            $docente = Auth::user();
        if (!$docente->esDocenteCargo()) abort(403);

            $validated = $request->validate([
            'email_institucional' => 'required|email|ends_with:@est.univalle.edu',
            'materia_id' => 'required|exists:materias,id',
            'titulo_proyecto' => 'nullable|string|max:255',
        ]);

        $estudiante = User::where('email_institucional', $validated['email_institucional'])
                     ->whereHas('rol', fn($q) => $q->where('nombre', 'estudiante'))
                     ->first();
    
        if (!$estudiante) return redirect()->back()->with('error', 'Estudiante no encontrado.');
    
        if (Inscripcion::where('estudiante_id', $estudiante->id)
                   ->where('materia_id', $validated['materia_id'])
                   ->exists()) {
        return redirect()->back()->with('error', 'Estudiante ya inscrito.');
        }

        Inscripcion::create([
            'estudiante_id' => $estudiante->id,
            'materia_id' => $validated['materia_id'],
            'docente_cargo_id' => $docente->id,
            'titulo_proyecto' => $validated['titulo_proyecto'] ?? 'Sin título',
            'estado_inscripcion' => 'activo',
        ]);

    // Notificación corregida (sin causar error)
    try {
        Notificacion::crear(
            usuarioId: $estudiante->id,
            titulo: ' Nueva inscripción',
            mensaje: 'Has sido inscrito en una materia.',
            tipo: 'nueva_inscripcion',
            entidadRelacionada: "materia:{$validated['materia_id']}"
        );
    } catch (\Exception $e) {
        // Si falla la notificación, no interrumpimos el flujo
        \Log::warning('Error al crear notificación: ' . $e->getMessage());
    }

    return redirect()->back()->with('success', 'Estudiante registrado correctamente.');
    }

    public function crearTarea(Request $request)
    {
        
        $docente = Auth::user();
        if (!$docente->esDocenteCargo()) abort(403);

        $validated = $request->validate([
            'materia_id' => 'required|exists:materias,id', 'titulo' => 'required|string|max:150',
            'descripcion' => 'required|string', 'fecha_limite' => 'required|date|after:today',
            'tipo_documento' => 'required|in:anteproyecto,documento_final,anexos,otro',
        ]);

        Tarea::create([
            'materia_id' => $validated['materia_id'], 'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'], 'fecha_limite' => $validated['fecha_limite'],
            'tipo_documento' => $validated['tipo_documento'], 'creada_por' => $docente->id,
        ]);

        $estudiantes = Inscripcion::where('materia_id', $validated['materia_id'])->where('docente_cargo_id', $docente->id)->where('estado_inscripcion', 'activo')->pluck('estudiante_id');
        foreach ($estudiantes as $id) {
            Notificacion::crear($id, '📋 Nueva tarea', "Tarea creada: {$validated['titulo']}", 'nueva_tarea', "tarea:{$validated['materia_id']}");
        }
        return redirect()->back()->with('success', 'Tarea creada.');
    }
/**
 * Asignar tutor a estudiante
 */
public function asignarTutor(Request $request, $inscripcionId)
{
    try {
        $validated = $request->validate([
            'inscripcion_id' => 'required|exists:inscripciones,id',
            'tutor_id' => 'required|exists:usuarios,id',
        ]);

        $inscripcion = Inscripcion::findOrFail($inscripcionId);
        
        //  Obtener el docente a cargo actual (quien está haciendo la asignación)
        $docenteCargoId = auth()->id();
        
        // Verificar si ya tiene tutor
        $existingTutor = $inscripcion->tutores()->first();
        
        if ($existingTutor) {
            // Actualizar si ya existe (sync con datos adicionales en la tabla pivote)
            $inscripcion->tutores()->sync([
                $validated['tutor_id'] => [
                    'asignado_por' => $docenteCargoId,
                    'asignado_en' => now(),
                ]
            ]);
            $message = 'Tutor actualizado correctamente';
        } else {
            // Asignar nuevo tutor con datos adicionales en la tabla pivote
            $inscripcion->tutores()->attach($validated['tutor_id'], [
                'asignado_por' => $docenteCargoId,
                'asignado_en' => now(),
            ]);
            $message = 'Tutor asignado correctamente';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al asignar tutor: ' . $e->getMessage(),
        ], 500);
    }
}
 /**
 * Asignar tribunal (jurado) a estudiante
 */
public function asignarTribunal(Request $request, $inscripcionId)
{
  
    try {
        $validated = $request->validate([
            'inscripcion_id' => 'required|exists:inscripciones,id',
            'tribunal_id' => 'required|exists:usuarios,id',
        ]);

        $inscripcion = Inscripcion::findOrFail($inscripcionId);
        $tribunal = User::findOrFail($validated['tribunal_id']);
        
        //  VALIDACIÓN: Permitir cualquier rol de docente
        $rolesPermitidos = ['docente', 'docente_cargo', 'tutor', 'tribunal'];
        if (!in_array($tribunal->rol->nombre, $rolesPermitidos)) {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no tiene un rol válido para ser jurado',
            ], 422);
        }
        
        // Contar tribunales actuales para ESTE estudiante
        $currentTribunales = $inscripcion->tribunales()->count();
        
        if ($currentTribunales >= 2) {
            return response()->json([
                'success' => false,
                'message' => 'Este estudiante ya tiene 2 jurados asignados',
            ], 422);
        }
        
        // No permitir que el mismo docente sea asignado dos veces al MISMO estudiante
        if ($inscripcion->tribunales()->where('tribunal_id', $validated['tribunal_id'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Este jurado ya está asignado a este estudiante',
            ], 422);
        }
        
        // ✅ Obtener el docente a cargo actual (quien está haciendo la asignación)
        $docenteCargoId = auth()->id();
        
        // Asignar tribunal con datos adicionales en la tabla pivote
        $inscripcion->tribunales()->attach($validated['tribunal_id'], [
            'asignado_por' => $docenteCargoId,
            'asignado_en' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jurado asignado correctamente',
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al asignar jurado: ' . $e->getMessage(),
        ], 500);
    }
}

    public function tareas($materiaId)
    {
        
        $docente = Auth::user();
        if (!$docente->esDocenteCargo()) abort(403);
        $materia = Materia::findOrFail($materiaId);
        if ($materia->docente_cargo_id !== $docente->id) abort(403);
        
        $tareas = Tarea::where('materia_id', $materiaId)->with(['creadaPor', 'documentos' => function($q) { $q->with('estudiante')->orderBy('version', 'desc'); }])->orderBy('fecha_limite', 'asc')->get();
        return view('docente.tareas', compact('materia', 'tareas'));
    }

/**
 * Actualizar título del proyecto
 */
public function actualizarProyecto(Request $request, $inscripcionId)
{
    
    try {
        $validated = $request->validate([
            'inscripcion_id' => 'required|exists:inscripciones,id',
            'titulo_proyecto' => 'nullable|string|max:500',
        ]);

        $inscripcion = Inscripcion::findOrFail($inscripcionId);
        $inscripcion->titulo_proyecto = $validated['titulo_proyecto'];
        $inscripcion->save();

        return response()->json([
            'success' => true,
            'message' => 'Título del proyecto actualizado correctamente',
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al actualizar título: ' . $e->getMessage(),
        ], 500);
    }
}
    /**
 * Ver detalles de una tarea
 */
public function verTarea($tareaId)
{
    
    $docente = Auth::user();
    if (!$docente->esDocenteCargo()) abort(403);

    $tarea = Tarea::with([
        'materia',
        'creadaPor',
        'documentos' => function($q) {
            $q->with(['estudiante', 'estado', 'observaciones' => function($q2) {
                $q2->with('revisor')->orderBy('created_at', 'desc');
            }]);
        }
    ])->findOrFail($tareaId);

    // Verificar que la tarea pertenezca a una materia de este docente
    if ($tarea->materia->docente_cargo_id !== $docente->id) {
        abort(403, 'No tienes permiso para ver esta tarea.');
    }

    // Obtener estudiantes que ya entregaron (únicos por estudiante_id)
    $entregados = $tarea->documentos->unique('estudiante_id');
    
    // Obtener todos los estudiantes de la materia
    $estudiantesMateria = Inscripcion::where('materia_id', $tarea->materia_id)
                                     ->where('docente_cargo_id', $docente->id)
                                     ->where('estado_inscripcion', 'activo')
                                     ->with(['estudiante', 'tutores'])
                                     ->get();
    
    
    $idsEntregaron = $entregados->pluck('estudiante_id')->toArray();
    
    // Filtrar estudiantes que aún no han entregado
    $pendientes = $estudiantesMateria->filter(function($ins) use ($idsEntregaron) {
        return !in_array($ins->estudiante_id, $idsEntregaron);
    })->pluck('estudiante');

    return view('docente.tareas-ver', compact('tarea', 'entregados', 'pendientes', 'estudiantesMateria'));
}

/**
 * Actualizar tarea
 */
public function actualizarTarea(Request $request, $tareaId)
{
     
    $docente = Auth::user();
    if (!$docente->esDocenteCargo()) abort(403);

    $tarea = Tarea::findOrFail($tareaId);
    
    // Verificar que la tarea pertenezca a una materia de este docente
    if ($tarea->materia->docente_cargo_id !== $docente->id) {
        abort(403, 'No tienes permiso para editar esta tarea.');
    }

    $validated = $request->validate([
        'titulo' => 'required|string|max:150',
        'descripcion' => 'required|string',
        'fecha_limite' => 'required|date',
        'tipo_documento' => 'required|in:anteproyecto,documento_final,anexos,otro',
    ]);

    $tarea->update($validated);

    return redirect()->route('docente.tareas-ver', $tarea->id)->with('success', 'Tarea actualizada correctamente.');
}
    
}