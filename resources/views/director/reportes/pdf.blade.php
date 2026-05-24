<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte General - DocGest</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        .header { text-align: center; border-bottom: 2px solid #6D2121; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #6D2121; margin: 0; font-size: 18px; }
        .header p { margin: 5px 0 0; color: #666; }
        .stats { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .stat-box { text-align: center; padding: 10px; border: 1px solid #ddd; width: 23%; }
        .stat-box h3 { margin: 0; font-size: 24px; color: #6D2121; }
        .stat-box p { margin: 5px 0 0; font-size: 10px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #6D2121; color: white; padding: 8px; text-align: left; font-size: 10px; }
        td { padding: 8px; border-bottom: 1px solid #ddd; font-size: 10px; }
        .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Reporte General - DocGest</h1>
        <p>Universidad del Valle · Generado el {{ date('d/m/Y H:i') }}</p>
    </div>

    <!-- Estadísticas -->
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

    <!-- Documentos por Estado -->
    <h3 style="color: #6D2121; border-bottom: 1px solid #ddd; padding-bottom: 5px;">📁 Documentos por Estado</h3>
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
                <td>{{ ucfirst($estado) }}</td>
                <td style="text-align: center;">{{ $total }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Top Materias -->
    <h3 style="color: #6D2121; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-top: 20px;">🎓 Top Materias con Proyectos</h3>
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