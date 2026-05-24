<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte - {{ $estudiante->nombres }} {{ $estudiante->apellidos }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .header { text-align: center; border-bottom: 3px solid #6D2121; padding-bottom: 20px; margin-bottom: 30px; }
        .header img { max-width: 100px; }
        .header h1 { color: #6D2121; margin: 10px 0; }
        .student-info { background: #f5f5f5; padding: 15px; border-radius: 5px; margin-bottom: 30px; }
        .student-info h2 { margin: 0 0 10px 0; color: #6D2121; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #6D2121; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('img/logo_univalle_footer.png') }}" alt="Universidad del Valle">
        <h1>DocGest - Sistema de Gestión Académica</h1>
        <p>Reporte de Documentos del Estudiante</p>
    </div>

    <div class="student-info">
        <h2>Información del Estudiante</h2>
        <p><strong>Nombre:</strong> {{ $estudiante->nombres }} {{ $estudiante->apellidos }}</p>
        <p><strong>Código:</strong> {{ $estudiante->codigo ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $estudiante->email_institucional }}</p>
        <p><strong>Proyecto:</strong> {{ $estudiante->titulo_proyecto ?? 'Sin proyecto asignado' }}</p>
    </div>

    <h3>Historial de Documentos Entregados</h3>
    
    @if($documentos->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Documento</th>
                <th>Tarea</th>
                <th>Versión</th>
                <th>Fecha Entrega</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($documentos as $doc)
            <tr>
                <td>{{ $doc->archivo_nombre_original }}</td>
                <td>{{ $doc->tarea->titulo ?? 'N/A' }}</td>
                <td>v{{ $doc->version }}</td>
                <td>{{ $doc->entregado_en->format('d/m/Y H:i') }}</td>
                <td>{{ ucfirst($doc->estado->nombre) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No hay documentos entregados.</p>
    @endif

    <div class="footer">
        <p>Generado el: {{ now()->format('d/m/Y H:i') }}</p>
        <p>DocGest · Universidad del Valle</p>
    </div>
</body>
</html>