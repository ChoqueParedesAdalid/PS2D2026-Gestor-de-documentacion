<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte General - DocGest</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        .header { text-align: center; border-bottom: 2px solid #6D2121; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #6D2121; margin: 0; font-size: 16px; }
        .header p { margin: 5px 0 0; color: #666; font-size: 9px; }
        .stats { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .stat-box { text-align: center; padding: 8px; border: 1px solid #ddd; width: 23%; }
        .stat-box h3 { margin: 0; font-size: 20px; color: #6D2121; }
        .stat-box p { margin: 5px 0 0; font-size: 9px; color: #666; }
        h3 { color: #6D2121; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-top: 20px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #6D2121; color: white; padding: 6px; text-align: left; font-size: 9px; }
        td { padding: 6px; border-bottom: 1px solid #ddd; font-size: 9px; }
        .footer { margin-top: 30px; text-align: center; font-size: 8px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Reporte General - DocGest</h1>
        <p>Universidad del Valle · Generado el {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="stats">
        <div class="stat-box">
            <h3>{{ $totalEstudiantes ?? 0 }}</h3>
            <p>Estudiantes</p>
        </div>
        <div class="stat-box">
            <h3>{{ $totalDocentes ?? 0 }}</h3>
            <p>Docentes</p>
        </div>
        <div class="stat-box">
            <h3>{{ $totalMaterias ?? 0 }}</h3>
            <p>Materias</p>
        </div>
        <div class="stat-box">
            <h3>{{ $totalProyectos ?? 0 }}</h3>
            <p>Proyectos</p>
        </div>
    </div>

    <h3>📈 Progreso por Materia</h3>
    <table>
        <thead>
            <tr>
                <th>Materia</th>
                <th>Estudiantes</th>
                <th>Tareas</th>
                <th>Entregas</th>
                <th>% Avance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($progresoPorMateria ?? [] as $item)
            <tr>
                <td>{{ $item['nombre'] }}</td>
                <td>{{ $item['estudiantes'] }}</td>
                <td>{{ $item['tareas'] }}</td>
                <td>{{ $item['entregas_unicas'] }}/{{ $item['total_esperado'] }}</td>
                <td>{{ $item['porcentaje'] }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>👨‍🏫 Carga de Tutores</h3>
    <table>
        <thead>
            <tr>
                <th>Tutor</th>
                <th>Tutorados</th>
                <th>Pendientes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cargaTutores ?? [] as $tutor)
            <tr>
                <td>{{ $tutor['nombre'] }}</td>
                <td>{{ $tutor['tutorados'] }}</td>
                <td>{{ $tutor['pendientes'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>📁 Documentos por Estado</h3>
    <table>
        <thead>
            <tr>
                <th>Estado</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($documentosPorEstado ?? [] as $estado => $total)
            <tr>
                <td>{{ ucfirst(str_replace('_', ' ', $estado)) }}</td>
                <td style="text-align: center;">{{ $total }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>🎓 Top Materias con Proyectos</h3>
    <table>
        <thead>
            <tr>
                <th>Materia</th>
                <th>Proyectos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proyectosPorMateria ?? [] as $item)
            <tr>
                <td>{{ is_object($item) ? $item->materia : ($item['materia'] ?? 'N/A') }}</td>
                <td style="text-align: center;">{{ is_object($item) ? $item->total : ($item['total'] ?? 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>DocGest · Sistema de Gestión Académica · Universidad del Valle</p>
        <p>Reporte generado por {{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}</p>
    </div>
</body>
</html>