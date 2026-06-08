@extends('director.layout')
@section('title', 'Reportes - Director')
@section('page-title', 'Reportes y Estadísticas')

@section('content')
<div class="space-y-6">
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Total Estudiantes</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $totalEstudiantes ?? 0 }}</p>
                </div>
                <div class="bg-blue-900 bg-opacity-50 rounded-full p-3">
                    <i class="fas fa-user-graduate text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Total Docentes</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $totalDocentes ?? 0 }}</p>
                </div>
                <div class="bg-green-900 bg-opacity-50 rounded-full p-3">
                    <i class="fas fa-chalkboard-teacher text-green-400 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Materias Activas</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $totalMaterias ?? 0 }}</p>
                </div>
                <div class="bg-purple-900 bg-opacity-50 rounded-full p-3">
                    <i class="fas fa-book text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Proyectos Activos</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $totalProyectos ?? 0 }}</p>
                </div>
                <div class="bg-yellow-900 bg-opacity-50 rounded-full p-3">
                    <i class="fas fa-folder-open text-yellow-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ALERTAS Y VENCIMIENTOS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Tareas por Vencer -->
        <div class="card-dark rounded-lg shadow p-6 border border-red-800 bg-red-900 bg-opacity-20">
            <h3 class="text-lg font-semibold text-red-300 mb-4">
                <i class="fas fa-exclamation-triangle mr-2"></i>Tareas por Vencer
            </h3>
            <p class="text-3xl font-bold text-white">{{ $tareasProximas->count() ?? 0 }}</p>
            <p class="text-sm text-gray-400 mb-3">Próximos 7 días</p>
            @if(($tareasProximas ?? collect())->isNotEmpty())
                <div class="space-y-2 max-h-40 overflow-y-auto">
                    @foreach($tareasProximas->take(5) as $tarea)
                        <div class="text-xs text-gray-300 bg-black bg-opacity-30 p-2 rounded">
                            <strong>{{ $tarea->titulo }}</strong><br>
                            <span class="text-gray-400">{{ $tarea->materia->nombre ?? 'N/A' }} — {{ $tarea->fecha_limite->format('d/m/Y') }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">No hay tareas próximas a vencer</p>
            @endif
        </div>

        <!-- Estudiantes Rezagados -->
        <div class="card-dark rounded-lg shadow p-6 border border-orange-800 bg-orange-900 bg-opacity-20">
            <h3 class="text-lg font-semibold text-orange-300 mb-4">
                <i class="fas fa-user-clock mr-2"></i>Estudiantes Rezagados
            </h3>
            <p class="text-3xl font-bold text-white">{{ $estudiantesRezagados->count() ?? 0 }}</p>
            <p class="text-sm text-gray-400 mb-3">Sin entregas en 30 días</p>
            @if(($estudiantesRezagados ?? collect())->isNotEmpty())
                <div class="space-y-2 max-h-40 overflow-y-auto">
                    @foreach($estudiantesRezagados as $inscripcion)
                        <div class="text-xs text-gray-300 bg-black bg-opacity-30 p-2 rounded">
                            <strong>{{ $inscripcion->estudiante->nombre_completo ?? 'N/A' }}</strong><br>
                            <span class="text-gray-400">Tutor: {{ $inscripcion->tutores->first()->nombre_completo ?? 'Sin asignar' }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">Todos los estudiantes están activos</p>
            @endif
        </div>

        <!-- Tareas Vencidas -->
        <div class="card-dark rounded-lg shadow p-6 border border-gray-700 bg-gray-900 bg-opacity-20">
            <h3 class="text-lg font-semibold text-gray-300 mb-4">
                <i class="fas fa-calendar-times mr-2"></i>Tareas Vencidas
            </h3>
            <p class="text-3xl font-bold text-white">{{ ($tareasVencidas ?? collect())->count() }}</p>
            <p class="text-sm text-gray-400 mb-3">Con estudiantes faltantes</p>
            @if(($tareasVencidas ?? collect())->isNotEmpty())
                <div class="space-y-2 max-h-40 overflow-y-auto">
                    @foreach($tareasVencidas->take(5) as $tarea)
                        <div class="text-xs text-gray-300 bg-black bg-opacity-30 p-2 rounded">
                            <strong>{{ $tarea['titulo'] }}</strong><br>
                            <span class="text-gray-400">{{ $tarea['materia'] }} — {{ $tarea['estudiantes_faltantes'] }} sin entregar</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">No hay tareas vencidas</p>
            @endif
        </div>
    </div>

    <!-- PROGRESO POR MATERIA (CORREGIDO) -->
    <div class="card-dark rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white">
                <i class="fas fa-graduation-cap mr-2 text-blue-400"></i>Progreso por Materia
            </h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($progresoPorMateria ?? [] as $materia)
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-white text-sm font-medium">{{ $materia['nombre'] }}</span>
                            <span class="text-gray-400 text-sm">
                                {{ $materia['entregas_unicas'] }}/{{ $materia['total_esperado'] }} 
                                ({{ $materia['porcentaje'] }}%)
                            </span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full transition-all" 
                                 style="width: {{ $materia['porcentaje'] }}%"></div>
                        </div>
                        <div class="flex gap-4 mt-1 text-xs text-gray-500">
                            <span>{{ $materia['estudiantes'] }} estudiantes</span>
                            <span>{{ $materia['tareas'] }} tareas</span>
                            <span>{{ $materia['entregas_unicas'] }} entregas realizadas</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- CARGA DE TUTORES -->
    <div class="card-dark rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white">
                <i class="fas fa-chalkboard-teacher mr-2 text-green-400"></i>Carga de Tutores
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-black bg-opacity-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs text-gray-300 uppercase">Tutor</th>
                        <th class="px-6 py-3 text-left text-xs text-gray-300 uppercase">Tutorados</th>
                        <th class="px-6 py-3 text-left text-xs text-gray-300 uppercase">Pendientes</th>
                        <th class="px-6 py-3 text-left text-xs text-gray-300 uppercase">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @foreach($cargaTutores ?? [] as $tutor)
                        <tr class="hover:bg-white hover:bg-opacity-5">
                            <td class="px-6 py-4 text-white">{{ $tutor['nombre'] }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $tutor['tutorados'] }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-bold
                                    {{ $tutor['pendientes'] > 5 ? 'bg-red-900 text-red-300' : 
                                       ($tutor['pendientes'] > 2 ? 'bg-yellow-900 text-yellow-300' : 'bg-green-900 text-green-300') }}">
                                    {{ $tutor['pendientes'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($tutor['pendientes'] > 5)
                                    <span class="text-red-400">Sobrecargado</span>
                                @elseif($tutor['pendientes'] > 2)
                                    <span class="text-yellow-400">Ocupado</span>
                                @else
                                    <span class="text-green-400">Disponible</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- RENDIMIENTO DE ESTUDIANTES (CORREGIDO) -->
    <div class="card-dark rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white">
                <i class="fas fa-user-graduate mr-2 text-purple-400"></i>Rendimiento de Estudiantes
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-black bg-opacity-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs text-gray-300 uppercase">Estudiante</th>
                        <th class="px-6 py-3 text-left text-xs text-gray-300 uppercase">Materia</th>
                        <th class="px-6 py-3 text-left text-xs text-gray-300 uppercase">Tutor</th>
                        <th class="px-6 py-3 text-left text-xs text-gray-300 uppercase">Entregas</th>
                        <th class="px-6 py-3 text-left text-xs text-gray-300 uppercase">Vencidas</th>
                        <th class="px-6 py-3 text-left text-xs text-gray-300 uppercase">Avance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @foreach(($rendimientoEstudiantes ?? collect())->take(10) as $est)
                        <tr class="hover:bg-white hover:bg-opacity-5">
                            <td class="px-6 py-4">
                                <div class="text-white font-medium">{{ $est['estudiante'] }}</div>
                                <div class="text-xs text-gray-500">{{ Str::limit($est['proyecto'], 30) }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-300">{{ $est['materia'] }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $est['tutor'] }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $est['entregas'] }}/{{ $est['tareas_totales'] }}</td>
                            <td class="px-6 py-4">
                                <span class="text-{{ $est['vencidas'] > 0 ? 'red' : 'gray' }}-400">
                                    {{ $est['vencidas'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-16 bg-gray-700 rounded-full h-1.5">
                                        <div class="bg-{{ $est['porcentaje'] >= 75 ? 'green' : ($est['porcentaje'] >= 50 ? 'yellow' : 'red') }}-500 h-1.5 rounded-full" 
                                             style="width: {{ $est['porcentaje'] }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-300">{{ $est['porcentaje'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Gráficos y Tablas originales -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Documentos por Estado -->
        <div class="card-dark rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-white mb-4">
                <i class="fas fa-chart-pie text-blue-400 mr-2"></i>Documentos por Estado
            </h3>
            @if(count($documentosPorEstado ?? []) > 0)
            <div class="space-y-3">
                @foreach($documentosPorEstado ?? [] as $estado => $total)
                <div class="flex items-center justify-between">
                    <span class="text-gray-300">{{ ucfirst(str_replace('_', ' ', $estado)) }}</span>
                    <span class="bg-gray-800 text-white px-3 py-1 rounded-full text-sm">{{ $total }}</span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-4">No hay datos disponibles</p>
            @endif
        </div>

        <!-- Proyectos por Materia -->
        <div class="card-dark rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-white mb-4">
                <i class="fas fa-list text-green-400 mr-2"></i>Top Materias con Proyectos
            </h3>
            
            @php
                $listaMaterias = is_object($proyectosPorMateria) && method_exists($proyectosPorMateria, 'toArray') 
                    ? $proyectosPorMateria 
                    : collect([]);
            @endphp
            
            @if($listaMaterias->isNotEmpty())
                <div class="space-y-3">
                    @foreach($listaMaterias as $item)
                        @php
                            $materia = is_object($item) ? ($item->materia ?? 'Sin nombre') : ($item['materia'] ?? 'Sin nombre');
                            $total = is_object($item) ? ($item->total ?? 0) : ($item['total'] ?? 0);
                        @endphp
                        <div class="flex items-center justify-between">
                            <span class="text-gray-300 truncate" title="{{ $materia }}">{{ $materia }}</span>
                            <span class="bg-green-900 text-green-300 px-3 py-1 rounded-full text-sm">{{ $total }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No hay datos disponibles</p>
            @endif
        </div>
    </div>

    <!-- Botones de Exportación -->
    <div class="card-dark rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-white mb-4">
            <i class="fas fa-download text-red-400 mr-2"></i>Exportar Reportes
        </h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('director.reportes.exportar', 'pdf') }}" 
               class="px-4 py-2 bg-red-700 hover:bg-red-600 text-white rounded-lg transition flex items-center">
                <i class="fas fa-file-pdf mr-2"></i>Exportar PDF
            </a>
            <a href="{{ route('director.reportes.exportar', 'excel') }}" 
               class="px-4 py-2 bg-green-700 hover:bg-green-600 text-white rounded-lg transition flex items-center">
                <i class="fas fa-file-excel mr-2"></i>Exportar Excel
            </a>
            <button onclick="window.print()" 
                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition flex items-center">
                <i class="fas fa-print mr-2"></i>Imprimir
            </button>
        </div>
    </div>
</div>
@endsection