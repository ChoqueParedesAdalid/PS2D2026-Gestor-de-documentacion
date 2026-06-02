@extends('tutor.layout')

@section('title', 'Dashboard - Tutor')
@section('page-title', 'Panel de Control')

@section('content')
<div class="space-y-6">
    
    <!-- STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Total Tutorados</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $totalTutorados ?? 0 }}</p>
                </div>
                <div class="bg-red-900 bg-opacity-50 rounded-full p-3">
                    <i class="fas fa-users text-red-400 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Documentos Pendientes</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $pendientes ?? 0 }}</p>
                </div>
                <div class="bg-yellow-900 bg-opacity-50 rounded-full p-3">
                    <i class="fas fa-clock text-yellow-400 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Documentos Aprobados</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $aprobados ?? 0 }}</p>
                </div>
                <div class="bg-green-900 bg-opacity-50 rounded-full p-3">
                    <i class="fas fa-check-circle text-green-400 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">En Revisión</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $enRevision ?? 0 }}</p>
                </div>
                <div class="bg-purple-900 bg-opacity-50 rounded-full p-3">
                    <i class="fas fa-eye text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- RECENT ACTIVITY & QUICK ACTIONS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- ACTIVIDAD RECIENTE (MEJORADA) -->
        <div class="lg:col-span-2 card-dark rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-white">
                    <i class="fas fa-history mr-2"></i>Actividad Reciente
                </h3>
                <a href="{{ route('tutor.documentos') }}" class="text-sm text-red-400 hover:text-red-300">
                    Ver todos <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($actividadReciente ?? [] as $actividad)
                    <div class="flex items-start space-x-4 p-4 bg-black bg-opacity-30 rounded-lg hover:bg-opacity-50 transition border border-gray-700">
                        {{-- Icono según estado del documento --}}
                        <div class="flex-shrink-0">
                            @php
                                $estadoNombre = $actividad->estado->nombre ?? 'entregado';
                                $icono = match($estadoNombre) {
                                    'entregado' => ['fa-file-upload', 'text-yellow-400', 'bg-yellow-900'],
                                    'con_observaciones' => ['fa-exclamation-triangle', 'text-orange-400', 'bg-orange-900'],
                                    'visto_bueno' => ['fa-check', 'text-blue-400', 'bg-blue-900'],
                                    'aprobado_tribunal' => ['fa-trophy', 'text-green-400', 'bg-green-900'],
                                    default => ['fa-file', 'text-gray-400', 'bg-gray-900']
                                };
                            @endphp
                            <div class="{{ $icono[2] }} bg-opacity-50 rounded-full p-3">
                                <i class="fas {{ $icono[0] }} {{ $icono[1] }} text-lg"></i>
                            </div>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            {{-- Nombre del estudiante --}}
                            <p class="text-sm font-bold text-white truncate">
                                {{ $actividad->estudiante->nombres }} {{ $actividad->estudiante->apellidos }}
                            </p>
                            
                            {{-- Tarea y documento --}}
                            <p class="text-sm text-gray-300 mt-1">
                                <i class="fas fa-tasks text-xs mr-1"></i>
                                {{ $actividad->tarea->titulo ?? 'Documento de proyecto' }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1 truncate">
                                <i class="fas fa-file text-xs mr-1"></i>
                                {{ $actividad->archivo_nombre_original }}
                            </p>
                            
                            {{-- Estado y fecha --}}
                            <div class="flex items-center mt-2 space-x-3">
                                <span class="px-2 py-0.5 text-xs rounded-full 
                                    {{ $estadoNombre === 'aprobado_tribunal' ? 'bg-green-900 bg-opacity-50 text-green-300' : 
                                       ($estadoNombre === 'visto_bueno' ? 'bg-blue-900 bg-opacity-50 text-blue-300' : 
                                       ($estadoNombre === 'con_observaciones' ? 'bg-orange-900 bg-opacity-50 text-orange-300' : 
                                       'bg-yellow-900 bg-opacity-50 text-yellow-300')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $estadoNombre)) }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    <i class="far fa-clock mr-1"></i>
                                    {{ $actividad->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                        
                        {{-- Botón de acción --}}
                        <div class="flex-shrink-0 self-center">
                            <a href="{{ route('tutor.revisar', $actividad->id) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-red-700 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition">
                                <i class="fas fa-eye mr-1.5"></i>Revisar
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <div class="bg-gray-800 bg-opacity-50 rounded-full p-4 inline-block mb-3">
                            <i class="fas fa-inbox text-gray-500 text-3xl"></i>
                        </div>
                        <p class="text-gray-400">No hay actividad reciente</p>
                        <p class="text-gray-500 text-sm mt-1">Los documentos de tus tutorados aparecerán aquí</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- ACCIONES RÁPIDAS -->
        <div class="card-dark rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white">
                    <i class="fas fa-bolt mr-2"></i>Acciones Rápidas
                </h3>
            </div>
            <div class="p-6 space-y-3">
                <a href="{{ route('tutor.tutorados') }}" 
                   class="flex items-center p-3 bg-black bg-opacity-50 hover:bg-opacity-70 rounded-lg transition group">
                    <div class="bg-red-900 bg-opacity-50 rounded-full p-2 mr-3 group-hover:bg-red-800">
                        <i class="fas fa-users text-red-400"></i>
                    </div>
                    <div>
                        <span class="text-white font-medium block">Ver Tutorados</span>
                        <span class="text-gray-500 text-xs">Lista de estudiantes asignados</span>
                    </div>
                </a>
                
                <a href="{{ route('tutor.documentos') }}" 
                   class="flex items-center p-3 bg-black bg-opacity-50 hover:bg-opacity-70 rounded-lg transition group">
                    <div class="bg-yellow-900 bg-opacity-50 rounded-full p-2 mr-3 group-hover:bg-yellow-800">
                        <i class="fas fa-file-alt text-yellow-400"></i>
                    </div>
                    <div>
                        <span class="text-white font-medium block">Documentos Pendientes</span>
                        <span class="text-gray-500 text-xs">Revisar entregas recientes</span>
                    </div>
                </a>
                
                <a href="{{ route('tutor.tareas') }}" 
                   class="flex items-center p-3 bg-black bg-opacity-50 hover:bg-opacity-70 rounded-lg transition group">
                    <div class="bg-green-900 bg-opacity-50 rounded-full p-2 mr-3 group-hover:bg-green-800">
                        <i class="fas fa-tasks text-green-400"></i>
                    </div>
                    <div>
                        <span class="text-white font-medium block">Ver Tareas</span>
                        <span class="text-gray-500 text-xs">Tareas de tus tutorados</span>
                    </div>
                </a>
                
                <button onclick="window.location.href='{{ route('tutor.tutorados') }}'" 
                        class="w-full flex items-center p-3 bg-black bg-opacity-50 hover:bg-opacity-70 rounded-lg transition group">
                    <div class="bg-purple-900 bg-opacity-50 rounded-full p-2 mr-3 group-hover:bg-purple-800">
                        <i class="fas fa-chart-bar text-purple-400"></i>
                    </div>
                    <div>
                        <span class="text-white font-medium block">Generar Reporte</span>
                        <span class="text-gray-500 text-xs">Reportes de estudiantes</span>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection