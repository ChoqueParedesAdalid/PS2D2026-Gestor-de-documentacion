@extends('tutor.layout')

@section('title', 'Revisar Documento - Tutor')
@section('page-title', 'Revisión de Documento')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- VISOR DE DOCUMENTO -->
    <div class="lg:col-span-2 card-dark rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-white">Capítulo 1 - Introducción.pdf</h3>
                <p class="text-sm text-gray-400">Estudiante: Juan Pérez | Versión 1.0</p>
            </div>
            <div class="flex space-x-2">
                <button class="bg-black bg-opacity-50 text-white px-3 py-2 rounded-lg hover:bg-opacity-70 transition">
                    <i class="fas fa-download mr-2"></i>Descargar
                </button>
                <button class="bg-red-700 text-white px-3 py-2 rounded-lg hover:bg-red-600 transition">
                    <i class="fas fa-expand mr-2"></i>Pantalla Completa
                </button>
            </div>
        </div>
        
        <div class="p-6 bg-black bg-opacity-30 h-[600px] overflow-auto">
            <div class="bg-white shadow-lg p-8 max-w-3xl mx-auto min-h-[800px]">
                <h1 class="text-2xl font-bold mb-4 text-center text-gray-900">CAPÍTULO 1</h1>
                <h2 class="text-xl font-semibold mb-6 text-center text-gray-900">INTRODUCCIÓN</h2>
                
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p>El presente proyecto tiene como objetivo desarrollar un sistema de gestión que permita optimizar los procesos administrativos de la organización...</p>
                    
                    <p>En la actualidad, las empresas enfrentan grandes desafíos en la gestión de información, lo que genera ineficiencias en los procesos operativos...</p>
                    
                    <p>Este sistema permitirá automatizar tareas repetitivas, reducir tiempos de procesamiento y mejorar la calidad del servicio ofrecido a los clientes...</p>
                    
                    <p class="text-sm text-gray-500 italic mt-8">[Documento de ejemplo - Aquí se visualizaría el PDF completo]</p>
                </div>
            </div>
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
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-300 mb-2">
                    Agregar Nueva Observación
                </label>
                <textarea id="observacion" rows="4" 
                          class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500"
                          placeholder="Escribe aquí la observación..."></textarea>
                
                <div class="mt-3">
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Sección del Documento
                    </label>
                    <select class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option class="bg-gray-800">Introducción</option>
                        <option class="bg-gray-800">Objetivos</option>
                        <option class="bg-gray-800">Marco Teórico</option>
                        <option class="bg-gray-800">Metodología</option>
                        <option class="bg-gray-800">Desarrollo</option>
                        <option class="bg-gray-800">Conclusiones</option>
                        <option class="bg-gray-800">General</option>
                    </select>
                </div>
                
                <button onclick="agregarObservacion()" 
                        class="mt-3 w-full bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                    <i class="fas fa-plus mr-2"></i>Agregar Observación
                </button>
            </div>

            <!-- LISTA DE OBSERVACIONES -->
            <div class="border-t border-gray-700 pt-4">
                <h4 class="text-sm font-semibold text-gray-300 mb-3">Observaciones Existentes</h4>
                
                <div id="lista-observaciones" class="space-y-3">
                    @for($i = 0; $i < 3; $i++)
                    <div class="bg-yellow-900 bg-opacity-30 border border-yellow-700 rounded-lg p-3">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-semibold text-yellow-300 bg-yellow-900 bg-opacity-50 px-2 py-1 rounded">
                                {{ ['Introducción', 'Objetivos', 'Metodología'][$i] }}
                            </span>
                            <span class="text-xs text-gray-400">Hace {{ $i + 1 }}h</span>
                        </div>
                        <p class="text-sm text-gray-300">
                            {{ ['Revisar la redacción del párrafo inicial', 'Los objetivos deben ser medibles', 'Especificar mejor la metodología de investigación'][$i] }}
                        </p>
                        <div class="mt-2 flex space-x-2">
                            <button class="text-xs text-green-400 hover:text-green-300 font-medium">
                                <i class="fas fa-check mr-1"></i>Marcar como corregida
                            </button>
                            <button class="text-xs text-red-400 hover:text-red-300 font-medium">
                                <i class="fas fa-trash mr-1"></i>Eliminar
                            </button>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>

            <!-- CHECKLIST DE CORRECCIONES -->
            <!--<div class="border-t border-gray-700 pt-4 mt-4">
                <h4 class="text-sm font-semibold text-gray-300 mb-3">
                    <i class="fas fa-clipboard-check mr-2"></i>Checklist de Correcciones
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
            </div>-->

            <!-- BOTONES DE ACCIÓN -->
            <div class="mt-6 space-y-2">
                <button class="w-full bg-green-700 bg-opacity-70 text-white px-4 py-3 rounded-lg hover:bg-opacity-90 transition font-medium">
                    <i class="fas fa-check-circle mr-2"></i>Aprobar Documento
                </button>
                <button class="w-full bg-yellow-700 bg-opacity-70 text-white px-4 py-3 rounded-lg hover:bg-opacity-90 transition font-medium">
                    <i class="fas fa-undo mr-2"></i>Solicitar Correcciones
                </button>
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
function agregarObservacion() {
    const observacion = document.getElementById('observacion').value;
    if(observacion.trim() === '') {
        alert('Por favor escribe una observación');
        return;
    }
    
    const lista = document.getElementById('lista-observaciones');
    const nuevaObservacion = document.createElement('div');
    nuevaObservacion.className = 'bg-yellow-900 bg-opacity-30 border border-yellow-700 rounded-lg p-3';
    nuevaObservacion.innerHTML = `
        <div class="flex justify-between items-start mb-2">
            <span class="text-xs font-semibold text-yellow-300 bg-yellow-900 bg-opacity-50 px-2 py-1 rounded">
                General
            </span>
            <span class="text-xs text-gray-400">Justo ahora</span>
        </div>
        <p class="text-sm text-gray-300">${observacion}</p>
        <div class="mt-2 flex space-x-2">
            <button class="text-xs text-green-400 hover:text-green-300 font-medium">
                <i class="fas fa-check mr-1"></i>Marcar como corregida
            </button>
            <button class="text-xs text-red-400 hover:text-red-300 font-medium" onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-trash mr-1"></i>Eliminar
            </button>
        </div>
    `;
    
    lista.insertBefore(nuevaObservacion, lista.firstChild);
    document.getElementById('observacion').value = '';
}
</script>
@endpush
@endsection