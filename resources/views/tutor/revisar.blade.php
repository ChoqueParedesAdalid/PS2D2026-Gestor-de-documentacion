@extends('layouts.tutor')
@section('title', 'Revisar Documento - DocGest Univalle')
@section('page-title', 'Revisar Documento')

@section('content')
<div class="content-grid">
    <!-- VISOR DE DOCUMENTO -->
    <div class="section-card">
        <div class="section-header">
            <i class="fas fa-file-pdf" style="color:var(--danger)"></i>
            <h3>{{ $documento->archivo_nombre_original ?? 'Documento' }}</h3>
        </div>
        <div class="section-body" style="padding:0">
            @if($documento->archivo_ruta)
                @if(pathinfo($documento->archivo_ruta, PATHINFO_EXTENSION) === 'pdf')
                <embed src="{{ asset($documento->archivo_ruta) }}" type="application/pdf" style="width:100%;height:700px;background:#fff">
                @else
                <div style="padding:40px;text-align:center;color:var(--text-muted)">
                    <i class="fas fa-file" style="font-size:4rem;opacity:.3;margin-bottom:1rem"></i>
                    <p>Vista previa no disponible para este formato</p>
                    <a href="{{ asset($documento->archivo_ruta) }}" target="_blank" style="color:var(--accent-red)">
                        Abrir documento en nueva pestaña →
                    </a>
                </div>
                @endif
            @else
            <div style="padding:40px;text-align:center;color:var(--text-muted)">
                <i class="fas fa-file-alt" style="font-size:4rem;opacity:.3;margin-bottom:1rem"></i>
                <p>Documento no disponible</p>
            </div>
            @endif
        </div>
        <div style="padding:16px 24px;border-top:1px solid var(--border-color);display:flex;gap:12px;justify-content:flex-end">
            @if($documento->archivo_ruta)
            <a href="{{ asset($documento->archivo_ruta) }}" target="_blank" class="btn" style="background:var(--bg-input);color:var(--text-primary)">
                <i class="fas fa-download"></i> Descargar
            </a>
            @endif
            <button onclick="toggleFullscreen()" class="btn" style="background:var(--primary-red);color:#fff">
                <i class="fas fa-expand"></i> Pantalla Completa
            </button>
        </div>
    </div>

    <!-- PANEL DE OBSERVACIONES -->
    <div class="section-card">
        <div class="section-header">
            <i class="fas fa-comment-alt" style="color:var(--warning)"></i>
            <h3>Observaciones</h3>
        </div>
        <div class="section-body">
            <!-- FORMULARIO -->
            <form action="{{ route('tutor.observacion.store') }}" method="POST" class="mb-4">
                @csrf
                <input type="hidden" name="id_documento" value="{{ $documento->id ?? '' }}">
                <label class="form-label" style="color:var(--text-secondary)">Agregar Nueva Observación</label>
                <textarea name="contenido" rows="3" class="form-control" 
                          style="background:var(--bg-input);border-color:var(--border-color);color:var(--text-primary)" 
                          placeholder="Escribe aquí la observación..." required></textarea>
                <label class="form-label mt-3" style="color:var(--text-secondary)">Sección del Documento</label>
                <select name="seccion" class="form-select" 
                        style="background:var(--bg-input);border-color:var(--border-color);color:var(--text-primary)">
                    <option value="">General</option>
                    <option value="Introducción">Introducción</option>
                    <option value="Objetivos">Objetivos</option>
                    <option value="Marco Teórico">Marco Teórico</option>
                    <option value="Metodología">Metodología</option>
                    <option value="Desarrollo">Desarrollo</option>
                    <option value="Conclusiones">Conclusiones</option>
                    <option value="Bibliografía">Bibliografía</option>
                    <option value="Anexos">Anexos</option>
                </select>
                <button type="submit" class="btn w-100 mt-3" style="background:var(--primary-red);color:#fff">
                    <i class="fas fa-plus"></i> Agregar Observación
                </button>
            </form>

            <!-- LISTA DE OBSERVACIONES -->
            <div style="border-top:1px solid var(--border-color);padding-top:16px">
                <h5 style="color:var(--text-secondary);margin-bottom:12px">Observaciones Existentes</h5>
                <div id="lista-observaciones" style="max-height:200px;overflow-y:auto">
                    @forelse($documento->observaciones ?? [] as $obs)
                    <div style="background:rgba(242,201,76,0.1);border:1px solid rgba(242,201,76,0.3);border-radius:8px;padding:12px;margin-bottom:8px">
                        <div style="display:flex;justify-content:space-between;margin-bottom:8px">
                            <span style="font-size:.75rem;font-weight:600;background:rgba(242,201,76,0.2);color:var(--warning);padding:2px 8px;border-radius:999px">
                                {{ $obs->seccion_documento ?? 'General' }}
                            </span>
                            <small style="color:var(--text-muted)">{{ $obs->created_at->diffForHumans() }}</small>
                        </div>
                        <p style="margin:0;color:var(--text-secondary);font-size:.9rem">{{ $obs->comentario }}</p>
                        @if(!$obs->resuelta)
                        <div style="margin-top:8px">
                            <button class="btn btn-sm" style="color:var(--success);padding:0" onclick="marcarCorregida({{ $obs->id }})">
                                <i class="fas fa-check"></i> Corregida
                            </button>
                            <button class="btn btn-sm" style="color:var(--danger);padding:0" onclick="eliminarObservacion({{ $obs->id }})">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                        @endif
                    </div>
                    @empty
                    <p style="text-align:center;color:var(--text-muted);padding:20px 0">Sin observaciones aún</p>
                    @endforelse
                </div>
            </div>

            <!-- CHECKLIST -->
            <div style="border-top:1px solid var(--border-color);padding-top:16px;margin-top:16px">
                <h5 style="color:var(--text-secondary);margin-bottom:12px">
                    <i class="fas fa-clipboard-check"></i> Checklist
                </h5>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox">
                    <label class="form-check-label" style="color:var(--text-secondary)">Ortografía y redacción</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox">
                    <label class="form-check-label" style="color:var(--text-secondary)">Formato APA</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox">
                    <label class="form-check-label" style="color:var(--text-secondary)">Coherencia de objetivos</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox">
                    <label class="form-check-label" style="color:var(--text-secondary)">Metodología clara</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox">
                    <label class="form-check-label" style="color:var(--text-secondary)">Bibliografía actualizada</label>
                </div>
            </div>

            <!-- BOTONES PRINCIPALES -->
            <div style="margin-top:24px">
                @if(($documento->estado->nombre ?? '') === 'entregado')
                <form action="{{ route('tutor.documento.aprobar', $documento->id) }}" method="POST" 
                      onsubmit="return confirm('¿Aprobar este documento? Pasará a revisión de tribunal.')">
                    @csrf
                    <button type="submit" class="btn w-100 mb-2" style="background:var(--success);color:#fff">
                        <i class="fas fa-check-circle"></i> Aprobar Documento
                    </button>
                </form>
                <form action="{{ route('tutor.documento.corregir', $documento->id) }}" method="POST">
                    @csrf
                    <textarea name="observaciones" rows="2" class="form-control mb-2" 
                              style="background:var(--bg-input);border-color:var(--border-color);color:var(--text-primary)" 
                              placeholder="Comentario adicional para el estudiante (opcional)"></textarea>
                    <button type="submit" class="btn w-100" style="background:var(--warning);color:#000">
                        <i class="fas fa-undo"></i> Solicitar Correcciones
                    </button>
                </form>
                @else
                <div style="text-align:center;padding:12px;color:var(--text-muted)">
                    @if(($documento->estado->nombre ?? '') === 'visto_bueno')
                        <i class="fas fa-check-circle" style="color:var(--success)"></i> Documento aprobado por tutor
                    @elseif(($documento->estado->nombre ?? '') === 'con_observaciones')
                        <i class="fas fa-clock" style="color:var(--warning)"></i> Esperando correcciones del estudiante
                    @endif
                </div>
                @endif
                <a href="{{ route('tutor.documentos') }}" class="btn w-100 mt-2" 
                   style="background:var(--bg-input);color:var(--text-primary)">
                    <i class="fas fa-arrow-left"></i> Volver a Documentos
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleFullscreen(){
    const c=document.getElementById('visor-container');
    if(!document.fullscreenElement){
        c.requestFullscreen().catch(e=>console.log(e));
    }else{
        document.exitFullscreen();
    }
}
function marcarCorregida(id){
    if(!confirm('¿Marcar esta observación como corregida?'))return;
    fetch(`/tutor/observacion/${id}/corregida`,{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
        body:JSON.stringify({resuelta:true})
    }).then(r=>r.json()).then(d=>{if(d.success)location.reload()}).catch(e=>console.error(e));
}
function eliminarObservacion(id){
    if(!confirm('¿Eliminar esta observación?'))return;
    fetch(`/tutor/observacion/${id}`,{
        method:'DELETE',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}
    }).then(r=>r.json()).then(d=>{if(d.success)location.reload()}).catch(e=>console.error(e));
}
</script>
@endpush

@push('styles')
<style>
.pagination{display:flex;gap:.25rem}.pagination li{display:inline}.pagination span,.pagination a{display:inline-flex;align-items:center;justify-content:center;min-width:2rem;height:2rem;padding:0 .5rem;border:1px solid var(--border-color);border-radius:.375rem;background-color:var(--bg-input);color:var(--text-primary);font-size:.875rem;text-decoration:none;transition:all .2s}.pagination span.active,.pagination a:hover{background-color:var(--primary-red);border-color:var(--primary-red);color:#fff}.pagination span.disabled{opacity:.5;cursor:not-allowed}
</style>
@endpush