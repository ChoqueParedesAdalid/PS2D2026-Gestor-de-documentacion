<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use App\Models\Gestion;
use App\Models\Materia;
use App\Models\Inscripcion;
use App\Models\Documento;
use Barryvdh\DomPDF\Facade\Pdf; 
use App\Exports\DirectorReportExport;
use Maatwebsite\Excel\Facades\Excel; 

class DirectorController extends Controller
{
    public function index()
    {
        $director = Auth::user();
        if (!$director->esDirector()) abort(403);

        $totalParalelos = Inscripcion::distinct('materia_id')->count();
        $docentesRegistrados = User::whereIn('role_id', function($q) {
            $q->select('id')->from('roles')->whereIn('nombre', ['docente_cargo', 'tutor', 'tribunal', 'docente']);
        })->count();
        $estudiantesActivos = User::where('role_id', function($q) {
            $q->select('id')->from('roles')->where('nombre', 'estudiante');
        })->where('activo', true)->count();
        $inscripcionesActivas = Inscripcion::where('estado_inscripcion', 'activo')->count();
        $actividadReciente = Documento::with(['estudiante', 'tarea'])->orderBy('created_at', 'desc')->take(10)->get();

        return view('director.dashboard', compact('totalParalelos', 'docentesRegistrados', 'estudiantesActivos', 'inscripcionesActivas', 'actividadReciente'));
    }

    // ================= GESTIÓN DE MATERIAS =================

    public function materias()
    {
        if (!Auth::user()->esDirector()) abort(403);
        $materias = Materia::with(['gestion', 'docenteCargo', 'inscripciones'])->orderBy('nombre')->get();
        return view('director.materias', compact('materias'));
    }

    public function crearMateria(Request $request)
    {
        if (!Auth::user()->esDirector()) abort(403);
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:500',
            'semestre' => 'required|in:7mo,8vo',
            'gestion_id' => 'required|exists:gestiones,id',
            'docente_cargo_id' => 'nullable|exists:usuarios,id',
        ]);

        Materia::create($request->only(['nombre', 'descripcion', 'semestre', 'gestion_id']) + ['semestre_requerido' => $request->semestre, 'docente_cargo_id' => $request->docente_cargo_id]);
        return redirect()->back()->with('success', 'Materia creada correctamente.');
    }

    public function verMateria($id)
    {
        if (!Auth::user()->esDirector()) abort(403);
        $materia = Materia::with(['gestion', 'docenteCargo', 'inscripciones.estudiante', 'inscripciones.tutores', 'inscripciones.tribunales', 'tareas'])->findOrFail($id);
        return view('director.materias-ver', compact('materia'));
    }

    public function editarMateria($id)
    {
        if (!Auth::user()->esDirector()) abort(403);
    
        $materia = Materia::findOrFail($id);
        $gestiones = Gestion::where('activa', true)->get(); 
        $docentes = User::whereHas('rol', fn($q) => $q->where('nombre', 'docente_cargo'))
                   ->where('activo', true)
                   ->orderBy('nombres')
                   ->get(); 
    
    return view('director.materias-editar', compact('materia', 'gestiones', 'docentes'));
    }

    /**
 * Actualizar materia
 */
public function actualizarMateria(Request $request, $id)
{
    if (!Auth::user()->esDirector()) abort(403);
    
    $validated = $request->validate([
        'nombre' => 'required|string|max:100',
        'descripcion' => 'nullable|string|max:500',
        'semestre' => 'required|in:7mo,8vo',
        'gestion_id' => 'required|exists:gestiones,id',
        'docente_cargo_id' => 'nullable|exists:usuarios,id', 
    ]);

    $materia = Materia::findOrFail($id);
    
    // Actualizar campos uno por uno para evitar problemas con null
    $materia->nombre = $validated['nombre'];
    $materia->descripcion = $validated['descripcion'] ?? $materia->descripcion;
    $materia->semestre_requerido = $validated['semestre'];
    $materia->gestion_id = $validated['gestion_id'];
    $materia->docente_cargo_id = $validated['docente_cargo_id']; 
    $materia->save();

    return redirect()->route('director.materias')->with('success', 'Materia actualizada correctamente.');
}

    // Funcionalidad para que Director asigne docente a materia existente (si fuera necesario aparte)
    public function asignarDocenteAMateria(Request $request)
    {
        if (!Auth::user()->esDirector()) abort(403);
        $request->validate(['materia_id' => 'required|exists:materias,id', 'docente_cargo_id' => 'required|exists:usuarios,id']);
        
        $materia = Materia::findOrFail($request->materia_id);
        $materia->docente_cargo_id = $request->docente_cargo_id;
        $materia->save();

        return redirect()->back()->with('success', 'Docente asignado a la materia.');
    }

    // ================= GESTIÓN DE DOCENTES =================

    public function docentes()
    {
        if (!Auth::user()->esDirector()) abort(403);
        $docentes = User::whereIn('role_id', function($q) {
            $q->select('id')->from('roles')->whereIn('nombre', ['docente_cargo', 'tutor', 'tribunal', 'docente']);
        })->with('rol')->orderBy('nombres')->paginate(15);
        return view('director.docentes', compact('docentes'));
    }

    public function crearDocente(Request $request)
    {
        if (!Auth::user()->esDirector()) abort(403);
        $validated = $request->validate([
            'nombres' => 'required|string|max:100', 'apellidos' => 'required|string|max:100',
            'email_institucional' => 'required|email|ends_with:@univalle.edu|unique:usuarios,email_institucional',
            'role_id' => 'required|exists:roles,id', 'password' => 'required|min:6',
        ]);
        User::create([
            'nombres' => $validated['nombres'], 'apellidos' => $validated['apellidos'],
            'email_institucional' => $validated['email_institucional'],
            'password_hash' => bcrypt($validated['password']), 'role_id' => $validated['role_id'], 'activo' => true,
        ]);
        return redirect()->back()->with('success', 'Docente registrado.');
    }

    public function actualizarRolDocente(Request $request, $id)
    {
        if (!Auth::user()->esDirector()) abort(403);
        $validated = $request->validate(['nuevo_rol' => 'required|in:docente_cargo,tutor,tribunal']);
        $docente = User::findOrFail($id);
        $nuevoRol = Role::where('nombre', $validated['nuevo_rol'])->first();
        if ($nuevoRol) { $docente->role_id = $nuevoRol->id; $docente->save(); }
        return redirect()->back()->with('success', 'Rol actualizado.');
    }

    public function verDocente($id)
    {
        // Mostrar detalles del docente, incluyendo materias asignadas como docente_cargo, tutorías y tribunales
        if (!Auth::user()->esDirector()) abort(403);
        $docente = User::with(['rol', 'asignacionesTutor', 'asignacionesTribunal'])->findOrFail($id);
        return view('director.docentes-ver', compact('docente'));
    }

    public function editarDocente($id)
    {
        if (!Auth::user()->esDirector()) abort(403);
        $docente = User::findOrFail($id);
        $roles = Role::whereIn('nombre', ['docente', 'docente_cargo', 'tutor', 'tribunal'])->get();
        return view('director.docentes-editar', compact('docente', 'roles'));
    }

    public function actualizarDocente(Request $request, $id)
    {
        if (!Auth::user()->esDirector()) abort(403);
        $validated = $request->validate([
            'nombres' => 'required|string|max:100', 'apellidos' => 'required|string|max:100',
            'email_institucional' => 'required|email|unique:usuarios,email_institucional,' . $id,
            'role_id' => 'required|exists:roles,id', 'activo' => 'boolean',
        ]);
        $docente = User::findOrFail($id);
        $docente->update($validated);
        return redirect()->route('director.docentes')->with('success', 'Docente actualizado.');
    }

    public function eliminarDocente($id)
    {
        if (!Auth::user()->esDirector()) abort(403);
        $docente = User::findOrFail($id);
        $docente->activo = false;
        $docente->save();
        return redirect()->route('director.docentes')->with('success', 'Docente desactivado.');
    }

    // ================= GESTIÓN DE ESTUDIANTES =================

    public function estudiantes()
    {
        if (!Auth::user()->esDirector()) abort(403);
        $estudiantes = User::where('role_id', function($q) {
            $q->select('id')->from('roles')->where('nombre', 'estudiante');
        })->with(['inscripciones.materia', 'inscripciones.tutores'])->orderBy('nombres')->paginate(15);
        return view('director.estudiantes', compact('estudiantes'));
    }

    public function buscarEstudiantes(Request $request)
    {
        if (!Auth::user()->esDirector()) return response()->json(['error' => 'No autorizado'], 403);
        $search = $request->get('q', '');
        $estudiantes = User::where('role_id', function($q) {
            $q->select('id')->from('roles')->where('nombre', 'estudiante');
        })->where(function($q) use ($search) {
            $q->where('nombres', 'LIKE', "%{$search}%")->orWhere('apellidos', 'LIKE', "%{$search}%");
        })->with(['inscripciones.materia', 'inscripciones.tutores'])->take(10)->get();

        return response()->json(['success' => true, 'estudiantes' => $estudiantes->map(function($e) {
            $ins = $e->inscripciones->first();
            return [
                'id' => $e->id, 'nombres' => $e->nombres, 'apellidos' => $e->apellidos, 'email' => $e->email_institucional,
                'proyecto' => $ins?->titulo_proyecto ?? 'Sin proyecto', 'tutor' => $ins?->tutores->first()?->nombres ?? 'Sin asignar', 'activo' => $e->activo
            ];
        })]);
    }

    public function verEstudiante($id)
    {
        if (!Auth::user()->esDirector()) abort(403);
        $estudiante = User::with(['inscripciones.materia', 'inscripciones.tutores', 'inscripciones.tribunales', 'inscripciones.documentos'])->findOrFail($id);
        return view('director.estudiantes-ver', compact('estudiante'));
    }

    public function eliminarEstudiante($id)
    {
        if (!Auth::user()->esDirector()) abort(403);
        $estudiante = User::findOrFail($id);
        $estudiante->activo = false;
        $estudiante->save();
        return redirect()->route('director.estudiantes')->with('success', 'Estudiante desactivado.');
    }

    // ================= DOCUMENTOS Y ASIGNACIONES =================

    public function documentos()
    {
        if (!Auth::user()->esDirector()) abort(403);
        $documentos = Documento::with(['estudiante', 'tarea.materia', 'estado'])->orderBy('created_at', 'desc')->paginate(20);
        return view('director.documentos', compact('documentos'));
    }
/**
 * Mostrar dashboard de reportes para Director con estadísticas generales y gráficos
 */
  
    public function reportes()
    {
        $director = Auth::user();
        
        // === ESTADÍSTICAS GENERALES ===
        $totalEstudiantes = User::join('roles', 'usuarios.role_id', '=', 'roles.id')
            ->where('roles.nombre', 'estudiante')
            ->where('usuarios.activo', true)
            ->count();
        
        $totalDocentes = User::join('roles', 'usuarios.role_id', '=', 'roles.id')
            ->whereIn('roles.nombre', ['docente', 'docente_cargo', 'tutor', 'tribunal'])
            ->where('usuarios.activo', true)
            ->count();
        
        $totalMaterias = Materia::count();
        $totalProyectos = Inscripcion::where('estado_inscripcion', 'activo')->count();
        
        // Documentos por estado
        $documentosPorEstado = Documento::join('estados_documento', 'documentos.estado_id', '=', 'estados_documento.id')
            ->select('estados_documento.nombre as estado', \DB::raw('COUNT(*) as total'))
            ->groupBy('estados_documento.nombre')
            ->pluck('total', 'estado');
        
        // Entregas por mes
        $entregasPorMes = Documento::selectRaw('MONTH(entregado_en) as mes, COUNT(*) as total')
            ->whereYear('entregado_en', date('Y'))
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');
        
        // Proyectos por materia
        $proyectosPorMateria = Inscripcion::join('materias', 'inscripciones.materia_id', '=', 'materias.id')
            ->where('inscripciones.estado_inscripcion', 'activo')
            ->select('materias.nombre as materia', \DB::raw('COUNT(*) as total'))
            ->groupBy('materias.nombre')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // === PROGRESO POR MATERIA (CORREGIDO) ===
        $progresoPorMateria = Materia::with(['inscripciones' => function($q) {
                $q->where('estado_inscripcion', 'activo');
            }, 'tareas'])
            ->get()
            ->map(function($materia) {
                $estudiantesActivos = $materia->inscripciones->count();
                $tareasActivas = $materia->tareas->count();
                
                // Entregas ÚNICAS por estudiante-tarea (solo la última versión de cada tarea)
                $entregasUnicas = 0;
                if ($estudiantesActivos > 0 && $tareasActivas > 0) {
                    foreach ($materia->inscripciones as $inscripcion) {
                        foreach ($materia->tareas as $tarea) {
                            $tieneEntrega = Documento::where('estudiante_id', $inscripcion->estudiante_id)
                                ->where('tarea_id', $tarea->id)
                                ->exists();
                            if ($tieneEntrega) {
                                $entregasUnicas++;
                            }
                        }
                    }
                }
                
                $totalEsperado = $estudiantesActivos * $tareasActivas;
                $porcentaje = $totalEsperado > 0 
                    ? round(($entregasUnicas / $totalEsperado) * 100, 1) 
                    : 0;

                return [
                    'nombre' => $materia->nombre,
                    'estudiantes' => $estudiantesActivos,
                    'tareas' => $tareasActivas,
                    'entregas_unicas' => $entregasUnicas,
                    'total_esperado' => $totalEsperado,
                    'porcentaje' => min($porcentaje, 100)
                ];
            });

        // === RENDIMIENTO DE ESTUDIANTES (CORREGIDO) ===
        $rendimientoEstudiantes = Inscripcion::where('estado_inscripcion', 'activo')
            ->with(['estudiante', 'tutores', 'materia'])
            ->get()
            ->map(function($inscripcion) {
                $estudiante = $inscripcion->estudiante;
                $tareas = \App\Models\Tarea::where('materia_id', $inscripcion->materia_id)->get();
                
                $tareasTotales = $tareas->count();
                $entregasUnicas = 0;
                $tareasVencidas = 0;
                
                foreach ($tareas as $tarea) {
                    // ¿Entregó al menos una versión?
                    $entrego = Documento::where('estudiante_id', $estudiante->id)
                        ->where('tarea_id', $tarea->id)
                        ->exists();
                    if ($entrego) {
                        $entregasUnicas++;
                    }
                    
                    // ¿Venció y no entregó?
                    if ($tarea->fecha_limite < now() && !$entrego) {
                        $tareasVencidas++;
                    }
                }
                
                return [
                    'estudiante' => $estudiante->nombre_completo,
                    'materia' => $inscripcion->materia->nombre ?? 'N/A',
                    'tutor' => $inscripcion->tutores->first()?->nombre_completo ?? 'Sin asignar',
                    'tareas_totales' => $tareasTotales,
                    'entregas' => $entregasUnicas,
                    'vencidas' => $tareasVencidas,
                    'proyecto' => $inscripcion->titulo_proyecto ?? 'Sin título',
                    'porcentaje' => $tareasTotales > 0 ? min(round(($entregasUnicas / $tareasTotales) * 100, 1), 100) : 0
                ];
            });

        // === CARGA DE TUTORES ===
        $cargaTutores = User::whereHas('rol', function($q) {
                $q->where('nombre', 'tutor');
            })
            ->where('activo', true)
            ->withCount(['asignacionesTutor' => function($q) {
                $q->where('activo', true);
            }])
            ->get()
            ->map(function($tutor) {
                $inscripcionIds = $tutor->asignacionesTutor()->where('activo', true)->pluck('inscripcion_id');
                $documentosPendientes = Documento::whereIn('estudiante_id', function($q) use ($inscripcionIds) {
                    $q->select('estudiante_id')->from('inscripciones')->whereIn('id', $inscripcionIds);
                })->where('estado_id', 2)->count();

                return [
                    'nombre' => $tutor->nombre_completo,
                    'tutorados' => $tutor->asignaciones_tutor_count,
                    'pendientes' => $documentosPendientes
                ];
            });

        // === ALERTAS Y VENCIMIENTOS ===
        $tareasProximas = \App\Models\Tarea::where('fecha_limite', '<=', now()->addDays(7))
            ->where('fecha_limite', '>=', now())
            ->with('materia')
            ->orderBy('fecha_limite')
            ->get();

        $estudiantesRezagados = Inscripcion::where('estado_inscripcion', 'activo')
            ->whereDoesntHave('estudiante.documentos', function($q) {
                $q->where('entregado_en', '>=', now()->subDays(30));
            })
            ->with(['estudiante', 'tutores'])
            ->take(10)
            ->get();

        $tareasVencidas = \App\Models\Tarea::where('fecha_limite', '<', now())
            ->with(['materia', 'documentos'])
            ->get()
            ->map(function($tarea) {
                $estudiantesSinEntregar = Inscripcion::where('materia_id', $tarea->materia_id)
                    ->where('estado_inscripcion', 'activo')
                    ->whereDoesntHave('estudiante.documentos', function($q) use ($tarea) {
                        $q->where('tarea_id', $tarea->id);
                    })->count();

                return [
                    'titulo' => $tarea->titulo,
                    'materia' => $tarea->materia->nombre ?? 'N/A',
                    'fecha_limite' => $tarea->fecha_limite->format('d/m/Y'),
                    'estudiantes_faltantes' => $estudiantesSinEntregar
                ];
            });

        return view('director.reportes', compact(
            'totalEstudiantes', 'totalDocentes', 'totalMaterias', 'totalProyectos',
            'documentosPorEstado', 'entregasPorMes', 'proyectosPorMateria',
            'progresoPorMateria', 'rendimientoEstudiantes', 'cargaTutores',
            'tareasProximas', 'estudiantesRezagados', 'tareasVencidas'
        ));
    }

    /**
     * Exportar reporte (PDF/Excel)
     */
    public function exportarReporte($tipo)
    {
        // === ESTADÍSTICAS GENERALES ===
        $totalEstudiantes = User::join('roles', 'usuarios.role_id', '=', 'roles.id')
            ->where('roles.nombre', 'estudiante')
            ->where('usuarios.activo', true)
            ->count();
        
        $totalDocentes = User::join('roles', 'usuarios.role_id', '=', 'roles.id')
            ->whereIn('roles.nombre', ['docente', 'docente_cargo', 'tutor', 'tribunal'])
            ->where('usuarios.activo', true)
            ->count();
        
        $totalMaterias = Materia::count();
        $totalProyectos = Inscripcion::where('estado_inscripcion', 'activo')->count();
        
        $documentosPorEstado = Documento::join('estados_documento', 'documentos.estado_id', '=', 'estados_documento.id')
            ->select('estados_documento.nombre as estado', \DB::raw('COUNT(*) as total'))
            ->groupBy('estados_documento.nombre')
            ->pluck('total', 'estado');
        
        $proyectosPorMateria = Inscripcion::join('materias', 'inscripciones.materia_id', '=', 'materias.id')
            ->where('inscripciones.estado_inscripcion', 'activo')
            ->select('materias.nombre as materia', \DB::raw('COUNT(*) as total'))
            ->groupBy('materias.nombre')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // PROGRESO POR MATERIA (CORREGIDO)
        $progresoPorMateria = Materia::with(['inscripciones' => function($q) {
                $q->where('estado_inscripcion', 'activo');
            }, 'tareas'])
            ->get()
            ->map(function($materia) {
                $estudiantesActivos = $materia->inscripciones->count();
                $tareasActivas = $materia->tareas->count();
                
                $entregasUnicas = 0;
                if ($estudiantesActivos > 0 && $tareasActivas > 0) {
                    foreach ($materia->inscripciones as $inscripcion) {
                        foreach ($materia->tareas as $tarea) {
                            $tieneEntrega = Documento::where('estudiante_id', $inscripcion->estudiante_id)
                                ->where('tarea_id', $tarea->id)
                                ->exists();
                            if ($tieneEntrega) {
                                $entregasUnicas++;
                            }
                        }
                    }
                }
                
                $totalEsperado = $estudiantesActivos * $tareasActivas;
                $porcentaje = $totalEsperado > 0 
                    ? round(($entregasUnicas / $totalEsperado) * 100, 1) 
                    : 0;

                return [
                    'nombre' => $materia->nombre,
                    'estudiantes' => $estudiantesActivos,
                    'tareas' => $tareasActivas,
                    'entregas_unicas' => $entregasUnicas,
                    'total_esperado' => $totalEsperado,
                    'porcentaje' => min($porcentaje, 100)
                ];
            });

        // CARGA DE TUTORES
        $cargaTutores = User::whereHas('rol', function($q) {
                $q->where('nombre', 'tutor');
            })
            ->where('activo', true)
            ->withCount(['asignacionesTutor' => function($q) {
                $q->where('activo', true);
            }])
            ->get()
            ->map(function($tutor) {
                $inscripcionIds = $tutor->asignacionesTutor()->where('activo', true)->pluck('inscripcion_id');
                $documentosPendientes = Documento::whereIn('estudiante_id', function($q) use ($inscripcionIds) {
                    $q->select('estudiante_id')->from('inscripciones')->whereIn('id', $inscripcionIds);
                })->where('estado_id', 2)->count();

                return [
                    'nombre' => $tutor->nombre_completo,
                    'tutorados' => $tutor->asignaciones_tutor_count,
                    'pendientes' => $documentosPendientes
                ];
            });

        if ($tipo === 'pdf') {
            $pdf = Pdf::loadView('director.reportes.pdf', compact(
                'totalEstudiantes', 'totalDocentes', 'totalMaterias', 'totalProyectos',
                'documentosPorEstado', 'proyectosPorMateria',
                'progresoPorMateria', 'cargaTutores'
            ));
            return $pdf->download('reporte-docgest-' . date('Y-m-d') . '.pdf');

        } elseif ($tipo === 'excel') {
            return Excel::download(
                new DirectorReportExport(
                    $totalEstudiantes, $totalDocentes, $totalMaterias, $totalProyectos,
                    $documentosPorEstado, $proyectosPorMateria,
                    $progresoPorMateria, $cargaTutores
                ),
                'reporte-docgest-' . date('Y-m-d') . '.xlsx'
            );
        }

        return back()->with('error', 'Tipo de exportación no válido.');
    }
}