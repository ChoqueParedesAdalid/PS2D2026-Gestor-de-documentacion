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

    public function __construct($totalEstudiantes, $totalDocentes, $totalMaterias, $totalProyectos, $documentosPorEstado, $proyectosPorMateria)
    {
        $this->data = compact('totalEstudiantes', 'totalDocentes', 'totalMaterias', 'totalProyectos', 'documentosPorEstado', 'proyectosPorMateria');
    }

    public function collection()
    {
        // Hoja 1: Resumen
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

        // Documentos por estado
        $rows[] = [''];
        $rows[] = ['DOCUMENTOS POR ESTADO', ''];
        $rows[] = ['Estado', 'Cantidad'];
        foreach ($this->data['documentosPorEstado'] ?? [] as $estado => $total) {
            $rows[] = [ucfirst($estado), $total];
        }

        // Top materias
        $rows[] = [''];
        $rows[] = ['TOP MATERIAS CON PROYECTOS', ''];
        $rows[] = ['Materia', 'Proyectos'];
        foreach ($this->data['proyectosPorMateria'] ?? [] as $item) {
            $materia = is_object($item) ? $item->materia : ($item['materia'] ?? 'N/A');
            $total = is_object($item) ? $item->total : ($item['total'] ?? 0);
            $rows[] = [$materia, $total];
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return []; // Usamos collection() directamente
    }

    public function title(): string
    {
        return 'Reporte General';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '6D2121']], 'font' => ['color' => ['rgb' => 'FFFFFF']]],
            5 => ['font' => ['bold' => true]],
            10 => ['font' => ['bold' => true]],
            12 => ['font' => ['bold' => true]],
        ];
    }
}