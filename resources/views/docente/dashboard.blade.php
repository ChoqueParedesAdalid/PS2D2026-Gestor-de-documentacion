@extends('layouts.docente')
@section('title', 'Dashboard - Docente a Cargo')
@section('page-title', 'DOCENTE A CARGO')

@section('content')
<div style="margin-bottom: 8px;">
    <h1 style="color: var(--text-primary); margin: 0; font-size: 2rem;">DOCENTE A CARGO</h1>
    <div style="color: var(--text-secondary);">Talleres | Paralelo A - Gestión 2027-I</div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-value">{{ $stats['total_estudiantes'] ?? 0 }}</div>
            <div class="stat-label">Estudiantes</div>
        </div>
        <div class="stat-icon"><i class="fas fa-users"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-value">{{ $stats['tareas_activas'] ?? 0 }}</div>
            <div class="stat-label">Tareas activas</div>
        </div>
        <div class="stat-icon"><i class="fas fa-tasks"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-value">{{ $stats['sin_tutor_jurado'] ?? 0 }}</div>
            <div class="stat-label">Sin tutor/Jurado</div>
        </div>
        <div class="stat-icon"><i class="fas fa-user-times"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-value">{{ $stats['entregas_hoy'] ?? 0 }}</div>
            <div class="stat-label">Entregas hoy</div>
        </div>
        <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
    </div>
</div>

<!-- Tareas List -->
<div class="section-card">
    <div class="section-header">
        <i class="fas fa-tasks"></i>
        <h3>Tareas de revisión</h3>
    </div>
    <div class="section-body">
        @forelse($tareas as $t)
        <div style="border-left: 4px solid {{ $t['estado'] === 'activa' ? 'var(--accent-red)' : '#555' }}; padding: 16px; margin-bottom: 16px; background: var(--bg-panel); border-radius: 8px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 600; color: var(--text-primary);">{{ $t['nombre'] }}</span>
                <span style="font-size: 11px; background: {{ $t['estado'] === 'activa' ? 'var(--primary-red)' : '#333' }}; color: white; padding: 3px 10px; border-radius: 12px;">
                    {{ $t['estado'] === 'activa' ? 'Activa' : 'En espera' }}
                </span>
            </div>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Fecha límite: {{ $t['fecha'] }}</div>
            
            <!-- Progress Bar -->
            <div style="background: rgba(255,255,255,0.1); border-radius: 4px; height: 8px; margin: 12px 0 6px;">
                <div style="background: var(--accent-red); height: 8px; border-radius: 4px; width: {{ $t['entregado'] > 0 ? ($t['entregado'] / 24) * 100 : 0 }}%;"></div>
            </div>
            
            <div style="display: flex; gap: 16px; font-size: 12px; color: var(--text-muted);">
                <span>Entregado: <strong style="color: #4caf50;">{{ $t['entregado'] }}</strong></span>
                <span>Pendiente: <strong style="color: #ffc107;">{{ $t['pendiente'] }}</strong></span>
                <span>Revisado: <strong style="color: #64b5f6;">{{ $t['revisado'] }}</strong></span>
            </div>
        </div>
        @empty
        <p style="color: var(--text-muted); text-align: center; padding: 40px;">No hay tareas registradas.</p>
        @endforelse
    </div>
</div>
@endsection