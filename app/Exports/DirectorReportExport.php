<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DirectorReportExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $data;

    public function __construct(
        $totalEstudiantes, $totalDocentes, $totalMaterias, $totalProyectos,
        $documentosPorEstado, $proyectosPorMateria,
        $progresoPorMateria = null, $cargaTutores = null
    ) {
        $this->data = compact(
            'totalEstudiantes', 'totalDocentes', 'totalMaterias', 'totalProyectos',
            'documentosPorEstado', 'proyectosPorMateria',
            'progresoPorMateria', 'cargaTutores'
        );
    }

    public function collection()
    {
        $rows = [
            ['REPORTE GENERAL - DOCGEST', ''],
            ['Generado el:', date('d/m/Y H:i')],
            [''],
            ['ESTADÍSTICAS GENERALES', ''],
            ['Total Estudiantes', $this->data['totalEstudiantes']],
            ['Total Docentes', $this->data['totalDocentes']],
            ['Total Materias', $this->data['totalMaterias']],
            ['Total Proyectos', $this->data['totalProyectos']],
        ];

        $rows[] = [''];
        $rows[] = ['DOCUMENTOS POR ESTADO', ''];
        $rows[] = ['Estado', 'Cantidad'];
        foreach ($this->data['documentosPorEstado'] ?? [] as $estado => $total) {
            $rows[] = [ucfirst(str_replace('_', ' ', $estado)), $total];
        }

        $rows[] = [''];
        $rows[] = ['TOP MATERIAS CON PROYECTOS', ''];
        $rows[] = ['Materia', 'Proyectos'];
        foreach ($this->data['proyectosPorMateria'] ?? [] as $item) {
            $materia = is_object($item) ? $item->materia : ($item['materia'] ?? 'N/A');
            $total = is_object($item) ? $item->total : ($item['total'] ?? 0);
            $rows[] = [$materia, $total];
        }

        if ($this->data['progresoPorMateria']) {
            $rows[] = [''];
            $rows[] = ['PROGRESO POR MATERIA', ''];
            $rows[] = ['Materia', 'Estudiantes', 'Tareas', 'Entregas', '% Avance'];
            foreach ($this->data['progresoPorMateria'] as $item) {
                $rows[] = [
                    $item['nombre'],
                    $item['estudiantes'],
                    $item['tareas'],
                    $item['entregas_unicas'] . '/' . $item['total_esperado'],
                    $item['porcentaje'] . '%'
                ];
            }
        }

        if ($this->data['cargaTutores']) {
            $rows[] = [''];
            $rows[] = ['CARGA DE TUTORES', ''];
            $rows[] = ['Tutor', 'Tutorados', 'Documentos Pendientes'];
            foreach ($this->data['cargaTutores'] as $tutor) {
                $rows[] = [$tutor['nombre'], $tutor['tutorados'], $tutor['pendientes']];
            }
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Reporte General';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '6D2121']]],
            4 => ['font' => ['bold' => true]],
            10 => ['font' => ['bold' => true]],
            12 => ['font' => ['bold' => true]],
        ];
    }
}