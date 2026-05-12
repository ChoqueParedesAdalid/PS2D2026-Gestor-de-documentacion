<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\Documento;
use App\Models\Inscripcion;
use App\Models\User;
use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EstudianteController extends Controller
{
    public function index()
    {
        $estudiante = Auth::user();
        
        // Obtener inscripción activa - SIN relaciones que no existen
        $inscripcion = Inscripcion::where('estudiante_id', $estudiante->id)
            ->where('estado_inscripcion', 'activo')
            ->with(['materia', 'estudiante'])  // ← Solo relaciones que SÍ existen
            ->first();

        if (!$inscripcion) {
            return view('estudiante.index', [
                'inscripcion' => null,
                'tutor' => null,
                'tribunales' => collect(),
                'tareasConInfo' => collect(),
                'notificaciones' => collect(),
                'tareasPendientes' => 0,
                'tareasObservadas' => 0,
                'tareasAprobadas' => 0,
            ]);
        }

        // Tutor y tribunales: consultar con try/catch para evitar errores
        $tutor = null;
        $tribunales = collect();
        
        try {
            $tutorData = \DB::table('asignaciones_tutor')
                ->where('inscripcion_id', $inscripcion->id)
                ->where('activo', true)
                ->join('usuarios', 'asignaciones_tutor.tutor_id', '=', 'usuarios.id')
                ->select('usuarios.nombres', 'usuarios.apellidos', 'usuarios.email_institucional')
                ->first();
            if ($tutorData) {
                $tutor = (object)['tutor' => $tutorData];
            }
        } catch(\Exception $e) {}
        
        try {
            $tribunalesData = \DB::table('asignaciones_tribunal')
                ->where('inscripcion_id', $inscripcion->id)
                ->where('activo', true)
                ->join('usuarios', 'asignaciones_tribunal.tribunal_id', '=', 'usuarios.id')
                ->select('usuarios.nombres', 'usuarios.apellidos', 'usuarios.email_institucional')
                ->get();
            if ($tribunalesData->count() > 0) {
                $tribunales = $tribunalesData->map(function($t) {
                    return (object)['tribunal' => $t];
                });
            }
        } catch(\Exception $e) {}

        // Obtener tareas
        $tareas = Tarea::where('materia_id', $inscripcion->materia_id)
            ->with(['documentos' => function($query) use ($estudiante) {
                $query->where('estudiante_id', $estudiante->id)->orderBy('version', 'desc');
            }])
            ->orderBy('fecha_limite', 'asc')
            ->get();

        // Procesar tareas
        $tareasConInfo = $tareas->map(function($tarea) use ($estudiante) {
            $documento = $tarea->documentos->first();
            $ahora = Carbon::now();
            $fechaLimite = Carbon::parse($tarea->fecha_limite);
            
            return [
                'id' => $tarea->id,
                'titulo' => $tarea->titulo,
                'descripcion' => $tarea->descripcion,
                'fecha_limite' => $tarea->fecha_limite,
                'tipo_documento' => $tarea->tipo_documento,
                'documento' => $documento,
                'esta_vencida' => $ahora->gt($fechaLimite) && (!$documento || !in_array($documento->estado_id ?? 0, [4, 5])),
                'dias_restantes' => $fechaLimite->diffInDays($ahora, false),
                'estado' => $documento ? 'entregado' : 'no_entregado',
            ];
        });

        // Notificaciones
        $notificaciones = collect();
        try {
            $notificaciones = \DB::table('notificaciones')
                ->where('usuario_id', $estudiante->id)
                ->where('leida', false)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        } catch(\Exception $e) {}

        // Contadores
        $tareasPendientes = $tareasConInfo->filter(fn($t) => in_array($t['estado'], ['no_entregado', 'entregado']))->count();
        $tareasObservadas = $tareasConInfo->filter(fn($t) => $t['estado'] === 'con_observaciones')->count();
        $tareasAprobadas = $tareasConInfo->filter(fn($t) => $t['estado'] === 'aprobado_tribunal')->count();

        return view('estudiante.index', compact(
            'inscripcion', 'tutor', 'tribunales', 'tareasConInfo',
            'notificaciones', 'tareasPendientes', 'tareasObservadas', 'tareasAprobadas'
        ));
    }

    public function verTarea($id)
    {
        $estudiante = Auth::user();
        $tarea = Tarea::with(['documentos' => function($q) use ($estudiante) {
            $q->where('estudiante_id', $estudiante->id)->orderBy('version', 'desc');
        }])->findOrFail($id);

        $inscripcion = Inscripcion::where('estudiante_id', $estudiante->id)
            ->whereHas('materia', fn($q) => $q->where('id', $tarea->materia_id))
            ->first();

        if (!$inscripcion) abort(403, 'No tienes acceso');

        return view('estudiante.tarea-detalle', compact('tarea', 'inscripcion'));
    }

    public function subirEntrega(Request $request, $tareaId)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $estudiante = Auth::user();
        $tarea = Tarea::findOrFail($tareaId);

        $inscripcion = Inscripcion::where('estudiante_id', $estudiante->id)
            ->whereHas('materia', fn($q) => $q->where('id', $tarea->materia_id))
            ->first();

        if (!$inscripcion) abort(403);

        $ultimo = Documento::where('tarea_id', $tarea->id)
            ->where('estudiante_id', $estudiante->id)
            ->orderBy('version', 'desc')->first();

        $version = $ultimo ? $ultimo->version + 1 : 1;
        $archivo = $request->file('archivo');
        $nombre = time() . '_' . $estudiante->id . '_' . $tarea->id . '_v' . $version . '.' . $archivo->getClientOriginalExtension();
        $ruta = $archivo->storeAs('documentos/' . date('Y') . '/' . date('m'), $nombre, 'public');

        Documento::create([
            'tarea_id' => $tarea->id,
            'estudiante_id' => $estudiante->id,
            'version' => $version,
            'archivo_ruta' => $ruta,
            'archivo_nombre_original' => $archivo->getClientOriginalName(),
            'archivo_tamaño' => $archivo->getSize(),
            'archivo_hash' => hash_file('sha256', $archivo->getRealPath()),
            'estado_id' => 2,
            'entregado_en' => now(),
        ]);

        return redirect()->route('estudiante.tarea.ver', $tarea->id)
            ->with('success', 'Documento entregado. Versión: ' . $version);
    }

    public function misDocumentos()
    {
        $estudiante = Auth::user();
        $documentos = Documento::where('estudiante_id', $estudiante->id)
            ->with(['tarea'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('estudiante.mis-documentos', compact('documentos'));
    }

    public function marcarNotificacionLeida($id)
    {
        try {
            \DB::table('notificaciones')->where('id', $id)->where('usuario_id', Auth::id())->update(['leida' => true]);
        } catch(\Exception $e) {}
        return redirect()->back();
    }
}