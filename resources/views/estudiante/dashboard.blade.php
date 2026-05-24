@extends('estudiante.layout')

@section('title', 'Inicio - Estudiante')
@section('page-title', 'ESTUDIANTE')

@section('content')
<div class="space-y-6">
    
    <!-- INFORMACIÓN DEL ESTUDIANTE -->
    <div class="mb-6">
        <p class="text-gray-300 text-lg">
            {{ auth()->user()->nombres }} {{ auth()->user()->apellidos }} | 
            <span class="text-red-400">{{ $inscripcion->materia->nombre ?? 'Proyecto de Sistemas' }}</span>
        </p>
    </div>

    <!-- TARJETAS DE TUTORES Y TRIBUNALES -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- MI TUTOR -->
        <div class="card-dark rounded-lg shadow p-6 border border-red-900 border-opacity-50">
            <div class="text-center">
                <div class="text-gray-400 text-sm mb-2">MI TUTOR</div>
                @if($tutor)
                    <div class="text-white font-semibold text-lg">
                        {{ $tutor->nombres }} {{ $tutor->apellidos }}
                    </div>
                    <div class="text-gray-400 text-sm mt-1">{{ $tutor->email_institucional }}</div>
                @else
                    <div class="text-gray-500">Sin tutor asignado</div>
                @endif
            </div>
        </div>
        
        <!-- JURADO 1 -->
        <div class="card-dark rounded-lg shadow p-6 border border-red-900 border-opacity-50">
            <div class="text-center">
                <div class="text-gray-400 text-sm mb-2">JURADO 1</div>
                @if(isset($tribunales[0]))
                    <div class="text-white font-semibold text-lg">
                        {{ $tribunales[0]->nombres }} {{ $tribunales[0]->apellidos }}
                    </div>
                    <div class="text-gray-400 text-sm mt-1">{{ $tribunales[0]->email_institucional }}</div>
                @else
                    <div class="text-gray-500">Sin asignar</div>
                @endif
            </div>
        </div>
        
        <!-- JURADO 2 -->
        <div class="card-dark rounded-lg shadow p-6 border border-red-900 border-opacity-50">
            <div class="text-center">
                <div class="text-gray-400 text-sm mb-2">JURADO 2</div>
                @if(isset($tribunales[1]))
                    <div class="text-white font-semibold text-lg">
                        {{ $tribunales[1]->nombres }} {{ $tribunales[1]->apellidos }}
                    </div>
                    <div class="text-gray-400 text-sm mt-1">{{ $tribunales[1]->email_institucional }}</div>
                @else
                    <div class="text-gray-500">Sin asignar</div>
                @endif
            </div>
        </div>
    </div>

    <!-- ESTADÍSTICAS DE TAREAS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Próximamente</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $tareasPendientes }}</p>
                </div>
                <div class="bg-yellow-900 bg-opacity-50 rounded-full p-3">
                    <i class="fas fa-clock text-yellow-400 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Vencida</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $tareasVencidas }}</p>
                </div>
                <div class="bg-red-900 bg-opacity-50 rounded-full p-3">
                    <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Completado</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $tareasCompletadas }}</p>
                </div>
                <div class="bg-green-900 bg-opacity-50 rounded-full p-3">
                    <i class="fas fa-check-circle text-green-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- TAREAS RECIENTES -->
    <div class="card-dark rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-white">Revisiones Documentales</h3>
            <a href="{{ route('estudiante.tareas') }}" class="text-red-400 hover:text-red-300 text-sm">
                Ver todas <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="p-6 space-y-4">
            @forelse($tareas->take(3) as $tarea)
                @php
                    $tieneDocumento = $tarea->documentos->isNotEmpty();
                    $estaVencida = $tarea->fecha_limite < now();
                @endphp
                
                <div class="bg-black bg-opacity-30 rounded-lg p-4 border border-gray-700">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="text-white font-semibold">{{ $tarea->titulo }}</h4>
                            <p class="text-gray-400 text-sm mt-1">{{ $tarea->descripcion }}</p>
                        </div>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                            {{ $tieneDocumento ? 'bg-green-900 bg-opacity-50 text-green-300' : ($estaVencida ? 'bg-red-900 bg-opacity-50 text-red-300' : 'bg-yellow-900 bg-opacity-50 text-yellow-300') }}">
                            {{ $tieneDocumento ? 'Completado' : ($estaVencida ? 'Vencida' : 'Pendiente') }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-400">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            Fecha límite: {{ $tarea->fecha_limite->format('d/m/Y H:i') }}
                        </div>
                        
                        @if($tieneDocumento)
                            <div class="text-sm text-green-400">
                                <i class="fas fa-check-circle mr-2"></i>
                                v{{ $tarea->documentos->first()->version }} entregado
                            </div>
                        @else
                            <a href="{{ route('estudiante.tareas') }}" class="bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition text-sm">
                                <i class="fas fa-upload mr-2"></i>Subir
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-gray-400 text-center py-8">No hay tareas disponibles</p>
            @endforelse
        </div>
    </div>
</div>
@endsection