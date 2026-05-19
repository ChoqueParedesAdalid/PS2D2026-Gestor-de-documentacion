@extends('layouts.docente')
@section('title', 'tareas')
@section('content')


@section('content')
    <h1>GESTIONAR TAREAS</h1>
    <div class="subtitle">Talleres | Paralelo A - Gestión 2027-I</div>

    <div style="margin-bottom: 20px;">
        <button id="btn-nueva-tarea" class="btn btn-red" style="padding: 10px 20px; font-size: 13px; font-weight: 600;">
            + NUEVA TAREA
        </button>
    </div>

    <!-- Form Nueva Tarea (Oculto por defecto) -->
    <div id="form-nueva-tarea" class="card" style="display: none; margin-bottom: 24px; border: 1px solid var(--border-color);">
        <h3 style="margin-bottom: 16px; color: white;">Crear nueva tarea</h3>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <div>
                <label style="font-size: 12px; color: #aaa; display: block; margin-bottom: 4px;">Nombre</label>
                <input placeholder="Ej: Revisión Documental #3">
            </div>
            <div>
                <label style="font-size: 12px; color: #aaa; display: block; margin-bottom: 4px;">Fecha límite</label>
                <input type="date">
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button id="btn-cancelar" class="btn btn-dark">Cancelar</button>
                <button class="btn btn-red">Publicar tarea</button>
            </div>
        </div>
    </div>

    <!-- Lista de Tareas -->
    <div style="display: flex; flex-direction: column; gap: 12px;">
        @foreach($tareas as $t)
        <div class="card" style="padding: 0; border: 1px solid var(--border-color); overflow: hidden;">
            <div style="padding: 16px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-weight: 600; color: white; margin-bottom: 4px;">{{ $t['nombre'] }}</div>
                    <div style="font-size: 12px; color: #aaa;">Fecha límite: {{ $t['fecha'] }}</div>
                    <div style="font-size: 12px; color: #aaa; margin-top: 4px;">{{ $t['entregado'] }} entregaron · {{ $t['pendiente'] }} pendientes</div>
                </div>
                <button class="btn btn-primary toggle-entregas" data-target="entregas-{{ $t['id'] }}">
                    Ver entregas ▼
                </button>
            </div>
            
            <!-- Entregas (Oculto) -->
            <div id="entregas-{{ $t['id'] }}" style="display: none; border-top: 1px solid var(--border-color); padding: 16px; background: #1a0a0a;">
                <div style="font-size: 13px; color: #aaa; margin-bottom: 10px;">Estudiantes que entregaron:</div>
                <!-- Lista estática de ejemplo (o loop si tuvieras los datos) -->
                <div style="padding: 8px 0; border-bottom: 1px solid #2a1010; display: flex; align-items: center; gap: 8px;">
                    <div style="width: 24px; height: 24px; border-radius: 50%; background: var(--primary-red); display: flex; align-items: center; justify-content: center; font-size: 11px;">M</div>
                    <span style="font-size: 13px; color: #ddd;">María Mamani</span>
                    <span style="margin-left: auto; font-size: 11px; background: #1a4a1a; color: #4caf50; padding: 2px 8px; border-radius: 10px;">Entregado</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <script>
        // Toggle Form
        const btnNueva = document.getElementById('btn-nueva-tarea');
        const formNueva = document.getElementById('form-nueva-tarea');
        const btnCancel = document.getElementById('btn-cancelar');

        btnNueva.addEventListener('click', () => formNueva.style.display = formNueva.style.display === 'none' ? 'block' : 'none');
        btnCancel.addEventListener('click', () => formNueva.style.display = 'none');

        // Toggle Entregas Acordeón
        document.querySelectorAll('.toggle-entregas').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = document.getElementById(btn.dataset.target);
                const isVisible = target.style.display === 'block';
                target.style.display = isVisible ? 'none' : 'block';
                btn.textContent = isVisible ? 'Ver entregas ▼' : 'Cerrar ▲';
            });
        });
    </script>
@endsection