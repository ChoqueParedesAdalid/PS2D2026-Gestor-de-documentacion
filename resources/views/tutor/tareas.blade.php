@extends('tutor.layout')
@section('title', 'Tareas - Tutor')
@section('page-title', 'Tareas Asignadas')

@section('content')
<div class="space-y-6">
    
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-white">Tareas de Mis Tutorados</h3>
            <p class="text-gray-400 text-sm">{{ $tareas->count() }} tareas disponibles</p>
        </div>
    </div>

    <!-- Tabs de Navegación -->
    <div class="card-dark rounded-lg shadow">
        <div class="border-b border-gray-700">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="switchTareaTab('activas')" 
                        id="tab-activas"
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-green-500 text-green-400">
                    <i class="fas fa-clock mr-2"></i>Tareas Activas ({{ $tareas->filter(fn($t) => $t->fecha_limite > now())->count() }})
                </button>
                <button onclick="switchTareaTab('vencidas')" 
                        id="tab-vencidas"
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-300">
                    <i class="fas fa-exclamation-circle mr-2"></i>Tareas Vencidas ({{ $tareas->filter(fn($t) => $t->fecha_limite <= now())->count() }})
                </button>
            </nav>
        </div>

        <!-- CONTENIDO: TAREAS ACTIVAS -->
        <div id="content-activas" class="tab-content p-6">
            @php
                $tareasActivas = $tareas->filter(fn($t) => $t->fecha_limite > now())->sortBy('fecha_limite');
            @endphp
            
            @if($tareasActivas->count() > 0)
                <div class="space-y-3">
                    @foreach($tareasActivas as $tarea)
                    <a href="{{ route('tutor.tareas-ver', $tarea->id) }}" 
                       class="card-dark rounded-lg shadow border border-gray-700 hover:border-green-500 transition block p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4 flex-1">
                                <!-- Icono -->
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-lg bg-pink-600 flex items-center justify-center text-white text-xs font-bold">
                                        PY
                                    </div>
                                </div>
                                
                                <!-- Información -->
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-white font-semibold text-sm truncate">
                                        {{ $tarea->titulo }}
                                    </h5>
                                    <p class="text-gray-400 text-xs mt-1">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        Vence el {{ $tarea->fecha_limite->format('d/m/Y H:i') }}
                                    </p>
                                    <p class="text-gray-500 text-xs mt-0.5">
                                        {{ $tarea->materia->nombre ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Badge Activa -->
                            <div class="flex-shrink-0 ml-4">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-900 bg-opacity-50 text-green-300">
                                    Activa
                                </span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-check-circle text-6xl text-green-400 mb-4"></i>
                    <p class="text-gray-400 text-lg">No hay tareas activas en este momento</p>
                </div>
            @endif
        </div>

        <!-- CONTENIDO: TAREAS VENCIDAS -->
        <div id="content-vencidas" class="tab-content hidden p-6">
            @php
                $tareasVencidas = $tareas->filter(fn($t) => $t->fecha_limite <= now())->sortByDesc('fecha_limite');
            @endphp
            
            @if($tareasVencidas->count() > 0)
                <div class="space-y-3">
                    @foreach($tareasVencidas as $tarea)
                    <a href="{{ route('tutor.tareas-ver', $tarea->id) }}" 
                       class="card-dark rounded-lg shadow border border-gray-700 hover:border-red-500 transition block p-4 opacity-75">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4 flex-1">
                                <!-- Icono -->
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-lg bg-gray-600 flex items-center justify-center text-white text-xs font-bold">
                                        PY
                                    </div>
                                </div>
                                
                                <!-- Información -->
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-white font-semibold text-sm truncate">
                                        {{ $tarea->titulo }}
                                    </h5>
                                    <p class="text-red-400 text-xs mt-1">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Venció el {{ $tarea->fecha_limite->format('d/m/Y H:i') }}
                                    </p>
                                    <p class="text-gray-500 text-xs mt-0.5">
                                        {{ $tarea->materia->nombre ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Badge Vencida -->
                            <div class="flex-shrink-0 ml-4">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-900 bg-opacity-50 text-red-300">
                                    Vencida
                                </span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-check-circle text-6xl text-green-400 mb-4"></i>
                    <p class="text-gray-400 text-lg">No hay tareas vencidas</p>
                </div>
            @endif
        </div>
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
        button.classList.remove('border-green-500', 'text-green-400');
        button.classList.add('border-transparent', 'text-gray-400');
    });
    
    // Mostrar el contenido seleccionado
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Activar el tab seleccionado
    const activeTab = document.getElementById('tab-' + tabName);
    activeTab.classList.remove('border-transparent', 'text-gray-400');
    activeTab.classList.add('border-green-500', 'text-green-400');
}
</script>
@endpush
@endsection