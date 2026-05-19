@extends('layouts.tutor')
@section('title', 'Gestión de Documentos - DocGest Univalle')
@section('page-title', 'Documentos')

@section('content')
<div class="page-header">
    <div>
        <h1>Gestión de Documentos</h1>
        <p>Revisa y aprueba los documentos de tus tutorados</p>
    </div>
</div>

<!-- Filtros -->
<div class="section-card mb-4">
    <div class="section-body">
        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="{{ route('tutor.documentos', array_merge(request()->query(), ['filtro'=>'pendientes'])) }}" 
               class="btn {{ request('filtro','pendientes')==='pendientes' ? 'btn-primary' : 'btn-outline-secondary' }}">
                <i class="fas fa-clock"></i> Pendientes
            </a>
            <a href="{{ route('tutor.documentos', array_merge(request()->query(), ['filtro'=>'aprobados'])) }}" 
               class="btn {{ request('filtro')==='aprobados' ? 'btn-success' : 'btn-outline-secondary' }}">
                <i class="fas fa-check-circle"></i> Aprobados
            </a>
        </div>
        <form method="GET" action="{{ route('tutor.documentos') }}" class="d-flex flex-wrap gap-2">
            <input type="hidden" name="filtro" value="{{ request('filtro','pendientes') }}">
            <input type="text" name="search" placeholder="Buscar documento o estudiante..." 
                   value="{{ request('search') }}" 
                   class="form-control" style="background:var(--bg-input);border-color:var(--border-color);color:var(--text-primary);min-width:200px">
            <select name="estudiante" onchange="this.form.submit()" 
                    class="form-select" style="background:var(--bg-input);border-color:var(--border-color);color:var(--text-primary);min-width:180px">
                <option value="">Todos los estudiantes</option>
                @if(isset($tutorados))
                    @foreach($tutorados as $est)
                        <option value="{{ $est->id }}" {{ request('estudiante')==$est->id ? 'selected' : '' }}>
                            {{ $est->nombres }} {{ $est->apellidos }}
                        </option>
                    @endforeach
                @endif
            </select>
            <button type="submit" class="btn" style="background:var(--primary-red);color:#fff">
                <i class="fas fa-search"></i> Filtrar
            </button>
        </form>
    </div>
</div>

<!-- Tabla -->
<div class="section-card">
    <div class="section-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Documento</th>
                        <th>Versión</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documentos ?? [] as $doc)
                    <tr>
                        <td class="fw-medium">{{ $doc->estudiante->nombres }} {{ $doc->estudiante->apellidos }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-file-pdf" style="color:#dc3545"></i>
                                <span style="color:var(--text-secondary);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                                    {{ $doc->archivo_nombre_original }}
                                </span>
                            </div>
                        </td>
                        <td style="color:var(--text-muted)">v{{ $doc->version }}.0</td>
                        <td style="color:var(--text-muted)">
                            {{ $doc->entregado_en ? $doc->entregado_en->format('d/m/Y') : '-' }}
                        </td>
                        <td>
                            @php
                                $estadoClass = match($doc->estado->nombre ?? '') {
                                    'entregado' => 'warning',
                                    'con_observaciones' => 'warning',
                                    'visto_bueno' => 'success',
                                    'aprobado_tribunal' => 'info',
                                    'rechazado' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge" style="background:var(--{{ $estadoClass }});color:#fff">
                                {{ ucfirst(str_replace('_', ' ', $doc->estado->nombre ?? 'no_entregado')) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('tutor.revisar', $doc->id) }}" class="btn-action" title="Revisar">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($doc->archivo_ruta)
                                <a href="{{ asset($doc->archivo_ruta) }}" target="_blank" class="btn-action" title="Descargar">
                                    <i class="fas fa-download"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5" style="color:var(--text-muted);background:var(--bg-panel);border-radius:8px">
                            <i class="fas fa-file-alt" style="font-size:3rem;opacity:.3;display:block;margin-bottom:1rem"></i>
                            <p class="mb-1" style="color:var(--text-secondary)">
                                No hay documentos 
                                @if(request('filtro')==='aprobados') aprobados @else pendientes de revisión @endif
                            </p>
                            <small>
                                @if(request('filtro')!=='aprobados')
                                    Los estudiantes aún no han subido documentos para revisar
                                @else
                                    Los documentos aprobados aparecerán aquí
                                @endif
                            </small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Paginación -->
@if(isset($documentos) && $documentos->hasPages())
<div class="d-flex justify-content-between align-items-center mt-4">
    <small style="color:var(--text-muted)">
        Mostrando {{ $documentos->firstItem()??0 }}-{{ $documentos->lastItem()??0 }} de {{ $documentos->total() }}
    </small>
    <div>{{ $documentos->appends(request()->query())->links() }}</div>
</div>
@endif
@endsection

@push('styles')
<style>
.pagination{display:flex;gap:.25rem}.pagination li{display:inline}.pagination span,.pagination a{display:inline-flex;align-items:center;justify-content:center;min-width:2rem;height:2rem;padding:0 .5rem;border:1px solid var(--border-color);border-radius:.375rem;background-color:var(--bg-input);color:var(--text-primary);font-size:.875rem;text-decoration:none;transition:all .2s}.pagination span.active,.pagination a:hover{background-color:var(--primary-red);border-color:var(--primary-red);color:#fff}.pagination span.disabled{opacity:.5;cursor:not-allowed}
</style>
@endpush