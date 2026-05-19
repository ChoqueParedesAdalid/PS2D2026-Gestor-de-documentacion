@extends('layouts.tutor')
@section('title', 'Mis Tutorados - DocGest Univalle')
@section('page-title', 'Mis Tutorados')

@section('content')
<div class="page-header">
    <div>
        <h1>Mis Estudiantes Tutorados</h1>
        <p>Gestiona los documentos y avances de tus estudiantes</p>
    </div>
    <form method="GET" action="{{ route('tutor.tutorados') }}" class="d-flex gap-2">
        <input type="text" name="search" placeholder="Buscar estudiante..." value="{{ request('search') }}" class="form-control" style="background:var(--bg-input);border-color:var(--border-color);color:var(--text-primary);min-width:250px">
        <button type="submit" class="btn" style="background:var(--primary-red);color:white"><i class="fas fa-search"></i></button>
    </form>
</div>

<div class="section-card">
    <div class="section-header">
        <i class="fas fa-users" style="color:var(--info)"></i>
        <h3>Lista de Estudiantes</h3>
    </div>
    <div class="section-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Estudiante</th>
                        <th>Proyecto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tutorados ?? [] as $estudiante)
                    <tr>
                        <td class="fw-medium">{{ explode('@', $estudiante->email_institucional)[0] }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:40px;height:40px;background:var(--primary-red);color:#fff">
                                    {{ substr($estudiante->nombres, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-medium">{{ $estudiante->nombres }} {{ $estudiante->apellidos }}</div>
                                    <small style="color:var(--text-muted)">{{ $estudiante->email_institucional }}</small>
                                </div>
                            </div>
                        </td>
                        <td style="color:var(--text-secondary)">{{ $estudiante->titulo_proyecto ?? 'Sin título' }}</td>
                        <td>
                            @php
                                $estadoClass = match($estudiante->estado_inscripcion) {
                                    'activo' => 'success',
                                    'finalizado' => 'info',
                                    'abandonado' => 'secondary',
                                    default => 'warning'
                                };
                            @endphp
                            <span class="badge" style="background:var(--{{ $estadoClass }});color:#fff">
                                {{ ucfirst($estudiante->estado_inscripcion ?? 'activo') }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <!-- 📁 Ver todos los documentos del estudiante -->
                                <a href="{{ route('tutor.documentos', ['estudiante' => $estudiante->id]) }}" class="btn-action" title="Ver documentos">
                                    <i class="fas fa-folder-open"></i>
                                </a>

                                <!-- 📋 Revisar documento pendiente (CORREGIDO) -->
                                @php
                                    $docPendiente = null;
                                    if (isset($estudiante->documentos)) {
                                        $docPendiente = $estudiante->documentos->where('estado_id', 2)->first();
                                    }
                                @endphp

                                @if($docPendiente)
                                    <a href="{{ route('tutor.revisar', $docPendiente->id) }}" class="btn-action edit" title="Revisar documento pendiente">
                                        <i class="fas fa-tasks"></i>
                                    </a>
                                @else
                                    <span class="btn-action" style="opacity: 0.3; cursor: not-allowed;" title="Sin documentos pendientes">
                                        <i class="fas fa-tasks"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5" style="color: var(--text-muted); background: var(--bg-panel); border-radius: 8px;">
                            <i class="fas fa-users" style="font-size: 3rem; opacity: 0.3; display: block; margin-bottom: 1rem;"></i>
                            <p class="mb-1" style="color: var(--text-secondary); font-weight: 500;">No tienes estudiantes tutorados asignados</p>
                            <small style="color: var(--text-muted);">Contacta al docente a cargo para recibir asignaciones</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(isset($tutorados) && $tutorados->hasPages())
<div class="d-flex justify-content-between align-items-center mt-4">
    <small style="color:var(--text-muted)">Mostrando {{ $tutorados->firstItem()??0 }}-{{ $tutorados->lastItem()??0 }} de {{ $tutorados->total() }}</small>
    <div>{{ $tutorados->appends(request()->query())->links() }}</div>
</div>
@endif
@endsection

@push('styles')
<style>
.pagination{display:flex;gap:.25rem}.pagination li{display:inline}.pagination span,.pagination a{display:inline-flex;align-items:center;justify-content:center;min-width:2rem;height:2rem;padding:0 .5rem;border:1px solid var(--border-color);border-radius:.375rem;background-color:var(--bg-input);color:var(--text-primary);font-size:.875rem;text-decoration:none;transition:all .2s}.pagination span.active,.pagination a:hover{background-color:var(--primary-red);border-color:var(--primary-red);color:#fff}.pagination span.disabled{opacity:.5;cursor:not-allowed}
</style>
@endpush