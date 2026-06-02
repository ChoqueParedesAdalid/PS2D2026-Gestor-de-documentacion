<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte - {{ $estudiante->nombres }} {{ $estudiante->apellidos }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 30px; 
            font-size: 11px;
        }
        .header { 
            text-align: center; 
            border-bottom: 3px solid #6D2121; 
            padding-bottom: 15px; 
            margin-bottom: 25px; 
        }
        .header img { 
            max-width: 80px; 
        }
        .header h1 { 
            color: #6D2121; 
            margin: 8px 0; 
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            color: #333;
        }
        .student-info { 
            background: #f5f5f5; 
            padding: 12px; 
            border-radius: 5px; 
            margin-bottom: 25px; 
        }
        .student-info h2 { 
            margin: 0 0 10px 0; 
            color: #6D2121; 
            font-size: 14px;
        }
        .student-info p {
            margin: 4px 0;
        }
        h3 {
            color: #6D2121;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        /* ✅ CORREGIDO: Tabla con ajuste automático de texto */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px; 
            table-layout: fixed; /* Fuerza ancho fijo de columnas */
        }
        th { 
            background: #6D2121; 
            color: white; 
            padding: 8px; 
            text-align: left; 
            font-size: 10px;
            word-wrap: break-word;
        }
        td { 
            padding: 6px; 
            border-bottom: 1px solid #ddd; 
            vertical-align: top;
            
            /* ✅ CORREGIDO: Permite que el texto haga saltos de línea */
            word-wrap: break-word;
            word-break: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }
        
        /* ✅ CORREGIDO: Anchos de columna definidos para evitar que se corten */
        th:nth-child(1), td:nth-child(1) { width: 30%; } /* Documento */
        th:nth-child(2), td:nth-child(2) { width: 25%; } /* Tarea */
        th:nth-child(3), td:nth-child(3) { width: 8%; }  /* Versión */
        th:nth-child(4), td:nth-child(4) { width: 18%; } /* Fecha */
        th:nth-child(5), td:nth-child(5) { width: 19%; } /* Estado */
        
        .footer { 
            margin-top: 30px; 
            text-align: center; 
            font-size: 10px; 
            color: #666; 
        }
        
        /* ✅ Para evitar que el contenido se desborde de la página */
        .page-break {
            page-break-after: always;
        }
        
        /* ✅ Estilos para los estados */
        .estado {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
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
        
        {{-- ✅ CORREGIDO: Usar $inscripcion para el título del proyecto --}}
        <p>
            <strong>Proyecto:</strong> 
            @if(isset($inscripcion) && $inscripcion->titulo_proyecto)
                {{ $inscripcion->titulo_proyecto }}
            @else
                Sin proyecto asignado
            @endif
        </p>
        
        {{-- ✅ NUEVO: Información adicional de la materia --}}
        @if(isset($inscripcion) && $inscripcion->materia)
            <p><strong>Materia:</strong> {{ $inscripcion->materia->nombre ?? 'N/A' }}</p>
        @endif
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
                <td>{{ ucfirst(str_replace('_', ' ', $doc->estado->nombre ?? 'Desconocido')) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No hay documentos entregados.</p>
    @endif

    <div class="footer">
        
        <p>DocGest · Universidad del Valle</p>
    </div>
</body>
</html>