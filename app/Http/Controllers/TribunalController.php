<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Documento;
use App\Models\Observacion;
use App\Models\Inscripcion;
use App\Models\AsignacionTribunal;
use App\Models\Tarea;
use App\Models\Notificacion;

class TribunalController extends Controller
{
    public function index()
    {
        $this->verificarAccesoTribunal();
        $tribunal = Auth::user();
        
        $inscripcionIds = AsignacionTribunal::where('tribunal_id', $tribunal->id)
                                           ->where('activo', true)
                                           ->pluck('inscripcion_id')
                                           ->toArray(); // ✅ Convertir a array

        $totalDocumentos = Documento::join('inscripciones', 'documentos.estudiante_id', '=', 'inscripciones.estudiante_id')
                                   ->whereIn('inscripciones.id', $inscripcionIds)
                                   ->where('documentos.estado_id', 4)
                                   ->count();
        $pendientes = $totalDocumentos;
        $aprobados = Documento::join('inscripciones', 'documentos.estudiante_id', '=', 'inscripciones.estudiante_id')
                             ->whereIn('inscripciones.id', $inscripcionIds)
                             ->where('documentos.estado_id', 5)
                             ->count();

        $actividadReciente = Documento::with(['estudiante', 'tarea'])
                                      ->join('inscripciones', 'documentos.estudiante_id', '=', 'inscripciones.estudiante_id')
                                      ->whereIn('inscripciones.id', $inscripcionIds)
                                      ->where('documentos.estado_id', 4)
                                      ->orderBy('documentos.entregado_en', 'desc')
                                      ->take(5)
                                      ->get();

        return view('tribunal.dashboard', compact('totalDocumentos', 'pendientes', 'aprobados', 'actividadReciente'));
    }

    public function estudiantes()
    {
        $this->verificarAccesoTribunal();
        $tribunal = Auth::user();
       
        $estudiantes = User::select('usuarios.*', 'inscripciones.titulo_proyecto', 'inscripciones.estado_inscripcion')
                     ->join('inscripciones', 'usuarios.id', '=', 'inscripciones.estudiante_id')
                     ->join('asignaciones_tribunal', 'inscripciones.id', '=', 'asignaciones_tribunal.inscripcion_id')
                     ->join('roles', 'usuarios.role_id', '=', 'roles.id')
                     ->where('roles.nombre', 'estudiante')
                     ->where('asignaciones_tribunal.tribunal_id', $tribunal->id)
                     ->where('usuarios.activo', true)
                     ->where('asignaciones_tribunal.activo', true)
                     ->paginate(10);

        return view('tribunal.estudiantes', compact('estudiantes'));
    }

    public function tareas(Request $request)
    {
        $this->verificarAccesoTribunal();
        $tribunal = Auth::user();
        
        $inscripcionIds = AsignacionTribunal::where('tribunal_id', $tribunal->id)
                                           ->where('activo', true)
                                           ->pluck('inscripcion_id')
                                           ->toArray(); // ✅ Convertir a array
        $materiaIds = Inscripcion::whereIn('id', $inscripcionIds)->pluck('materia_id')->toArray();

        $tareas = Tarea::whereIn('materia_id', $materiaIds)
                      ->with(['materia', 'documentos' => function($q) use ($tribunal) {
                          $q->with(['estudiante', 'estado', 'observaciones' => function($q2) { 
                              $q2->with('revisor')->orderBy('created_at', 'desc'); 
                          }]);
                      }])
                      ->orderBy('fecha_limite', 'asc')
                      ->get();

        return view('tribunal.tareas', compact('tareas'));
    }

    /**
     * Display task details for tribunal
     */
    public function verTarea($tareaId)
    {
        $this->verificarAccesoTribunal();
        $tribunal = Auth::user();
        
        $tarea = Tarea::with([
            'materia',
            'creadaPor',
            'documentos' => function($q) {
                $q->with(['estudiante', 'estado', 'observaciones' => function($q2) {
                    $q2->with('revisor')->orderBy('created_at', 'desc');
                }]);
            }
        ])->findOrFail($tareaId);

        // Obtener inscripciones de ESTE tribunal como array
        $inscripcionIds = AsignacionTribunal::where('tribunal_id', $tribunal->id)
                                        ->where('activo', true)
                                        ->pluck('inscripcion_id')
                                        ->toArray(); // ✅ Convertir a array
        
        $materiaIds = Inscripcion::whereIn('id', $inscripcionIds)->pluck('materia_id')->toArray();
        
        if (!in_array($tarea->materia_id, $materiaIds)) {
            abort(403, 'No tienes permiso para ver esta tarea.');
        }

        // === LÓGICA CORREGIDA - Filtrar SOLO estudiantes de ESTE tribunal ===
        
        // 1. ENTREGADOS: Documentos con "Visto Bueno" del tutor que ESTE tribunal AÚN NO ha revisado
        $entregados = $tarea->documentos->filter(function($doc) use ($tribunal, $inscripcionIds, $tarea) {
            // ✅ Verificar que el estudiante pertenezca a ESTE tribunal
            $estudianteInscripcion = Inscripcion::where('estudiante_id', $doc->estudiante_id)
                                               ->where('materia_id', $tarea->materia_id)
                                               ->first();
            
            if (!$estudianteInscripcion || !in_array($estudianteInscripcion->id, $inscripcionIds)) {
                return false; // Estudiante NO es de este tribunal
            }
            
            // Debe estar en estado "visto_bueno" (aprobado por tutor)
            if ($doc->estado_id != 4) return false;
            
            // NO debe tener observaciones de ESTE tribunal (aún no lo ha revisado)
            $tieneObsTribunal = $doc->observaciones->contains(function($obs) use ($tribunal) {
                return $obs->revisor_id === $tribunal->id && $obs->rol_revisor === 'tribunal';
            });
            
            return !$tieneObsTribunal;
        })->unique('estudiante_id');
        
        // 2. REVISADOS: Documentos que ESTE tribunal YA revisó
        $revisados = $tarea->documentos->filter(function($doc) use ($tribunal, $inscripcionIds, $tarea) {
            // ✅ Verificar que el estudiante pertenezca a ESTE tribunal
            $estudianteInscripcion = Inscripcion::where('estudiante_id', $doc->estudiante_id)
                                               ->where('materia_id', $tarea->materia_id)
                                               ->first();
            
            if (!$estudianteInscripcion || !in_array($estudianteInscripcion->id, $inscripcionIds)) {
                return false; // Estudiante NO es de este tribunal
            }
            
            // Opción A: Ya aprobado por tribunal (estado final)
            if ($doc->estado_id == 5) return true;
            
            // Opción B: Tiene observaciones de ESTE tribunal (ya lo revisó)
            $tieneObsTribunal = $doc->observaciones->contains(function($obs) use ($tribunal) {
                return $obs->revisor_id === $tribunal->id && $obs->rol_revisor === 'tribunal';
            });
            
            return $tieneObsTribunal;
        })->unique('estudiante_id');
        
        // 3. PENDIENTES: Estudiantes de ESTE tribunal que aún no han entregado
        $estudiantesMateria = Inscripcion::where('materia_id', $tarea->materia_id)
                                         ->whereIn('id', $inscripcionIds)
                                         ->where('estado_inscripcion', 'activo')
                                         ->with(['estudiante', 'tutores'])
                                         ->get();
        
        $idsProcesados = $entregados->merge($revisados)->pluck('estudiante_id')->toArray();
        $pendientes = $estudiantesMateria->filter(function($ins) use ($idsProcesados) {
            return !in_array($ins->estudiante_id, $idsProcesados);
        })->pluck('estudiante');

        return view('tribunal.tareas-ver', compact('tarea', 'entregados', 'revisados', 'pendientes', 'estudiantesMateria'));
    }

    public function documentos(Request $request)
    {
        $this->verificarAccesoTribunal();
        $tribunal = Auth::user();
        // ✅ CORREGIDO: Usar input() en lugar de get()
        $filtro = $request->input('filtro', 'pendientes');
        
        $inscripcionIds = AsignacionTribunal::where('tribunal_id', $tribunal->id)
                                           ->where('activo', true)
                                           ->pluck('inscripcion_id')
                                           ->toArray(); // ✅ Convertir a array
        
        $query = Documento::with(['estudiante', 'tarea', 'estado', 'observaciones'])
                          ->join('inscripciones', 'documentos.estudiante_id', '=', 'inscripciones.estudiante_id')
                          ->whereIn('inscripciones.id', $inscripcionIds);

        if ($filtro === 'pendientes') $query->where('documentos.estado_id', 4);
        elseif ($filtro === 'aprobados') $query->where('documentos.estado_id', 5);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('documentos.archivo_nombre_original', 'LIKE', "%{$search}%")
                  ->orWhereHas('estudiante', fn($q2) => $q2->where('nombres', 'LIKE', "%{$search}%")->orWhere('apellidos', 'LIKE', "%{$search}%"));
            });
        }

        $documentos = $query->orderBy('documentos.entregado_en', 'desc')->paginate(10)->withQueryString();
        return view('tribunal.documentos', compact('documentos', 'filtro'));
    }

    public function revisar($id)
    {
        $this->verificarAccesoTribunal();
        $tribunal = Auth::user();

        $documento = Documento::with(['estudiante', 'tarea', 'estado', 'observaciones' => fn($q) => $q->orderBy('created_at', 'desc')])
                    ->join('inscripciones', 'documentos.estudiante_id', '=', 'inscripciones.estudiante_id')
                    ->join('asignaciones_tribunal', 'inscripciones.id', '=', 'asignaciones_tribunal.inscripcion_id')
                    ->where('documentos.id', $id)
                    ->where('asignaciones_tribunal.tribunal_id', $tribunal->id)
                    ->where('asignaciones_tribunal.activo', true)
                    ->select('documentos.*')
                    ->firstOrFail();

        $todasLasObservaciones = Observacion::join('documentos', 'observaciones.documento_id', '=', 'documentos.id')
                                            ->where('documentos.tarea_id', $documento->tarea_id)
                                            ->where('documentos.estudiante_id', $documento->estudiante_id)
                                            ->whereIn('observaciones.rol_revisor', ['tutor', 'tribunal'])
                                            ->with('revisor')
                                            ->orderBy('observaciones.created_at', 'desc')
                                            ->select('observaciones.*')
                                            ->get();

        return view('tribunal.revisar', compact('documento', 'todasLasObservaciones'));
    }

    public function storeObservacion(Request $request)
    {
        $tribunal = Auth::user();
        $this->verificarAccesoTribunal();

        $validated = $request->validate([
            'id_documento' => 'required|exists:documentos,id', 
            'contenido' => 'required|string|max:1000|min:10', 
            'seccion' => 'nullable|string|max:100'
        ]);
        
        $documento = Documento::find($request->id_documento);

        $observacion = Observacion::create([
            'documento_id' => $request->id_documento, 
            'revisor_id' => $tribunal->id, 
            'rol_revisor' => 'tribunal',
            'comentario' => $validated['contenido'], 
            'seccion_documento' => $validated['seccion'] ?? 'General', 
            'resuelta' => false, 
            'created_at' => now(),
        ]);

        Notificacion::crear(
            $documento->estudiante_id, 
            '💬 Nueva observación del tribunal', 
            'El tribunal ha dejado una nueva observación.', 
            'nueva_observacion', 
            "documento:{$documento->id}"
        );

        return response()->json([
            'success' => true, 
            'message' => 'Observación agregada.', 
            'observacion' => [
                'id' => $observacion->id, 
                'comentario' => $observacion->comentario, 
                'seccion_documento' => $observacion->seccion_documento, 
                'created_at' => $observacion->created_at?->diffForHumans() ?? 'Justo ahora'
            ]
        ]);
    }

    public function marcarObservacionCorregida($id)
    {
        $tribunal = Auth::user();
        $this->verificarAccesoTribunal();
        
        $observacion = Observacion::findOrFail($id);
        $observacion->resuelta = true; 
        $observacion->resuelta_en = now(); 
        $observacion->save();
        
        return response()->json(['success' => true, 'message' => 'Observación marcada como corregida.']);
    }

    public function eliminarObservacion($id)
    {
        $tribunal = Auth::user();
        $this->verificarAccesoTribunal();
        
        Observacion::findOrFail($id)->delete();
        
        return response()->json(['success' => true, 'message' => 'Observación eliminada.']);
    }

    public function aprobarDocumento($id)
    {
        $tribunal = Auth::user();
        $this->verificarAccesoTribunal();
        
        $documento = Documento::findOrFail($id);
        
        if ($documento->estado_id !== 4) {
            return redirect()->back()->with('error', 'Estado no válido.');
        }
        
        $documento->estado_id = 5; 
        $documento->save();
        
        Notificacion::crear(
            $documento->estudiante_id, 
            '🎉 Documento aprobado por tribunal', 
            '¡Felicidades! Listo para defensa.', 
            'aprobado_final', 
            "documento:{$documento->id}"
        );
        
        return redirect()->route('tribunal.documentos')->with('success', 'Documento aprobado. ¡Listo para defensa!');
    }

    public function solicitarCorrecciones(Request $request, $id)
    {
        $tribunal = Auth::user();
        $this->verificarAccesoTribunal();
        
        $documento = Documento::findOrFail($id);
        $validated = $request->validate(['observaciones' => 'nullable|string|max:2000']);
        
        if ($validated['observaciones']) {
            Observacion::create([
                'documento_id' => $documento->id, 
                'revisor_id' => $tribunal->id, 
                'rol_revisor' => 'tribunal', 
                'comentario' => $validated['observaciones'], 
                'seccion_documento' => 'General', 
                'resuelta' => false, 
                'created_at' => now()
            ]);
        }
        
        $documento->estado_id = 3; 
        $documento->save();
        
        Notificacion::crear(
            $documento->estudiante_id, 
            '💬 Correcciones solicitadas', 
            'El tribunal requiere correcciones.', 
            'nueva_observacion', 
            "documento:{$documento->id}"
        );
        
        return redirect()->route('tribunal.documentos')->with('success', 'Correcciones solicitadas.');
    }

    /**
     * Verificar acceso para tribunal (permite acceso por rol principal O por asignación)
     */
    private function verificarAccesoTribunal()
    {
        $user = Auth::user();
        
        // Caso 1: Tiene el rol tribunal directamente → Acceso permitido
        if ($user->rol?->nombre === 'tribunal') {
            return true;
        }
        
        // Caso 2: Es tutor pero tiene asignación activa como tribunal → Acceso permitido
        if ($user->rol?->nombre === 'tutor') {
            $tieneAsignacion = AsignacionTribunal::where('tribunal_id', $user->id)
                                                     ->where('activo', true)
                                                     ->exists();
            if ($tieneAsignacion) {
                return true;
            }
        }
        
        // Caso 3: No cumple ninguna condición → Denegar acceso
        abort(403, 'No tienes permiso para acceder a esta sección como tribunal.');
    }
}