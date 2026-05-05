<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Proyecto;
use App\Models\Documento;
use App\Models\Observacion;

class TutorController extends Controller
{
    /**
     * Display dashboard
     */
    public function index()
    {
        // Datos de ejemplo - Reemplazar con consultas reales a la BD
        $data = [
            'totalTutorados' => 12,
            'pendientes' => 8,
            'aprobados' => 24,
            'enRevision' => 5,
        ];
        
        return view('tutor.dashboard', $data);
    }

    /**
     * Display list of students
     */
    public function tutorados()
    {
        // Consulta real: $estudiantes = User::where('rol', 'estudiante')
        //                         ->whereHas('tutor', function($q) {
        //                             $q->where('id_tutor', auth()->id());
        //                         })->get();
        
        return view('tutor.tutorados');
    }

    /**
     * Display list of documents
     */
    public function documentos()
    {
        // Consulta real: $documentos = Documento::whereHas('proyecto', function($q) {
        //                                    $q->where('id_tutor', auth()->id());
        //                                })->get();
        
        return view('tutor.documentos');
    }

    /**
     * Display document review page
     */
    public function revisar($id = null)
    {
        // Consulta real: $documento = Documento::with(['estudiante', 'observaciones'])
        //                                   ->findOrFail($id);
        
        return view('tutor.revisar', [
            'documento' => null // $documento
        ]);
    }

    /**
     * Store observation
     */
    public function storeObservacion(Request $request)
    {
        $request->validate([
            'id_documento' => 'required|exists:documentos,id_documento',
            'contenido' => 'required|string|max:1000',
            'seccion' => 'nullable|string|max:100',
        ]);

        Observacion::create([
            'id_documento' => $request->id_documento,
            'id_docente' => auth()->id(),
            'tipo_revisor' => 'tutor',
            'contenido' => $request->contenido,
            'seccion_documento' => $request->seccion,
            'fecha_creacion' => now(),
            'resuelta' => false,
        ]);

        return redirect()->back()->with('success', 'Observación agregada correctamente');
    }

    /**
     * Approve document
     */
    public function aprobarDocumento($id)
    {
        // Actualizar estado del documento
        // Enviar notificación al estudiante
        
        return redirect()->route('tutor.documentos')->with('success', 'Documento aprobado correctamente');
    }

    /**
     * Request corrections
     */
    public function solicitarCorrecciones(Request $request, $id)
    {
        // Validar que existan observaciones
        // Actualizar estado del documento
        // Enviar notificación al estudiante
        
        return redirect()->route('tutor.documentos')->with('success', 'Correcciones solicitadas al estudiante');
    }
}