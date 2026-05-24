@extends('tutor.layout')

@section('title', 'Revisar Documento - Tutor')
@section('page-title', 'Revisión de Documento')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- INFORMACIÓN DEL DOCUMENTO -->
    <div class="lg:col-span-2 card-dark rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white">{{ $documento->archivo_nombre_original ?? 'Documento' }}</h3>
            <p class="text-sm text-gray-400 mt-1">
                Estudiante: {{ $documento->estudiante->nombres ?? 'N/A' }} {{ $documento->estudiante->apellidos ?? '' }} | 
                Versión {{ $documento->version ?? 1 }}.0
            </p>
        </div>
        
        <div class="p-6 bg-black bg-opacity-30">
            <!-- Solo botón de descarga, sin vista previa -->
            @if($documento->archivo_ruta)
            <div class="bg-white bg-opacity-5 rounded-lg p-8 text-center">
                <i class="fas fa-file-pdf text-6xl text-red-400 mb-4"></i>
                <p class="text-gray-300 mb-4">Documento PDF listo para descargar</p>
                <a href="{{ asset('storage/' . $documento->archivo_ruta) }}" 
                   target="_blank"
                   class="inline-flex items-center bg-red-700 hover:bg-red-600 text-white px-6 py-3 rounded-lg transition">
                    <i class="fas fa-download mr-2"></i>Descargar Documento
                </a>
                <p class="text-xs text-gray-500 mt-4">
                    <i class="fas fa-info-circle mr-1"></i>
                    El archivo se abrirá en una nueva pestaña
                </p>
            </div>
            @else
            <div class="bg-white bg-opacity-5 rounded-lg p-8 text-center text-gray-500">
                <i class="fas fa-file-alt text-6xl mb-4"></i>
                <p>Documento no disponible</p>
            </div>
            @endif
        </div>
    </div>

    <!-- PANEL DE OBSERVACIONES (Se mantiene igual) -->
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
                        <!--DE TODAS LAS VERSIONES -->
            <div class="border-t border-gray-700 pt-4">
                <h4 class="text-sm font-semibold text-gray-300 mb-3">
                    <i class="fas fa-history mr-2"></i>
                    Observaciones de todas las versiones
                </h4>
                
                @if(isset($todasLasObservaciones) && $todasLasObservaciones->isNotEmpty())
                    <div id="lista-observaciones" class="space-y-3 max-h-64 overflow-y-auto">
                        @foreach($todasLasObservaciones as $obs)
                        <div class="bg-yellow-900 bg-opacity-30 border border-yellow-700 rounded-lg p-3">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-semibold text-yellow-300 bg-yellow-900 bg-opacity-50 px-2 py-1 rounded">
                                        {{ $obs->seccion_documento ?? 'General' }}
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        v{{ $obs->documento->version ?? '?' }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-400">{{ $obs->created_at?->diffForHumans() ?? '' }}</span>
                            </div>
                            <p class="text-sm text-gray-300">{{ $obs->comentario }}</p>
                            <div class="mt-2 flex space-x-2">
                                @if(!$obs->resuelta)
                                <button class="text-xs text-green-400 hover:text-green-300 font-medium" 
                                        onclick="marcarCorregida({{ $obs->id }})">
                                    <i class="fas fa-check mr-1"></i>Corregida
                                </button>
                                @else
                                <span class="text-xs text-green-400">
                                    <i class="fas fa-check-circle mr-1"></i>Corregida
                                </span>
                                @endif
                                <button class="text-xs text-red-400 hover:text-red-300 font-medium" 
                                        onclick="eliminarObservacion({{ $obs->id }})">
                                    <i class="fas fa-trash mr-1"></i>Eliminar
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400 text-center py-4 text-sm">Sin observaciones aún</p>
                @endif
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
                
                <a href="{{ route('tutor.tareas') }}" 
                    class="block w-full text-center bg-black bg-opacity-50 text-gray-300 px-4 py-2 rounded-lg hover:bg-opacity-70 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Volver a Tareas
                </a>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
// Función para mostrar notificaciones toast
function showToast(message, type = 'success') {
    // Crear elemento toast
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
    
    // Colores según el tipo
    if (type === 'success') {
        toast.className += ' bg-green-900 bg-opacity-90 border border-green-600 text-green-100';
    } else if (type === 'error') {
        toast.className += ' bg-red-900 bg-opacity-90 border border-red-600 text-red-100';
    } else if (type === 'warning') {
        toast.className += ' bg-yellow-900 bg-opacity-90 border border-yellow-600 text-yellow-100';
    }
    
    // Icono según el tipo
    let icon = '';
    if (type === 'success') {
        icon = '<i class="fas fa-check-circle mr-2"></i>';
    } else if (type === 'error') {
        icon = '<i class="fas fa-exclamation-circle mr-2"></i>';
    } else if (type === 'warning') {
        icon = '<i class="fas fa-exclamation-triangle mr-2"></i>';
    }
    
    toast.innerHTML = `${icon}${message}`;
    
    // Agregar al body
    document.body.appendChild(toast);
    
    // Animar entrada
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Eliminar después de 5 segundos
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 5000);
}

// Agregar observación con AJAX
document.querySelector('form[action="{{ route('tutor.observacion.store') }}"]')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const textarea = form.querySelector('textarea[name="contenido"]');
    
    // Deshabilitar botón durante la petición
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Guardando...';
    
    fetch('{{ route('tutor.observacion.store') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            'Accept': 'application/json',
        },
        body: formData,
    })
    .then(response => {
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf('application/json') !== -1) {
            return response.json();
        } else {
            throw new Error('La respuesta no es JSON');
        }
    })
    .then(data => {
        if (data.success) {
            // Agregar observación a la lista
            const lista = document.getElementById('lista-observaciones');
            const emptyMsg = lista?.querySelector('.text-center');
            if (emptyMsg) emptyMsg.remove();
            
            const nuevaObs = document.createElement('div');
            nuevaObs.className = 'bg-yellow-900 bg-opacity-30 border border-yellow-700 rounded-lg p-3 mb-3';
            nuevaObs.innerHTML = `
                <div class="flex justify-between items-start mb-2">
                    <span class="text-xs font-semibold text-yellow-300 bg-yellow-900 bg-opacity-50 px-2 py-1 rounded">
                        ${formData.get('seccion') || 'General'}
                    </span>
                    <span class="text-xs text-gray-400">${data.observacion.created_at}</span>
                </div>
                <p class="text-sm text-gray-300">${data.observacion.comentario}</p>
                <div class="mt-2 flex space-x-2">
                    <button class="text-xs text-green-400 hover:text-green-300 font-medium" onclick="marcarCorregida(${data.observacion.id})">
                        <i class="fas fa-check mr-1"></i>Marcar como corregida
                    </button>
                    <button class="text-xs text-red-400 hover:text-red-300 font-medium" onclick="eliminarObservacion(${data.observacion.id})">
                        <i class="fas fa-trash mr-1"></i>Eliminar
                    </button>
                </div>
            `;
            
            if (lista) {
                lista.insertBefore(nuevaObs, lista.firstChild);
            }
            
            // Limpiar formulario
            if (textarea) textarea.value = '';
            form.querySelector('select[name="seccion"]')?.querySelectorAll('option').forEach(opt => opt.selected = false);
            
            // ✅ Mostrar notificación toast en lugar de alert
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Error al guardar la observación', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error al guardar la observación', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-plus mr-2"></i>Agregar Observación';
    });
});

// Marcar observación como corregida
function marcarCorregida(obsId) {
    if (!confirm('¿Marcar esta observación como corregida?')) return;
    
    fetch(`/tutor/observacion/${obsId}/corregida`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar notificación toast en lugar de alert
            showToast(data.message, 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showToast(data.message || 'Error al marcar', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error al marcar la observación', 'error');
    });
}

// Eliminar observación
function eliminarObservacion(obsId) {
    if (!confirm('¿Eliminar esta observación?')) return;
    
    fetch(`/tutor/observacion/${obsId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar notificación toast en lugar de alert
            showToast(data.message, 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showToast(data.message || 'Error al eliminar', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error al eliminar la observación', 'error');
    });
}
</script>
@endpush
@endsection