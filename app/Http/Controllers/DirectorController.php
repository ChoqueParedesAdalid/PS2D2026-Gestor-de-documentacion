<?php

namespace App\Http\Controllers;

use App\Models\Paralelo;
use App\Models\Inscripcion;
use App\Models\User;
use App\Models\Materia;
use App\Models\Gestion;  // ✅ CORREGIDO: Gestion (sin "e")
use App\Models\AsignacionesTutor;
use App\Models\AsignacionesTribunal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DirectorController extends Controller
{
    // ==========================================
    // ✅ DASHBOARD PRINCIPAL
    // ==========================================
    public function index()
    {
        $stats = [
            'paralelos' => Paralelo::count(),
            'docentes' => User::whereIn('role_id', [3, 4, 5])->where('activo', true)->count(),
            'estudiantes' => User::where('role_id', 1)->where('activo', true)->count(),
            'inscripciones_activas' => Inscripcion::where('estado_inscripcion', 'activo')->count(),
        ];

        $paralelosRecientes = Paralelo::with(['materia', 'docenteCargo', 'gestion'])
            ->latest()
            ->take(5)
            ->get();

        // ✅ CORREGIDO: Gestion en lugar de Gestione
        $gestionesActivas = Gestion::where('activa', true)->first();

        return view('director.dashboard', compact('stats', 'paralelosRecientes', 'gestionesActivas'));
    }

    // ==========================================
    // ✅ PARALELOS - LISTA
    // ==========================================
    public function paralelos()
    {
        $paralelos = Paralelo::with(['materia', 'docenteCargo', 'gestion'])
            ->orderBy('letra')
            ->get();

        return view('director.paralelos.index', compact('paralelos'));
    }

    // ==========================================
    // ✅ PARALELOS - FORMULARIO CREAR
    // ==========================================
   public function crearParalelo()
{
    $gestiones = Gestion::where('activa', true)->get();
    $materias = Materia::all();
    
    // ✅ CAMBIA el 3 por el role_id correcto de "docente_cargo" en tu BD
    $docentes = User::where('role_id', 3)->where('activo', true)->get();

    return view('director.paralelos.crear', compact('gestiones', 'materias', 'docentes'));
}

    // ==========================================
    // ✅ PARALELOS - GUARDAR CON VALIDACIONES
    // ==========================================
    public function guardarParalelo(Request $request)
    {
        $validated = $request->validate([
            'gestion_id' => 'required|exists:gestiones,id',
            'materia_id' => 'required|exists:materias,id',
            'letra' => 'required|string|max:5',
            'docente_cargo_id' => 'nullable|exists:usuarios,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ], [
            'letra.required' => 'La letra del paralelo es obligatoria',
            'letra.max' => 'La letra no puede exceder 5 caracteres',
            'docente_cargo_id.exists' => 'El docente seleccionado no existe',
        ]);

        // ✅ VALIDACIÓN 1: Docente máximo 2 paralelos activos
        if (!empty($validated['docente_cargo_id'])) {
            $count = Paralelo::where('docente_cargo_id', $validated['docente_cargo_id'])
                ->where('estado', 'activo')
                ->count();
            
            if ($count >= 2) {
                return back()->withErrors([
                    'docente_cargo_id' => '⚠️ Este docente ya tiene 2 paralelos activos asignados. No puede tener más para evitar sobrecarga.'
                ])->withInput();
            }
        }

        // ✅ VALIDACIÓN 2: No duplicar paralelo (misma gestión + materia + letra)
        $existe = Paralelo::where('gestion_id', $validated['gestion_id'])
            ->where('materia_id', $validated['materia_id'])
            ->where('letra', strtoupper($validated['letra']))
            ->where('id', '!=', $request->input('id'))
            ->exists();

        if ($existe) {
            return back()->withErrors([
                'letra' => '⚠️ Ya existe un paralelo "' . strtoupper($validated['letra']) . '" para esta materia en la gestión seleccionada.'
            ])->withInput();
        }

        // ✅ Preparar datos finales
        $validated['letra'] = strtoupper($validated['letra']);
        $validated['estado'] = 'activo';

        // ✅ Crear paralelo
        Paralelo::create($validated);

        return redirect()->route('director.paralelos')
            ->with('success', '✅ Paralelo "' . $validated['letra'] . '" creado correctamente.');
    }

    // ==========================================
    // ✅ DOCENTES - LISTA
    // ==========================================
    public function docentes()
    {
        $docentesCarga = User::where('role_id', 3)->where('activo', true)->get();
        $tutores = User::where('role_id', 4)->where('activo', true)->get();
        $tribunales = User::where('role_id', 5)->where('activo', true)->get();
        
        $stats = [
            'total' => User::whereIn('role_id', [3, 4, 5])->where('activo', true)->count(),
            'docentes_carga' => $docentesCarga->count(),
            'tutores' => $tutores->count(),
            'tribunales' => $tribunales->count(),
        ];
        
        return view('director.docentes.index', compact('docentesCarga', 'tutores', 'tribunales', 'stats'));
    }

    // ==========================================
    // ✅ DOCENTES - FORMULARIO CREAR
    // ==========================================
    public function crearDocente()
    {
        // Obtener roles disponibles (3=docente_cargo, 4=tutor, 5=tribunal)
        $roles = [
            ['id' => 3, 'nombre' => 'Docente a Cargo'],
            ['id' => 4, 'nombre' => 'Tutor'],
            ['id' => 5, 'nombre' => 'Tribunal'],
        ];
        
        return view('director.docentes.crear', compact('roles'));
    }

    // ==========================================
    // ✅ DOCENTES - GUARDAR
    // ==========================================
    public function guardarDocente(Request $request)
    {
        $validated = $request->validate([
            'email_institucional' => 'required|email|unique:usuarios,email_institucional',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'password' => 'required|min:6|confirmed',
            'role_id' => 'required|in:3,4,5',
            'ci' => 'required|string|max:20|unique:usuarios,ci',
            'telefono' => 'nullable|string|max:20',
        ], [
            'email_institucional.unique' => 'Este correo institucional ya está registrado',
            'ci.unique' => 'Este número de CI ya está registrado',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ]);

        User::create([
            'email_institucional' => $validated['email_institucional'],
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'password_hash' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'ci' => $validated['ci'],
            'telefono' => $validated['telefono'] ?? null,
            'activo' => true,
        ]);

        return redirect()->route('director.docentes')
            ->with('success', '✅ Docente registrado correctamente');
    }

    // ==========================================
    // ✅ ESTUDIANTES - LISTA
    // ==========================================
    public function estudiantes()
    {
        $estudiantes = User::where('role_id', 1)->where('activo', true)
            ->with(['inscripciones' => function($query) {
                $query->with(['materia', 'paralelo']);
            }])
            ->get();
        
        $stats = [
            'total' => User::where('role_id', 1)->where('activo', true)->count(),
            'inscritos' => Inscripcion::where('estado_inscripcion', 'activo')->count(),
        ];
        
        return view('director.estudiantes.index', compact('estudiantes', 'stats'));
    }

    // ==========================================
    // ✅ ESTUDIANTES - FORMULARIO CREAR
    // ==========================================
    public function crearEstudiante()
    {
        return view('director.estudiantes.crear');
    }

//metodfo para el dire
// ✅ EDITAR PARALELO - Formulario
public function editarParalelo($id)
{
    $paralelo = Paralelo::findOrFail($id);
    $gestiones = Gestion::where('activa', true)->get();
    $materias = Materia::all();
    $docentes = User::where('role_id', 3)->where('activo', true)->get();
    
    return view('director.paralelos.editar', compact('paralelo', 'gestiones', 'materias', 'docentes'));
}

// ✅ ACTUALIZAR PARALELO
public function actualizarParalelo(Request $request, $id)
{
    $paralelo = Paralelo::findOrFail($id);
    
    $validated = $request->validate([
        'gestion_id' => 'required|exists:gestiones,id',
        'materia_id' => 'required|exists:materias,id',
        'letra' => 'required|string|max:5',
        'docente_cargo_id' => 'nullable|exists:usuarios,id',
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
    ]);
    
    // Validar que el docente no tenga más de 2 paralelos (excluyendo el actual)
    if (!empty($validated['docente_cargo_id'])) {
        $count = Paralelo::where('docente_cargo_id', $validated['docente_cargo_id'])
            ->where('estado', 'activo')
            ->where('id', '!=', $id)
            ->count();
        
        if ($count >= 2) {
            return back()->withErrors([
                'docente_cargo_id' => 'Este docente ya tiene 2 paralelos activos asignados.'
            ])->withInput();
        }
    }
    
    $paralelo->update($validated);
    
    return redirect()->route('director.paralelos')->with('success', 'Paralelo actualizado correctamente');
}

// ✅ VER DETALLE DE PARALELO
public function detalleParalelo($id)
{
    $paralelo = Paralelo::with(['materia', 'docenteCargo', 'gestion', 'estudiantes'])->findOrFail($id);
    
    $stats = [
        'total_estudiantes' => $paralelo->estudiantes->count(),
        'inscripciones_activas' => $paralelo->estudiantes->where('pivot.estado_inscripcion', 'activo')->count(),
    ];
    
    return view('director.paralelos.detalle', compact('paralelo', 'stats'));
}

// ✅ VER ESTUDIANTES POR PARALELO
public function estudiantesPorParalelo($id)
{
    $paralelo = Paralelo::with(['materia', 'docenteCargo', 'gestion', 'estudiantes'])->findOrFail($id);
    
    return view('director.paralelos.estudiantes', compact('paralelo'));
}

    // //
    // ==========================================
    // ✅ ESTUDIANTES - GUARDAR
    // ==========================================


    public function guardarEstudiante(Request $request)
    {
        $validated = $request->validate([
            'email_institucional' => 'required|email|unique:usuarios,email_institucional',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'password' => 'required|min:6|confirmed',
            'ci' => 'required|string|max:20|unique:usuarios,ci',
            'telefono' => 'nullable|string|max:20',
        ], [
            'email_institucional.unique' => 'Este correo institucional ya está registrado',
            'ci.unique' => 'Este número de CI ya está registrado',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ]);

        $estudiante = User::create([
            'email_institucional' => $validated['email_institucional'],
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'password_hash' => Hash::make($validated['password']),
            'role_id' => 1, // estudiante
            'ci' => $validated['ci'],
            'telefono' => $validated['telefono'] ?? null,
            'activo' => true,
        ]);

        return redirect()->route('director.estudiantes')
            ->with('success', '✅ Estudiante registrado correctamente');
 
 
            }
}