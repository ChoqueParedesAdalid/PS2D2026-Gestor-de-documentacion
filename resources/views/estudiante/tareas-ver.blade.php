@extends('estudiante.layout')

@section('title', 'Detalle de Tarea - Estudiante')
@section('page-title', 'DETALLE DE TAREA')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Header -->
    <div class="flex justify-between items-center">
        <a href="{{ route('estudiante.tareas') }}" 
           class="text-red-400 hover:text-red-300">
            <i class="fas fa-arrow-left mr-2"></i>Volver a tareas
        </a>
        <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-400">
                <i class="fas fa-clock mr-1"></i>
                Vence: {{ $tarea->fecha_limite->format('d/m/Y H:i') }}
            </span>
        </div>
    </div>

    <!-- Información de la tarea -->
    <div class="card-dark rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-white mb-2">{{ $tarea->titulo }}</h2>
        <p class="text-gray-400 mb-6">{{ $tarea->descripcion }}</p>
        
        <div class="grid grid-cols-3 gap-4 pt-6 border-t border-gray-700">
            <div>
                <span class="text-sm text-gray-400">Materia</span>
                <p class="text-white font-semibold">{{ $tarea->materia->nombre ?? 'N/A' }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-400">Tipo de documento</span>
                <p class="text-white font-semibold">{{ ucfirst(str_replace('_', ' ', $tarea->tipo_documento)) }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-400">Estado</span>
                <p class="text-white font-semibold">
                    @if($ultimoDocumento)
                        <span class="text-green-400">
                            <i class="fas fa-check-circle mr-1"></i>Entregado (v{{ $ultimoDocumento->version }})
                        </span>
                    @elseif($tarea->fecha_limite < now())
                        <span class="text-red-400">
                            <i class="fas fa-exclamation-circle mr-1"></i>Vencida
                        </span>
                    @else
                        <span class="text-yellow-400">
                            <i class="fas fa-clock mr-1"></i>Pendiente
                        </span>
                    @endif
                </p>
            </div>
        </div>
    </div>

        <!-- SECCIÓN DE ENTREGA -->
    <div class="card-dark rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-white mb-4">
            <i class="fas fa-upload mr-2"></i>Entrega de Tarea
        </h3>
        
        @if($ultimoDocumento)
            <!-- Documento ya entregado -->
            <div class="bg-green-900 bg-opacity-20 border border-green-700 rounded-lg p-6 mb-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="bg-red-900 bg-opacity-50 rounded-lg p-4">
                            <i class="fas fa-file-pdf text-red-400 text-3xl"></i>
                        </div>
                        <div>
                            <h4 class="text-white font-semibold">{{ $ultimoDocumento->archivo_nombre_original }}</h4>
                            <p class="text-gray-400 text-sm">
                                Versión {{ $ultimoDocumento->version }} • 
                                Entregado el {{ $ultimoDocumento->entregado_en->format('d/m/Y H:i') }}
                            </p>
                            @if($ultimoDocumento->entregado_en > $tarea->fecha_limite)
                                <p class="text-yellow-400 text-xs mt-1">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>Entregado con retraso
                                </p>
                            @endif
                            <p class="text-gray-500 text-xs">
                                {{ number_format($ultimoDocumento->archivo_tamaño / 1024 / 1024, 2) }} MB
                            </p>
                        </div>
                    </div>
                    <a href="{{ asset('storage/' . $ultimoDocumento->archivo_ruta) }}" 
                       target="_blank"
                       class="bg-red-700 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                        <i class="fas fa-download mr-2"></i>Descargar
                    </a>
                </div>
            </div>

            <!-- Observaciones del tutor/tribunal -->
            @if($ultimoDocumento->observaciones->isNotEmpty())
                <div class="mb-6">
                    <h4 class="text-white font-semibold mb-3">
                        <i class="fas fa-comment-alt mr-2 text-yellow-400"></i>
                        Observaciones ({{ $ultimoDocumento->observaciones->count() }})
                    </h4>
                    <div class="space-y-3">
                        @foreach($ultimoDocumento->observaciones as $obs)
                            <div class="bg-yellow-900 bg-opacity-20 border border-yellow-700 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-yellow-400 font-semibold text-sm">
                                            {{ $obs->revisor->nombres ?? 'Revisor' }}
                                        </span>
                                        <span class="text-xs text-gray-400">({{ ucfirst($obs->rol_revisor) }})</span>
                                    </div>
                                    <span class="text-xs text-gray-400">
                                        {{ $obs->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                <p class="text-gray-300 text-sm mb-2">{{ $obs->comentario }}</p>
                                @if($obs->seccion_documento)
                                    <span class="text-xs text-gray-500">
                                        <i class="fas fa-tag mr-1"></i>{{ $obs->seccion_documento }}
                                    </span>
                                @endif
                                @if($obs->resuelta)
                                    <span class="text-green-400 text-xs mt-2 block">
                                        <i class="fas fa-check mr-1"></i>Corregida
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Botón subir nueva versión -->
            <button onclick="toggleUploadForm()" 
                    class="w-full bg-blue-700 hover:bg-blue-600 text-white px-4 py-3 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>Agregar nueva versión
            </button>
        @else
            <!-- Sin documento entregado -->
            <div class="text-center py-8 mb-4">
                <i class="fas fa-cloud-upload-alt text-6xl text-gray-600 mb-4"></i>
                <p class="text-gray-400 text-lg mb-2">Aún no has entregado esta tarea</p>
                @if($tarea->fecha_limite < now())
                    <p class="text-red-400 text-sm mb-4">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <strong>Fecha límite vencida:</strong> {{ $tarea->fecha_limite->format('d/m/Y H:i') }}
                    </p>
                @else
                    <p class="text-gray-500 text-sm mb-4">
                        Fecha límite: {{ $tarea->fecha_limite->format('d/m/Y H:i') }}
                    </p>
                @endif
            </div>

            <!-- Botón Entregar -->
            <button onclick="toggleUploadForm()" 
                    class="w-full bg-red-700 hover:bg-red-600 text-white px-4 py-3 rounded-lg transition font-semibold">
                <i class="fas fa-upload mr-2"></i>Entregar Tarea
            </button>
        @endif

        <!-- Formulario de upload (oculto por defecto) -->
        <form action="{{ route('estudiante.tareas.subir', $tarea->id) }}" 
              method="POST" 
              enctype="multipart/form-data" 
              id="upload-form"
              class="upload-form-ajax hidden mt-6">
            @csrf
            <div class="upload-zone rounded-lg p-8 text-center cursor-pointer border-2 border-dashed border-gray-600 hover:border-red-500 transition" 
                 onclick="document.getElementById('file-input').click()">
                <i class="fas fa-file-pdf text-5xl text-gray-400 mb-4"></i>
                <p class="text-gray-300 mb-2">Arrastra tu archivo PDF aquí</p>
                <p class="text-gray-500 text-sm">o haz clic para seleccionar</p>
                <p class="text-gray-500 text-xs mt-2">Máximo 50MB • Solo PDF</p>
                <input type="file" 
                       id="file-input" 
                       name="archivo" 
                       accept=".pdf,application/pdf" 
                       class="hidden" 
                       required
                       onchange="validateFile(this)">
            </div>
            <div class="mt-4 flex space-x-3">
                <button type="button" onclick="toggleUploadForm()" 
                        class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                    Cancelar
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-3 bg-red-700 hover:bg-red-600 text-white rounded-lg transition font-semibold">
                    <i class="fas fa-upload mr-2"></i>Entregar Documento
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Toggle formulario de upload
function toggleUploadForm() {
    const form = document.getElementById('upload-form');
    form.classList.toggle('hidden');
}

// Validar archivo antes de subir
function validateFile(input) {
    const file = input.files[0];
    const maxSize = 50 * 1024 * 1024; // 50MB
    
    if (file) {
        if (file.size > maxSize) {
            alert('❌ El archivo excede los 50MB.');
            input.value = '';
            return false;
        }
        
        if (file.type !== 'application/pdf') {
            alert('❌ Solo se permiten archivos PDF.');
            input.value = '';
            return false;
        }
        
        const fileName = file.name.length > 30 ? file.name.substring(0, 30) + '...' : file.name;
        input.parentElement.querySelector('p.text-gray-300').textContent = 'Archivo: ' + fileName;
    }
    
    return true;
}

// Subida con AJAX
document.querySelector('.upload-form-ajax')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const fileInput = document.getElementById('file-input');
    
    if (!fileInput.files[0]) {
        alert('❌ Debes seleccionar un archivo.');
        return;
    }
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Subiendo...';
    
    const xhr = new XMLHttpRequest();
    
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const percent = (e.loaded / e.total) * 100;
            submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>Subiendo ${Math.round(percent)}%`;
        }
    });
    
    xhr.addEventListener('load', function() {
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    alert('✅ ' + response.message);
                    location.reload();
                } else {
                    alert('❌ ' + response.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-upload mr-2"></i>Subir Documento';
                }
            } catch (e) {
                alert('✅ Documento subido. Recargando...');
                location.reload();
            }
        } else {
            alert('❌ Error al subir.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-upload mr-2"></i>Subir Documento';
        }
    });
    
    xhr.open('POST', this.action);
    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
    xhr.send(formData);
});
</script>

<style>
.upload-zone:hover {
    border-color: #dc2626;
    background-color: rgba(220, 38, 38, 0.05);
}
</style>
@endpush
@endsection