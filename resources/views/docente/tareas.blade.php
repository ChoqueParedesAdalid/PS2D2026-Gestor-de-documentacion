@extends('docente.layout')
@section('title', 'Tareas - Docente a Cargo')
@section('page-title', 'Gestión de Tareas')

@section('content')
<div class="space-y-6">
    
    <!-- Header con información de la materia -->
    <div class="card-dark rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-xl font-semibold text-white">{{ $materia->nombre }}</h3>
                <p class="text-gray-400 mt-1">{{ $materia->descripcion }}</p>
                <p class="text-sm text-gray-500 mt-2">
                    <i class="fas fa-tasks mr-1"></i> {{ $tareas->count() }} tareas creadas
                </p>
            </div>
            <button onclick="document.getElementById('modalCrearTarea').classList.remove('hidden')" 
                    class="bg-red-700 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>Crear Tarea
            </button>
        </div>
    </div>

    <!-- Tabs de Navegación -->
    <div class="card-dark rounded-lg shadow">
        <div class="border-b border-gray-700">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="switchTareaTab('activas')" 
                        id="tab-activas"
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-red-500 text-red-400">
                    <i class="fas fa-clock mr-2"></i>Activas ({{ $tareas->filter(fn($t) => $t->fecha_limite > now())->count() }})
                </button>
                <button onclick="switchTareaTab('vencidas')" 
                        id="tab-vencidas"
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-300">
                    <i class="fas fa-exclamation-circle mr-2"></i>Vencidas ({{ $tareas->filter(fn($t) => $t->fecha_limite <= now())->count() }})
                </button>
            </nav>
        </div>

        <!-- CONTENIDO: TAREAS ACTIVAS -->
        <div id="content-activas" class="tab-content p-6">
            @php
                $tareasActivas = $tareas->filter(fn($t) => $t->fecha_limite > now());
            @endphp
            
            @if($tareasActivas->count() > 0)
                <div class="grid grid-cols-1 gap-4">
                    @foreach($tareasActivas as $tarea)
                    <div class="bg-black bg-opacity-30 border border-gray-700 rounded-lg p-4 hover:border-red-500 transition">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <h4 class="text-white font-semibold text-lg mb-1">{{ $tarea->titulo }}</h4>
                                <p class="text-gray-400 text-sm">{{ $tarea->descripcion }}</p>
                                <div class="flex items-center gap-4 mt-3 text-sm">
                                    <span class="text-gray-400">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        Fecha límite: {{ $tarea->fecha_limite->format('d/m/Y H:i') }}
                                    </span>
                                    <span class="text-gray-400">
                                        <i class="fas fa-file-upload mr-1"></i>
                                        {{ $tarea->documentos->count() }} entregas
                                    </span>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-900 bg-opacity-50 text-green-300">
                                        <i class="fas fa-check-circle mr-1"></i>Activa
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        @if($tarea->documentos->count() > 0)
                        <div class="mt-3 pt-3 border-t border-gray-700">
                            <p class="text-sm text-gray-400 mb-2">Últimas entregas:</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($tarea->documentos->take(3) as $doc)
                                <span class="text-xs bg-gray-800 text-gray-300 px-2 py-1 rounded">
                                    <i class="fas fa-user mr-1"></i>{{ $doc->estudiante->nombres }} (v{{ $doc->version }})
                                </span>
                                @endforeach
                                @if($tarea->documentos->count() > 3)
                                <span class="text-xs text-gray-500">+{{ $tarea->documentos->count() - 3 }} más</span>
                                @endif
                            </div>
                        </div>
                        @else
                        <p class="text-gray-500 text-sm mt-3">
                            <i class="fas fa-inbox mr-1"></i>Sin entregas aún
                        </p>
                        @endif
                        
                        <div class="mt-4">
                            <a href="{{ route('docente.tareas-ver', $tarea->id) }}" 
                               class="block w-full text-center bg-red-700 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                                <i class="fas fa-eye mr-2"></i>Ver Detalles
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-400">
                    <i class="fas fa-check-circle text-4xl mb-3 text-green-400"></i>
                    <p class="text-lg">No hay tareas activas en este momento</p>
                </div>
            @endif
        </div>

        <!-- CONTENIDO: TAREAS VENCIDAS -->
        <div id="content-vencidas" class="tab-content hidden p-6">
            @php
                $tareasVencidas = $tareas->filter(fn($t) => $t->fecha_limite <= now());
            @endphp
            
            @if($tareasVencidas->count() > 0)
                <div class="grid grid-cols-1 gap-4">
                    @foreach($tareasVencidas as $tarea)
                    <div class="bg-black bg-opacity-30 border border-gray-700 rounded-lg p-4 opacity-75 hover:opacity-100 transition">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <h4 class="text-white font-semibold text-lg mb-1">{{ $tarea->titulo }}</h4>
                                <p class="text-gray-400 text-sm">{{ $tarea->descripcion }}</p>
                                <div class="flex items-center gap-4 mt-3 text-sm">
                                    <span class="text-gray-400">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        Venció: {{ $tarea->fecha_limite->format('d/m/Y H:i') }}
                                    </span>
                                    <span class="text-gray-400">
                                        <i class="fas fa-file-upload mr-1"></i>
                                        {{ $tarea->documentos->count() }} entregas
                                    </span>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-900 bg-opacity-50 text-red-300">
                                        <i class="fas fa-times-circle mr-1"></i>Vencida
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        @if($tarea->documentos->count() > 0)
                        <div class="mt-3 pt-3 border-t border-gray-700">
                            <p class="text-sm text-gray-400 mb-2">Entregas recibidas:</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($tarea->documentos->take(3) as $doc)
                                <span class="text-xs bg-gray-800 text-gray-300 px-2 py-1 rounded">
                                    <i class="fas fa-user mr-1"></i>{{ $doc->estudiante->nombres }} (v{{ $doc->version }})
                                </span>
                                @endforeach
                                @if($tarea->documentos->count() > 3)
                                <span class="text-xs text-gray-500">+{{ $tarea->documentos->count() - 3 }} más</span>
                                @endif
                            </div>
                        </div>
                        @else
                        <p class="text-gray-500 text-sm mt-3">
                            <i class="fas fa-inbox mr-1"></i>Sin entregas recibidas
                        </p>
                        @endif
                        
                        <div class="mt-4">
                            <a href="{{ route('docente.tareas-ver', $tarea->id) }}" 
                               class="block w-full text-center bg-red-700 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                                <i class="fas fa-eye mr-2"></i>Ver Detalles
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-400">
                    <i class="fas fa-check-circle text-4xl mb-3 text-green-400"></i>
                    <p class="text-lg">No hay tareas vencidas</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Crear Tarea -->
<div id="modalCrearTarea" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center">
    <div class="bg-gray-900 rounded-lg p-6 max-w-md w-full mx-4 border border-gray-700">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-white">Crear Nueva Tarea</h3>
            <button onclick="document.getElementById('modalCrearTarea').classList.add('hidden')" class="text-gray-400 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="{{ route('docente.tareas.crear') }}" method="POST">
    @csrf
    <input type="hidden" name="materia_id" value="{{ $materia->id }}">
    
    <div class="space-y-4">
        <!-- Título -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Título</label>
            <input type="text" name="titulo" required 
                   class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500"
                   value="{{ old('titulo') }}">
            @error('titulo') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        <!-- Descripción -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Descripción</label>
            <textarea name="descripcion" rows="3" required
                      class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('descripcion') }}</textarea>
            @error('descripcion') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        <!--  Tipo de Documento (NUEVO) -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Tipo de Documento</label>
            <select name="tipo_documento" required 
                    class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="">Seleccionar tipo...</option>
                <option value="anteproyecto" {{ old('tipo_documento') == 'anteproyecto' ? 'selected' : '' }}>Anteproyecto</option>
                <option value="documento_final" {{ old('tipo_documento') == 'documento_final' ? 'selected' : '' }}>Documento Final</option>
                <option value="anexos" {{ old('tipo_documento') == 'anexos' ? 'selected' : '' }}>Anexos</option>
                <option value="otro" {{ old('tipo_documento') == 'otro' ? 'selected' : '' }}>Otro</option>
            </select>
            @error('tipo_documento') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        <!-- Fecha Límite -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Fecha Límite</label>
            <input type="datetime-local" name="fecha_limite" required 
                   class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500"
                   value="{{ old('fecha_limite') }}">
            @error('fecha_limite') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
    
    <!-- Mostrar errores generales -->
    @if($errors->any())
    <div class="bg-red-900/70 border border-red-600 text-red-100 px-4 py-3 rounded mb-4 text-sm">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    <div class="mt-6 flex space-x-3">
        <button type="button" onclick="document.getElementById('modalCrearTarea').classList.add('hidden')" 
                class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
            Cancelar
        </button>
        <button type="submit" class="flex-1 px-4 py-2 bg-red-700 hover:bg-red-600 text-white rounded-lg transition">
            Crear
        </button>
    </div>
</form>
    </div>
</div>

@push('scripts')
<script>
// Función para cambiar entre tabs
function switchTareaTab(tabName) {
    // Ocultar todos los contenidos
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Resetear estilos de todos los tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-red-500', 'text-red-400');
        button.classList.add('border-transparent', 'text-gray-400');
    });
    
    // Mostrar el contenido seleccionado
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Activar el tab seleccionado
    const activeTab = document.getElementById('tab-' + tabName);
    activeTab.classList.remove('border-transparent', 'text-gray-400');
    activeTab.classList.add('border-red-500', 'text-red-400');
}
</script>
@endpush
@endsection