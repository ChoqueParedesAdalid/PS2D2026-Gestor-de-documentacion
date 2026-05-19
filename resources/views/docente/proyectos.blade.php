@extends('layouts.docente')
@section('title', 'proyectos')
@section('content')


@section('content')
    <h1>GESTIONAR PROYECTOS</h1>
    <div class="subtitle">Talleres | Paralelo A - Gestión 2027-I</div>

    <!-- Stats -->
    <div style="display: flex; gap: 16px; margin-bottom: 30px; flex-wrap: wrap;">
        <div class="card" style="min-width: 120px; text-align: center;">
            <div style="font-size: 36px; font-weight: bold; color: white;">{{ $stats['total_estudiantes'] ?? 0 }}</div>
            <div style="font-size: 11px; color: #ddd; margin-top: 4px;">Estudiantes</div>
        </div>
        <!-- ... otros stats iguales al dashboard ... -->
        <div class="card" style="min-width: 120px; text-align: center;">
            <div style="font-size: 36px; font-weight: bold; color: white;">{{ $stats['tareas_activas'] ?? 0 }}</div>
            <div style="font-size: 11px; color: #ddd; margin-top: 4px;">Tareas activas</div>
        </div>
    </div>

    <!-- Tareas -->
    <div style="margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <span style="font-weight: 600; font-size: 15px; color: white;">Tareas de revisión</span>
            <button class="btn btn-red">+ NUEVA TAREA</button>
        </div>
        @foreach($tareas as $t)
        <div class="card" style="border-left: 4px solid {{ $t['estado'] === 'activa' ? 'var(--accent-red)' : '#555' }}; padding: 16px; margin-bottom: 12px;">
            <div style="display: flex; justify-content: space-between;">
                <span style="font-weight: 600; color: white;">{{ $t['nombre'] }}</span>
                <span style="font-size: 11px; background: var(--primary-red); padding: 3px 10px; border-radius: 12px;">{{ $t['estado'] }}</span>
            </div>
            <div style="font-size: 12px; color: #aaa; margin-top: 4px;">Fecha límite: {{ $t['fecha'] }}</div>
            <div style="background: #4a1a1a; height: 6px; margin: 10px 0; border-radius: 4px;">
                <div style="background: var(--accent-red); height: 6px; border-radius: 4px; width: {{ ($t['entregado'] / 24) * 100 }}%;"></div>
            </div>
            <div style="font-size: 11px; color: #aaa;">Entregado: {{ $t['entregado'] }} · Pendiente: {{ $t['pendiente'] }}</div>
        </div>
        @endforeach
    </div>

    <!-- Buscador -->
    <div style="margin-bottom: 16px;">
        <input id="search-proy" placeholder="Buscar estudiante o proyecto..." style="width: 300px;">
    </div>

    <!-- Tabla Proyectos -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <table>
            <thead>
                <tr>
                    <th>Estudiante</th><th>Tutor</th><th>Jurados</th><th>Estado Rev1</th><th>Proyecto</th>
                </tr>
            </thead>
            <tbody id="proy-table-body">
                @foreach($estudiantes as $est)
                <tr data-name="{{ strtolower($est['nombre']) }}">
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--primary-red); display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                {{ substr($est['nombre'], 0, 1) }}
                            </div>
                            <span style="color: #ddd;">{{ $est['nombre'] }}</span>
                        </div>
                    </td>
                    <td style="color: #ddd;">{{ $est['tutor'] ?? '-' }}</td>
                    <td style="color: #ddd;">{{ $est['jurados'] ?? '-' }}</td>
                    <td>
                        <span style="padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600;
                            @if($est['estado']=='Entregado') background:#1a4a1a; color:#4caf50; @else background:#2a2a2a; color:#aaa; @endif">
                            {{ $est['estado'] }}
                        </span>
                    </td>
                    <td><input placeholder="Nombre del proyecto..." style="width: 180px;"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        const searchInput = document.getElementById('search-proy');
        const rows = document.querySelectorAll('#proy-table-body tr');
        searchInput.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            rows.forEach(row => {
                row.style.display = row.dataset.name.includes(term) ? '' : 'none';
            });
        });
    </script>
@endsection