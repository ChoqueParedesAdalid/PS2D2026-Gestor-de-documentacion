<?php

namespace App\Http\Controllers\DocenteACargo;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class DocenteController extends Controller
{
    private $estudiantes = [
        ['id' => 1, 'nombre' => 'María Mamani', 'tutor' => 'Lic. Lima', 'jurados' => '2 Jurados', 'estado' => 'Entregado'],
        ['id' => 2, 'nombre' => 'Juan Quispe',  'tutor' => 'Lic. Hinojosa', 'jurados' => '1 Jurado', 'estado' => 'En revisión'],
        ['id' => 3, 'nombre' => 'Ana Ortiz',    'tutor' => 'Lic. Lima',     'jurados' => '2 Jurados', 'estado' => 'Pendiente'],
        ['id' => 4, 'nombre' => 'Ana Vega',     'tutor' => '',              'jurados' => '',          'estado' => 'Sin asignar'],
    ];

    private $tareas = [
        ['id' => 1, 'nombre' => 'Revisión Documental #1', 'fecha' => '20 Nov 2024', 'estado' => 'activa',  'entregado' => 17, 'pendiente' => 7, 'revisado' => 18],
        ['id' => 2, 'nombre' => 'Revisión Documental #2', 'fecha' => '31 Abril 2026','estado' => 'espera', 'entregado' => 0,  'pendiente' => 32,'revisado' => 0],
    ];

    public function dashboard()
    {
        if (!session('usuario') || session('usuario')['rol'] !== 'docente') {
            return redirect()->route('login');
        }
        return Inertia::render('DocenteACargo/Dashboard', [
            'estudiantes' => $this->estudiantes,
            'tareas'      => $this->tareas,
            'stats'       => [
                'total_estudiantes' => 24,
                'tareas_activas'    => 10,
                'sin_tutor_jurado'  => 5,
                'entregas_hoy'      => 8,
            ],
        ]);
    }

    public function estudiantes()
    {
        if (!session('usuario') || session('usuario')['rol'] !== 'docente') {
            return redirect()->route('login');
        }
        return Inertia::render('DocenteACargo/GestionEstudiantes', [
            'estudiantes' => $this->estudiantes,
        ]);
    }

    public function proyectos()
    {
        if (!session('usuario') || session('usuario')['rol'] !== 'docente') {
            return redirect()->route('login');
        }
        return Inertia::render('DocenteACargo/GestionProyectos', [
            'estudiantes' => $this->estudiantes,
            'tareas'      => $this->tareas,
            'stats'       => [
                'total_estudiantes' => 24,
                'tareas_activas'    => 10,
                'sin_tutor_jurado'  => 5,
                'entregas_hoy'      => 8,
            ],
        ]);
    }
    public function tareas()
    {
    if (!session('usuario') || session('usuario')['rol'] !== 'docente') {
        return redirect()->route('login');
    }
    return Inertia::render('DocenteACargo/GestionTareas', [
        'tareas' => $this->tareas,
    ]);
}
}