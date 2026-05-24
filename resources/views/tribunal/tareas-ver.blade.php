@extends('tribunal.layout')
@section('title', 'Detalle de Tarea - Tribunal')
@section('page-title', 'Detalle de Tarea')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    @if(session('success'))
        <div class="bg-green-900 bg-opacity-70 border border-green-600 text-green-100 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Header -->
    <div class="flex justify-between items-center">
        <a href="{{ route('tribunal.tareas') }}" 
           class="text-red-400 hover:text-red-300">
            <i class="fas fa-arrow-left mr-2"></i>Volver a tareas
        </a>
    </div>

    <!-- Información de la tarea -->
    <div class="card-dark rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-white mb-2">{{ $tarea->titulo }}</h2>
        <p class="text-gray-400 mb-4">{{ $tarea->descripcion }}</p>
        
        <div class="grid grid-cols-3 gap-4">
            <div>
                <span class="text-sm text-gray-400">Fecha límite</span>
                <p class="text-white font-semibold {{ $tarea->fecha_limite < now() ? 'text-red-400' : '' }}">
                    {{ $tarea->fecha_limite->format('d/m/Y H:i') }}
                </p>
            </div>
            <div>
                <span class="text-sm text-gray-400">Tipo de documento</span>
                <p class="text-white font-semibold">{{ ucfirst(str_replace('_', ' ', $tarea->tipo_documento)) }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-400">Progreso</span>
                <p class="text-white font-semibold">
                    {{ $entregados->count() }} / {{ $estudiantesMateria->count() }} estudiantes
                </p>
            </div>
        </div>
    </div>

    <!-- TABS DE NAVEGACIÓN -->
    <div class="card-dark rounded-lg shadow">
        <div class="border-b border-gray-700">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="switchTab('por-entregar')" 
                        id="tab-por-entregar"
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-red-500 text-red-400">
                    Por entregar ({{ $pendientes->count() }})
                </button>
                <button onclick="switchTab('entregado')" 
                        id="tab-entregado"
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-300">
                    Entregado ({{ $entregados->count() }})
                </button>
                <button onclick="switchTab('revisados')" 
                        id="tab-revisados"
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-300">
                    Revisados ({{ $revisados->count() }})
                </button>
            </nav>
        </div>

        <!-- CONTENIDO DE LAS TABS -->
        <div class="p-6">
            
            <!-- TAB: POR ENTREGAR (PENDIENTES) -->
            <div id="content-por-entregar" class="tab-content">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-clock text-yellow-400 mr-2"></i>
                        Pendientes de entrega
                    </h3>
                    <input type="text" 
                           id="search-pendientes"
                           onkeyup="filterTable('table-pendientes', 'search-pendientes')"
                           placeholder="Buscar alumnos..." 
                           class="px-4 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                
                @if($pendientes->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full" id="table-pendientes">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-300 uppercase border-b border-gray-700">
                                    <th class="pb-3">Nombre</th>
                                    <th class="pb-3">Correo</th>
                                    <th class="pb-3">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($pendientes as $estudiante)
                                <tr class="hover:bg-white hover:bg-opacity-5">
                                    <td class="py-3">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-gray-600 flex items-center justify-center text-white text-xs font-semibold">
                                                {{ substr($estudiante->nombres, 0, 1) }}
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-white font-medium">{{ $estudiante->nombres }} {{ $estudiante->apellidos }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 text-gray-400">{{ $estudiante->email_institucional }}</td>
                                    <td class="py-3">
                                        <span class="text-yellow-400 text-sm">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            Sin entregar
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-400 text-center py-8">
                        <i class="fas fa-check-circle text-green-400 text-3xl mb-2"></i>
                        <br>Todos los estudiantes han entregado
                    </p>
                @endif
            </div>

            <!-- TAB: ENTREGADO (Para revisión del tribunal) -->
            <div id="content-entregado" class="tab-content hidden">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-check-circle text-green-400 mr-2"></i>
                        Entregados para revisión
                    </h3>
                    <input type="text" 
                           id="search-entregados"
                           onkeyup="filterTable('table-entregados', 'search-entregados')"
                           placeholder="Buscar alumnos..." 
                           class="px-4 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                
                @if($entregados->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full" id="table-entregados">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-300 uppercase border-b border-gray-700">
                                    <th class="pb-3">Nombre</th>
                                    <th class="pb-3">Versión</th>
                                    <th class="pb-3">Fecha de entrega</th>
                                    <th class="pb-3">Archivo</th>
                                    <th class="pb-3">Estado</th>
                                    <th class="pb-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($entregados as $doc)
                                <tr class="hover:bg-white hover:bg-opacity-5">
                                    <td class="py-3">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-red-700 flex items-center justify-center text-white text-xs font-semibold">
                                                {{ substr($doc->estudiante->nombres, 0, 1) }}
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-white font-medium">{{ $doc->estudiante->nombres }} {{ $doc->estudiante->apellidos }}</p>
                                                <p class="text-gray-400 text-xs">{{ $doc->estudiante->email_institucional }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 text-gray-300">v{{ $doc->version }}</td>
                                    <td class="py-3 text-gray-300">{{ $doc->entregado_en->format('d/m/Y H:i') }}</td>
                                    <td class="py-3">
                                        <a href="{{ asset('storage/' . $doc->archivo_ruta) }}" 
                                           target="_blank"
                                           class="text-blue-400 hover:text-blue-300 text-sm">
                                            <i class="fas fa-file-pdf mr-1"></i>Descargar
                                        </a>
                                    </td>
                                    <td class="py-3">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-900 bg-opacity-50 text-purple-300">
                                            <i class="fas fa-gavel mr-1"></i>En revisión tribunal
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <a href="{{ route('tribunal.revisar', $doc->id) }}" 
                                           class="text-purple-400 hover:text-purple-300" 
                                           title="Revisar documento">
                                            <i class="fas fa-eye mr-2"></i>Revisar
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-400 text-center py-8">
                        <i class="fas fa-inbox text-3xl mb-2"></i>
                        <br>Aún no hay entregas para revisar
                    </p>
                @endif
            </div>

            <!-- TAB: REVISADOS (Por el tribunal) -->
            <div id="content-revisados" class="tab-content hidden">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-clipboard-check text-blue-400 mr-2"></i>
                        Revisados por tribunal
                    </h3>
                    <input type="text" 
                           id="search-revisados"
                           onkeyup="filterTable('table-revisados', 'search-revisados')"
                           placeholder="Buscar alumnos..." 
                           class="px-4 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                
                @if($revisados->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full" id="table-revisados">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-300 uppercase border-b border-gray-700">
                                    <th class="pb-3">Nombre</th>
                                    <th class="pb-3">Versión</th>
                                    <th class="pb-3">Fecha de entrega</th>
                                    <th class="pb-3">Estado final</th>
                                    <th class="pb-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($revisados as $doc)
                                <tr class="hover:bg-white hover:bg-opacity-5">
                                    <td class="py-3">
                                        <div class="flex items-center">
                                            
                                            <div class="h-8 w-8 rounded-full bg-red-700 flex items-center justify-center text-white text-xs font-semibold">
                                                {{ substr($doc->estudiante->nombres, 0, 1) }}
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-white font-medium">{{ $doc->estudiante->nombres }} {{ $doc->estudiante->apellidos }}</p>
                                                <p class="text-gray-400 text-xs">{{ $doc->estudiante->email_institucional }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 text-gray-300">v{{ $doc->version }}</td>
                                    <td class="py-3 text-gray-300">{{ $doc->entregado_en->format('d/m/Y H:i') }}</td>
                                    <td class="py-3">
                                        @if($doc->estado->nombre === 'aprobado_tribunal')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-900 bg-opacity-50 text-green-300">
                                                <i class="fas fa-check-circle mr-1"></i>Aprobado final
                                            </span>
                                        @elseif($doc->estado->nombre === 'con_observaciones')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-900 bg-opacity-50 text-yellow-300">
                                                <i class="fas fa-comment-alt mr-1"></i>Con observaciones
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <a href="{{ route('tribunal.revisar', $doc->id) }}" 
                                           class="text-purple-400 hover:text-purple-300" 
                                           title="Ver revisión">
                                            <i class="fas fa-eye mr-2"></i>Ver
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-400 text-center py-8">
                        <i class="fas fa-inbox text-3xl mb-2"></i>
                        <br>No hay documentos revisados aún
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Función para cambiar entre tabs
function switchTab(tabName) {
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

// Función para buscar/filter en las tablas
function filterTable(tableId, searchId) {
    const input = document.getElementById(searchId);
    const filter = input.value.toUpperCase();
    const table = document.getElementById(tableId);
    const tr = table.getElementsByTagName('tr');
    
    for (let i = 1; i < tr.length; i++) {
        const td = tr[i].getElementsByTagName('td')[0]; // Buscar por nombre
        if (td) {
            const txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = '';
            } else {
                tr[i].style.display = 'none';
            }
        }
    }
}
</script>
@endpush
@endsection