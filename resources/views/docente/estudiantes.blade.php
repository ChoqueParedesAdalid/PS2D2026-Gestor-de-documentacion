@extends('layouts.docente')
@section('title', 'Estudiantes')
@section('content')


@section('content')
    <h1>GESTIÓN DE ESTUDIANTES</h1>
    <div class="subtitle">Talleres | Paralelo A - Gestión 2027-I</div>

    <!-- Panel Asignar -->
    <div class="card" style="margin-bottom: 24px; border: 1px solid var(--border-color);">
        <h3 style="margin-bottom: 16px; font-size: 15px; color: white;">
            Asignar tutor y tribunal a: <span id="selected-student-name" style="color: var(--accent-red);">— selecciona un estudiante —</span>
        </h3>
        <input type="hidden" id="selected-student-id">

        <div style="margin-bottom: 14px;">
            <label style="font-size: 12px; color: #aaa; display: block; margin-bottom: 6px;">Nombre del proyecto</label>
            <input id="input-proyecto" placeholder="Ej: Sistema de gestión académica..." disabled style="opacity: 0.5;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
            <div>
                <label style="font-size: 12px; color: #aaa; display: block; margin-bottom: 6px;">Tutor</label>
                <input id="input-tutor" placeholder="Buscar tutor..." disabled style="opacity: 0.5;">
            </div>
            <div>
                <label style="font-size: 12px; color: #aaa; display: block; margin-bottom: 6px;">Tribunal 1</label>
                <input id="input-jurado1" placeholder="Buscar tribunal 1..." disabled style="opacity: 0.5;">
            </div>
            <div>
                <label style="font-size: 12px; color: #aaa; display: block; margin-bottom: 6px;">Tribunal 2</label>
                <input id="input-jurado2" placeholder="Buscar tribunal 2..." disabled style="opacity: 0.5;">
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 16px;">
            <button class="btn btn-red" style="padding: 10px 24px; font-size: 13px; font-weight: 600;">GUARDAR</button>
        </div>
    </div>

    <!-- Buscador -->
    <div style="margin-bottom: 16px;">
        <input id="search-input" placeholder="Buscar por nombre o apellido..." style="width: 300px;">
    </div>

    <!-- Tabla -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <table>
            <thead>
                <tr>
                    <th>Estudiante</th><th>Tutor</th><th>Tribunal 1</th><th>Tribunal 2</th><th>Estado</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody id="estudiantes-table-body">
                @foreach($estudiantes as $est)
                <tr data-id="{{ $est['id'] }}" 
                    data-name="{{ strtolower($est['nombre']) }}"
                    data-tutor="{{ $est['tutor'] }}" 
                    data-jurados="{{ $est['jurados'] }}"
                    data-estado="{{ $est['estado'] }}">
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
                    <td style="color: #ddd;">-</td>
                    <td>
                        <span style="background: 
                            @if($est['estado'] == 'Entregado') #1a4a1a; color: #4caf50;
                            @elseif($est['estado'] == 'En revisión') #4a3a00; color: #ffc107;
                            @elseif($est['estado'] == 'Pendiente') #4a1a1a; color: #e05555;
                            @else #2a2a2a; color: #aaa;
                            @endif
                            padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600;">
                            {{ $est['estado'] }}
                        </span>
                    </td>
                    <td><button class="btn btn-dark">Ver doc.</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        // Lógica de selección y búsqueda (reemplaza useState)
        const rows = document.querySelectorAll('#estudiantes-table-body tr');
        const searchInput = document.getElementById('search-input');
        const inputs = ['input-proyecto', 'input-tutor', 'input-jurado1', 'input-jurado2'];

        rows.forEach(row => {
            row.addEventListener('click', () => {
                // Quitar selección previa
                rows.forEach(r => r.classList.remove('selected'));
                // Seleccionar actual
                row.classList.add('selected');
                
                // Rellenar panel
                document.getElementById('selected-student-name').textContent = row.dataset.name.replace(/\b\w/g, l => l.toUpperCase());
                document.getElementById('input-tutor').value = row.dataset.tutor || '';
                document.getElementById('input-jurado1').value = row.dataset.jurados || '';
                
                // Habilitar inputs
                document.getElementById('input-proyecto').disabled = false;
                inputs.forEach(id => document.getElementById(id).style.opacity = '1');
            });
        });

        searchInput.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            rows.forEach(row => {
                const name = row.dataset.name;
                row.style.display = name.includes(term) ? '' : 'none';
            });
        });
    </script>
@endsection