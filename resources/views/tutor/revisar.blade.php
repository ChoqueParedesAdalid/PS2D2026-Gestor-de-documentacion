@extends('tutor.layout')

@section('title', 'Revisar Documento - Tutor')
@section('page-title', 'Revisión de Documento')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- VISOR DE DOCUMENTO -->
    <div class="lg:col-span-2 card-dark rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h3 class="text-lg font-semibold text-white">{{ $documento->archivo_nombre_original ?? 'Documento' }}</h3>
                <p class="text-sm text-gray-400">
                    Estudiante: {{ $documento->estudiante->nombres ?? 'N/A' }} {{ $documento->estudiante->apellidos ?? '' }} | 
                    Versión {{ $documento->version ?? 1 }}.0
                </p>
            </div>
            <div class="flex space-x-2">
                @if($documento->archivo_ruta ?? false)
                <a href="{{ asset($documento->archivo_ruta) }}" target="_blank" 
                   class="bg-black bg-opacity-50 text-white px-3 py-2 rounded-lg hover:bg-opacity-70 transition">
                    <i class="fas fa-download mr-2"></i>Descargar
                </a>
                @endif
                <button onclick="toggleFullscreen()" class="bg-red-700 text-white px-3 py-2 rounded-lg hover:bg-red-600 transition">
                    <i class="fas fa-expand mr-2"></i>Pantalla Completa
                </button>
            </div>
        </div>
        
        <div class="p-6 bg-black bg-opacity-30 h-[600px] overflow-auto" id="visor-container">
            @if($documento->archivo_ruta ?? false)
                <!-- Si es PDF, mostrar embed; si no, mostrar mensaje -->
                @if(pathinfo($documento->archivo_ruta, PATHINFO_EXTENSION) === 'pdf')
                <embed src="{{ asset($documento->archivo_ruta) }}" type="application/pdf" 
                       class="w-full h-full min-h-[800px] bg-white" />
                @else
                <div class="bg-white shadow-lg p-8 max-w-3xl mx-auto min-h-[800px] text-center">
                    <i class="fas fa-file text-6xl text-gray-400 mb-4"></i>
                    <p class="text-gray-700">Vista previa no disponible para este formato</p>
                    <a href="{{ asset($documento->archivo_ruta) }}" target="_blank" 
                       class="text-red-600 hover:underline mt-2 inline-block">
                        Abrir documento en nueva pestaña →
                    </a>
                </div>
                @endif
            @else
            <div class="bg-white shadow-lg p-8 max-w-3xl mx-auto min-h-[800px] text-center text-gray-500">
                <i class="fas fa-file-alt text-6xl mb-4"></i>
                <p>Documento no disponible</p>
            </div>
            @endif
        </div>
    </div>

    <!-- PANEL DE OBSERVACIONES -->
    <div class="card-dark rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white">
                <i class="fas fa-comment-alt mr-2"></i>Observaciones
            </h3>
        </div>
        
        <div class="p-6">
            <!-- FORMULARIO DE OBSERVACIÓN -->
            <form action="{{ route('tutor.observacion.store') }}" method="POST" class="mb-6">
                @csrf
                <input type="hidden" name="id_documento" value="{{ $documento->id ?? '' }}">
                
                <label class="block text-sm font-medium text-gray-300 mb-2">
                    Agregar Nueva Observación
                </label>
                <textarea name="contenido" rows="4" 
                          class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500"
                          placeholder="Escribe aquí la observación..." required></textarea>
                
                <div class="mt-3">
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Sección del Documento
                    </label>
                    <select name="seccion" class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
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
                </div>
                
                <button type="submit" class="mt-3 w-full bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                    <i class="fas fa-plus mr-2"></i>Agregar Observación
                </button>
            </form>

            <!-- LISTA DE OBSERVACIONES -->
            <div class="border-t border-gray-700 pt-4">
                <h4 class="text-sm font-semibold text-gray-300 mb-3">Observaciones Existentes</h4>
                
                <div id="lista-observaciones" class="space-y-3 max-h-64 overflow-y-auto">
                    @forelse($documento->observaciones ?? [] as $obs)
                    <div class="bg-yellow-900 bg-opacity-30 border border-yellow-700 rounded-lg p-3">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-semibold text-yellow-300 bg-yellow-900 bg-opacity-50 px-2 py-1 rounded">
                                {{ $obs->seccion_documento ?? 'General' }}
                            </span>
                            <span class="text-xs text-gray-400">{{ $obs->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-300">{{ $obs->comentario }}</p>
                        <div class="mt-2 flex space-x-2">
                            @if(!$obs->resuelta)
                            <button class="text-xs text-green-400 hover:text-green-300 font-medium" 
                                    onclick="marcarCorregida({{ $obs->id }})">
                                <i class="fas fa-check mr-1"></i>Corregida
                            </button>
                            @endif
                            <button class="text-xs text-red-400 hover:text-red-300 font-medium" 
                                    onclick="eliminarObservacion({{ $obs->id }})">
                                <i class="fas fa-trash mr-1"></i>Eliminar
                            </button>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-400 text-center py-4 text-sm">Sin observaciones aún</p>
                    @endforelse
                </div>
            </div>

            <!-- CHECKLIST DE CORRECCIONES -->
            <div class="border-t border-gray-700 pt-4 mt-4">
                <h4 class="text-sm font-semibold text-gray-300 mb-3">
                    <i class="fas fa-clipboard-check mr-2"></i>Checklist
                </h4>
                
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" class="h-4 w-4 text-red-600 rounded focus:ring-red-500 bg-black bg-opacity-50 border-gray-600">
                        <span class="ml-2 text-sm text-gray-300">Ortografía y redacción</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" class="h-4 w-4 text-red-600 rounded focus:ring-red-500 bg-black bg-opacity-50 border-gray-600">
                        <span class="ml-2 text-sm text-gray-300">Formato APA</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" class="h-4 w-4 text-red-600 rounded focus:ring-red-500 bg-black bg-opacity-50 border-gray-600">
                        <span class="ml-2 text-sm text-gray-300">Coherencia de objetivos</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" class="h-4 w-4 text-red-600 rounded focus:ring-red-500 bg-black bg-opacity-50 border-gray-600">
                        <span class="ml-2 text-sm text-gray-300">Metodología clara</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" class="h-4 w-4 text-red-600 rounded focus:ring-red-500 bg-black bg-opacity-50 border-gray-600">
                        <span class="ml-2 text-sm text-gray-300">Bibliografía actualizada</span>
                    </label>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN PRINCIPAL -->
            <div class="mt-6 space-y-2">
                @if(($documento->estado->nombre ?? '') === 'entregado')
                <form action="{{ route('tutor.documento.aprobar', $documento->id) }}" method="POST" onsubmit="return confirm('¿Aprobar este documento? Pasará a revisión de tribunal.')">
                    @csrf
                    <button type="submit" class="w-full bg-green-700 bg-opacity-70 text-white px-4 py-3 rounded-lg hover:bg-opacity-90 transition font-medium">
                        <i class="fas fa-check-circle mr-2"></i>Aprobar Documento
                    </button>
                </form>
                
                <form action="{{ route('tutor.documento.corregir', $documento->id) }}" method="POST">
                    @csrf
                    <textarea name="observaciones" rows="2" 
                              class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 mb-2"
                              placeholder="Comentario adicional para el estudiante (opcional)"></textarea>
                    <button type="submit" class="w-full bg-yellow-700 bg-opacity-70 text-white px-4 py-3 rounded-lg hover:bg-opacity-90 transition font-medium">
                        <i class="fas fa-undo mr-2"></i>Solicitar Correcciones
                    </button>
                </form>
                @else
                <div class="text-center py-3 text-gray-400 text-sm">
                    @if(($documento->estado->nombre ?? '') === 'visto_bueno')
                        <i class="fas fa-check-circle text-green-400 mr-2"></i>Documento aprobado por tutor
                    @elseif(($documento->estado->nombre ?? '') === 'con_observaciones')
                        <i class="fas fa-clock text-yellow-400 mr-2"></i>Esperando correcciones del estudiante
                    @endif
                </div>
                @endif
                
                <a href="{{ route('tutor.documentos') }}" 
                   class="block w-full text-center bg-black bg-opacity-50 text-gray-300 px-4 py-2 rounded-lg hover:bg-opacity-70 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Volver a Documentos
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Pantalla completa para el visor
function toggleFullscreen() {
    const container = document.getElementById('visor-container');
    if (!document.fullscreenElement) {
        container.requestFullscreen().catch(err => {
            console.log(`Error: ${err.message}`);
        });
    } else {
        document.exitFullscreen();
    }
}

// Marcar observación como corregida (AJAX)
function marcarCorregida(obsId) {
    if (!confirm('¿Marcar esta observación como corregida?')) return;
    
    fetch(`/tutor/observacion/${obsId}/corregida`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ resuelta: true })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

// Eliminar observación (AJAX)
function eliminarObservacion(obsId) {
    if (!confirm('¿Eliminar esta observación?')) return;
    
    fetch(`/tutor/observacion/${obsId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endpush
@endsection