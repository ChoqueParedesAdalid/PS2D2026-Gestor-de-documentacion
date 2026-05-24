@extends('tribunal.layout')

@section('title', 'Revisar Documento - Tribunal')
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

    <!-- PANEL DE OBSERVACIONES -->
    <div class="card-dark rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white">
                <i class="fas fa-comment-alt mr-2"></i>Observaciones
            </h3>
        </div>
        
        <div class="p-6">
            <!-- FORMULARIO DE OBSERVACIÓN -->
            <form action="{{ route('tribunal.observacion.store') }}" method="POST" class="mb-6" id="form-observacion">
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

            <!-- LISTA DE OBSERVACIONES (DE TODAS LAS VERSIONES) -->
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

            <!-- BOTONES DE ACCIÓN PRINCIPAL (100% PROFESIONAL - SIN POPUPS) -->
            <div class="mt-6 space-y-3">
                
                @if(($documento->estado->nombre ?? '') === 'visto_bueno')
                    
                    <!-- Verificar si hay observaciones pendientes del tribunal -->
                    @php
                        $obsPendientesTribunal = $todasLasObservaciones->filter(fn($o) => 
                            $o->rol_revisor === 'tribunal' && !$o->resuelta
                        )->count();
                    @endphp

                    <!-- Botón APROBAR (para capítulo/entrega parcial) - MODAL PERSONALIZADO -->
                    @if($obsPendientesTribunal === 0)
                    <button type="button" onclick="showAprobarModal({{ $documento->id }})"
                            class="w-full bg-green-700 hover:bg-green-600 text-white px-4 py-3 rounded-lg transition font-semibold flex items-center justify-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <div class="text-left">
                            <div>Aprobar esta entrega</div>
                            <div class="text-xs font-normal opacity-80">El estudiante continúa con la siguiente etapa</div>
                        </div>
                    </button>
                    @else
                    <div class="bg-yellow-900/30 border border-yellow-700 rounded-lg p-3 text-yellow-300 text-sm">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Tienes {{ $obsPendientesTribunal }} observación(es) sin marcar como corregida(s). 
                        <br>Resuélvelas antes de aprobar esta entrega.
                    </div>
                    @endif

                    <!-- Botón SOLICITAR CORRECCIONES - AJAX -->
                    <div class="space-y-2">
                        <textarea id="comentario-correccion" rows="2" 
                                  class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 text-sm"
                                  placeholder="Comentario adicional para el estudiante (opcional)..."></textarea>
                        <button type="button" onclick="solicitarCorrecciones({{ $documento->id }})"
                                class="w-full bg-yellow-700 hover:bg-yellow-600 text-white px-4 py-3 rounded-lg transition font-medium flex items-center justify-center">
                            <i class="fas fa-undo mr-2"></i>
                            <div class="text-left">
                                <div>Solicitar Correcciones</div>
                                <div class="text-xs font-normal opacity-80">El estudiante deberá corregir y volver a entregar</div>
                            </div>
                        </button>
                    </div>

                @elseif(($documento->estado->nombre ?? '') === 'aprobado_tribunal')
                    <!-- Estado: Ya aprobado -->
                    <div class="bg-green-900/30 border border-green-700 rounded-lg p-4 text-center">
                        <i class="fas fa-trophy text-3xl text-yellow-400 mb-2"></i>
                        <p class="text-green-300 font-semibold">Documento aprobado por tribunal</p>
                        <p class="text-gray-400 text-sm mt-1">✅ Listo para la siguiente etapa</p>
                    </div>

                @elseif(($documento->estado->nombre ?? '') === 'con_observaciones')
                    <!-- Estado: Esperando correcciones del estudiante -->
                    <div class="bg-yellow-900/30 border border-yellow-700 rounded-lg p-4 text-center">
                        <i class="fas fa-clock text-3xl text-yellow-400 mb-2"></i>
                        <p class="text-yellow-300 font-semibold">Esperando correcciones del estudiante</p>
                        <p class="text-gray-400 text-sm mt-1">El estudiante fue notificado para corregir</p>
                    </div>

                @endif
                
                <!-- Botón VOLVER (al detalle de la tarea, no a la lista de documentos) -->
                <a href="{{ route('tribunal.tareas-ver', $documento->tarea_id) }}" 
                   class="block w-full text-center bg-black bg-opacity-50 hover:bg-opacity-70 text-gray-300 px-4 py-2 rounded-lg transition text-sm">
                    <i class="fas fa-arrow-left mr-2"></i>Volver a detalle de tarea
                </a>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DE CONFIRMACIÓN PERSONALIZADO -->
<div id="modal-confirm" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center">
    <div class="bg-gray-900 rounded-lg p-6 max-w-md w-full mx-4 border border-gray-700 shadow-2xl">
        <div class="flex items-center mb-4">
            <div class="bg-yellow-900/50 rounded-full p-3 mr-4">
                <i class="fas fa-exclamation-triangle text-yellow-400 text-2xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-white">Confirmar aprobación</h3>
                <p class="text-sm text-gray-400">Esta acción no se puede deshacer</p>
            </div>
        </div>
        
        <div class="mb-6">
            <p class="text-gray-300 text-sm mb-3">
                ¿Estás seguro de aprobar esta entrega?
            </p>
            <ul class="text-sm text-gray-400 space-y-2">
                <li class="flex items-start">
                    <i class="fas fa-check text-green-400 mr-2 mt-1"></i>
                    <span>El estudiante podrá continuar con la siguiente etapa del proyecto</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-times text-red-400 mr-2 mt-1"></i>
                    <span>Esta acción no se puede deshacer</span>
                </li>
            </ul>
        </div>
        
        <div class="flex space-x-3">
            <button onclick="hideAprobarModal()" 
                    class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                Cancelar
            </button>
            <button id="btn-confirmar-aprobar" 
                    onclick="aprobarDocumento(currentDocId)"
                    class="flex-1 px-4 py-2 bg-green-700 hover:bg-green-600 text-white rounded-lg transition font-semibold">
                Sí, aprobar
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentDocId = null;

// Función para mostrar notificaciones toast
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
    
    if (type === 'success') {
        toast.className += ' bg-green-900 bg-opacity-90 border border-green-600 text-green-100';
    } else if (type === 'error') {
        toast.className += ' bg-red-900 bg-opacity-90 border border-red-600 text-red-100';
    } else if (type === 'warning') {
        toast.className += ' bg-yellow-900 bg-opacity-90 border border-yellow-600 text-yellow-100';
    }
    
    let icon = '';
    if (type === 'success') { icon = '<i class="fas fa-check-circle mr-2"></i>'; }
    else if (type === 'error') { icon = '<i class="fas fa-exclamation-circle mr-2"></i>'; }
    else if (type === 'warning') { icon = '<i class="fas fa-exclamation-triangle mr-2"></i>'; }
    
    toast.innerHTML = `${icon}${message}`;
    document.body.appendChild(toast);
    
    setTimeout(() => { toast.classList.remove('translate-x-full'); }, 100);
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => { toast.remove(); }, 300);
    }, 5000);
}

// Mostrar modal de aprobación
function showAprobarModal(docId) {
    currentDocId = docId;
    document.getElementById('modal-confirm').classList.remove('hidden');
}

// Ocultar modal de aprobación
function hideAprobarModal() {
    document.getElementById('modal-confirm').classList.add('hidden');
    currentDocId = null;
}

// Aprobar documento (AJAX)
function aprobarDocumento(docId) {
    hideAprobarModal();
    
    // Mostrar loading de procesando en el botón de confirmar (para evitar múltiples clics)
    const btn = document.querySelector('button[onclick="showAprobarModal(' + docId + ')"]');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';
    }
    
    fetch(`/tribunal/documento/${docId}/aprobar`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('✅ ' + data.message, 'success');
            setTimeout(() => { location.reload(); }, 1500);
        } else {
            showToast('❌ ' + (data.message || 'Error al aprobar'), 'error');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i><div class="text-left"><div>Aprobar esta entrega</div><div class="text-xs font-normal opacity-80">El estudiante continúa con la siguiente etapa</div></div>';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('❌ Error de conexión al aprobar', 'error');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i><div class="text-left"><div>Aprobar esta entrega</div><div class="text-xs font-normal opacity-80">El estudiante continúa con la siguiente etapa</div></div>';
        }
    });
}

// Solicitar correcciones (AJAX)
function solicitarCorrecciones(docId) {
    const comentario = document.getElementById('comentario-correccion')?.value || '';
    
    fetch(`/tribunal/documento/${docId}/solicitar-correcciones`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ observaciones: comentario }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('✅ ' + data.message, 'success');
            setTimeout(() => { location.reload(); }, 1500);
        } else {
            showToast('❌ ' + (data.message || 'Error al solicitar correcciones'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('❌ Error de conexión', 'error');
    });
}

// Agregar observación con AJAX
document.getElementById('form-observacion')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const textarea = form.querySelector('textarea[name="contenido"]');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Guardando...';
    
    fetch('{{ route('tribunal.observacion.store') }}', {
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
            
            if (lista) { lista.insertBefore(nuevaObs, lista.firstChild); }
            if (textarea) textarea.value = '';
            form.querySelector('select[name="seccion"]')?.querySelectorAll('option').forEach(opt => opt.selected = false);
            
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
    fetch(`/tribunal/observacion/${obsId}/corregida`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => { location.reload(); }, 1000);
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
    fetch(`/tribunal/observacion/${obsId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => { location.reload(); }, 1000);
        } else {
            showToast(data.message || 'Error al eliminar', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error al eliminar la observación', 'error');
    });
}

// Cerrar modal al hacer clic fuera
document.getElementById('modal-confirm')?.addEventListener('click', function(e) {
    if (e.target === this) {
        hideAprobarModal();
    }
});
</script>
@endpush
@endsection