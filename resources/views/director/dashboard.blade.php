@extends('layouts.director')
@section('page-title', 'Panel de Control')

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
            <h3>{{ $stats['paralelos'] ?? 0 }}</h3>
            <p>Total Paralelos</p>
        </div>
        <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
    </div>
    
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ $stats['docentes'] ?? 0 }}</h3>
            <p>Docentes Registrados</p>
        </div>
        <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
    </div>
    
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ $stats['estudiantes'] ?? 0 }}</h3>
            <p>Estudiantes Activos</p>
        </div>
        <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
    </div>
    
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ $stats['inscripciones_activas'] ?? 0 }}</h3>
            <p>Inscripciones Activas</p>
        </div>
        <div class="stat-icon"><i class="fas fa-file-signature"></i></div>
    </div>
</div>

<div class="content-grid">
    <div class="section-card">
        <div class="section-header">
            <i class="fas fa-clock" style="color: var(--accent-red);"></i>
            <h3>Actividad Reciente</h3>
        </div>
        <div class="section-body" style="text-align: center; color: var(--text-muted); padding: 60px 20px;">
            <i class="fas fa-inbox" style="font-size: 3rem; opacity: 0.3; margin-bottom: 16px;"></i>
            <p>No hay actividad reciente</p>
        </div>
    </div>

    <div class="section-card">
        <div class="section-header">
            <i class="fas fa-bolt" style="color: var(--warning);"></i>
            <h3>Acciones Rápidas</h3>
        </div>
        <div class="section-body quick-actions">
            <a href="{{ route('director.paralelos') }}" class="action-item">
                <i class="fas fa-layer-group"></i>
                <span>Ver Paralelos</span>
            </a>
            <a href="{{ route('director.paralelos.crear') }}" class="action-item">
                <i class="fas fa-plus-circle"></i>
                <span>Crear Paralelo</span>
            </a>
            <a href="{{ route('director.docentes') }}" class="action-item">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Docentes Registrados</span>
            </a>
            <a href="#" class="action-item">
                <i class="fas fa-file-export"></i>
                <span>Generar Reporte</span>
            </a>
        </div>
    </div>
</div>
@endsection