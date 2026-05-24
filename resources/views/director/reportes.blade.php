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

    <!-- Gráficos y Tablas -->
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
                    <span class="text-gray-300">{{ ucfirst($estado) }}</span>
                    <span class="bg-gray-800 text-white px-3 py-1 rounded-full text-sm">{{ $total }}</span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-4">No hay datos disponibles</p>
            @endif
        </div>

        <!-- Proyectos por Materia (Top 5) -->
<div class="card-dark rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-white mb-4">
        <i class="fas fa-list text-green-400 mr-2"></i>Top Materias con Proyectos
    </h3>
    
    @php
        // Asegurar que sea una colección válida
        $listaMaterias = is_object($proyectosPorMateria) && method_exists($proyectosPorMateria, 'toArray') 
            ? $proyectosPorMateria 
            : collect([]);
    @endphp
    
    @if($listaMaterias->isNotEmpty())
        <div class="space-y-3">
            @foreach($listaMaterias as $item)
                @php
                    // Acceso seguro a propiedades (soporta objeto o array)
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