@extends('layouts.tutor')
@section('title', 'Dashboard - Tutor - DocGest Univalle')

@section('content')
<div class="page-header">
    <div>
        <h1>Panel de Control</h1>
        <p>Bienvenido, {{ Auth::user()->nombres }}</p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ $totalTutorados ?? 0 }}</h3>
            <p>Total Tutorados</p>
        </div>
        <div class="stat-icon"><i class="fas fa-users"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ $pendientes ?? 0 }}</h3>
            <p>Documentos Pendientes</p>
        </div>
        <div class="stat-icon"><i class="fas fa-clock"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ $aprobados ?? 0 }}</h3>
            <p>Documentos Aprobados</p>
        </div>
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ $enRevision ?? 0 }}</h3>
            <p>En Revisión</p>
        </div>
        <div class="stat-icon"><i class="fas fa-eye"></i></div>
    </div>
</div>

<div class="content-grid">
    <div class="section-card">
        <div class="section-header">
            <i class="fas fa-clock" style="color: var(--warning);"></i>
            <h3>Actividad Reciente</h3>
        </div>
        <div class="section-body">
            @forelse($actividadReciente ?? [] as $actividad)
            <div style="padding: 12px 0; border-bottom: 1px solid var(--border-soft); display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; background: var(--primary-red); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-file-upload" style="color: white;"></i>
                </div>
                <div style="flex: 1;">
                    <p style="margin: 0; font-weight: 500;">{{ $actividad->estudiante->nombres }} {{ $actividad->estudiante->apellidos }} subió un documento</p>
                    <small style="color: var(--text-muted);">{{ $actividad->created_at->diffForHumans() }}</small>
                </div>
                <a href="{{ route('tutor.revisar', $actividad->id) }}" style="color: var(--accent-red); text-decoration: none; font-weight: 500;">Revisar</a>
            </div>
            @empty
            <p style="text-align: center; color: var(--text-muted); padding: 40px 0;">No hay actividad reciente</p>
            @endforelse
        </div>
    </div>

    <div class="section-card">
        <div class="section-header">
            <i class="fas fa-bolt" style="color: var(--info);"></i>
            <h3>Acciones Rápidas</h3>
        </div>
        <div class="section-body quick-actions">
            <a href="{{ route('tutor.tutorados') }}" class="action-item">
                <i class="fas fa-users"></i><span>Ver Tutorados</span>
            </a>
            <a href="{{ route('tutor.documentos') }}" class="action-item">
                <i class="fas fa-file-alt"></i><span>Documentos Pendientes</span>
            </a>
            <a href="{{ route('tutor.revisar') }}" class="action-item">
                <i class="fas fa-tasks"></i><span>Revisar Documentos</span>
            </a>
            <a href="#" class="action-item">
                <i class="fas fa-chart-bar"></i><span>Generar Reporte</span>
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.pagination{display:flex;gap:.25rem}.pagination li{display:inline}.pagination span,.pagination a{display:inline-flex;align-items:center;justify-content:center;min-width:2rem;height:2rem;padding:0 .5rem;border:1px solid var(--border-color);border-radius:.375rem;background-color:var(--bg-input);color:var(--text-primary);font-size:.875rem;text-decoration:none;transition:all .2s}.pagination span.active,.pagination a:hover{background-color:var(--primary-red);border-color:var(--primary-red);color:#fff}.pagination span.disabled{opacity:.5;cursor:not-allowed}
</style>
@endpush