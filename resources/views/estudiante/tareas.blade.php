@extends('estudiante.layout')

@section('title', 'Tareas - Estudiante')
@section('page-title', 'TAREAS')

@section('content')
<div class="space-y-6">
    
    <!-- FILTROS DE TAREAS (Solo 3 opciones) -->
    <div class="card-dark rounded-lg shadow p-2">
        <div class="flex space-x-2">
            <a href="{{ route('estudiante.tareas', ['filtro' => 'proximamente']) }}" 
               class="flex-1 px-4 py-3 rounded-lg text-center transition {{ $filtro === 'proximamente' ? 'bg-blue-900 bg-opacity-70 text-white' : 'text-gray-400 hover:bg-black hover:bg-opacity-50' }}">
                <i class="fas fa-clock mr-2"></i>Próximamente
            </a>
            <a href="{{ route('estudiante.tareas', ['filtro' => 'vencida']) }}" 
               class="flex-1 px-4 py-3 rounded-lg text-center transition {{ $filtro === 'vencida' ? 'bg-red-900 bg-opacity-70 text-white' : 'text-gray-400 hover:bg-black hover:bg-opacity-50' }}">
                <i class="fas fa-exclamation-triangle mr-2"></i>Vencida
            </a>
            <a href="{{ route('estudiante.tareas', ['filtro' => 'completado']) }}" 
               class="flex-1 px-4 py-3 rounded-lg text-center transition {{ $filtro === 'completado' ? 'bg-green-900 bg-opacity-70 text-white' : 'text-gray-400 hover:bg-black hover:bg-opacity-50' }}">
                <i class="fas fa-check-circle mr-2"></i>Completado
            </a>
        </div>
    </div>

    <!-- LISTA DE TAREAS (UNA SOLA COLUMNA - Agrupado por fecha) -->
    <div class="space-y-6">
        @php
            // Agrupar tareas por fecha
            $tareasPorFecha = $tareas->groupBy(function($tarea) {
                if ($tarea->fecha_limite->isToday()) {
                    return 'Hoy';
                } elseif ($tarea->fecha_limite->isTomorrow()) {
                    return 'Mañana';
                } else {
                    return $tarea->fecha_limite->format('d M Y');
                }
            });
        @endphp

        @foreach($tareasPorFecha as $fecha => $tareasDelDia)
        <div>
            <h4 class="text-sm font-semibold text-gray-400 mb-3">
                {{ $fecha }}
                @if($fecha === 'Hoy')
                    <span class="text-xs font-normal text-blue-400">Hoy</span>
                @elseif($fecha === 'Mañana')
                    <span class="text-xs font-normal text-gray-500">Mañana</span>
                @else
                    <span class="text-xs font-normal text-gray-500">{{ \Carbon\Carbon::parse($fecha)->format('l') }}</span>
                @endif
            </h4>
            
            <div class="space-y-3">
                @foreach($tareasDelDia as $tarea)
                    @php
                        $ultimoDocumento = $tarea->documentos->first();
                        $tieneDocumento = $ultimoDocumento !== null;
                        $estaVencida = $tarea->fecha_limite < now();
                        $diasRestantes = now()->diffInDays($tarea->fecha_limite, false);
                    @endphp
                    
                    <!-- TARJA DE TAREA (Click para ver detalles) -->
                    <a href="{{ route('estudiante.tareas.ver', $tarea->id) }}" 
                       class="card-dark rounded-lg shadow border border-gray-700 hover:border-red-500 transition block p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4 flex-1">
                                <!-- Icono -->
                                <div class="flex-shrink-0">
                                    @if($tieneDocumento)
                                        <div class="h-10 w-10 rounded-lg bg-green-600 flex items-center justify-center text-white text-xs font-bold">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    @else
                                        <div class="h-10 w-10 rounded-lg bg-pink-600 flex items-center justify-center text-white text-xs font-bold">
                                            LE
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Información -->
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-white font-semibold text-sm truncate">
                                        {{ $tarea->titulo }}
                                    </h5>
                                    <p class="text-gray-400 text-xs mt-1">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        Vence el {{ $tarea->fecha_limite->format('H:i') }}
                                    </p>
                                    <p class="text-gray-500 text-xs mt-0.5">
                                        {{ $tarea->materia->nombre ?? 'N/A' }}
                                    </p>
                                    @if($tieneDocumento)
                                        <p class="text-green-400 text-xs mt-1">
                                            <i class="fas fa-file-pdf mr-1"></i>
                                            v{{ $ultimoDocumento->version }} entregada
                                        </p>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Badge de estado -->
                            <div class="flex-shrink-0 ml-4">
                                @if($tieneDocumento)
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-900 bg-opacity-50 text-green-300">
                                        Completado
                                    </span>
                                @elseif($estaVencida)
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-900 bg-opacity-50 text-red-300">
                                        Vencida
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-900 bg-opacity-50 text-blue-300">
                                        Pendiente
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        @endforeach
        
        @if($tareas->isEmpty())
        <div class="text-center py-12">
            <i class="fas fa-tasks text-6xl text-gray-600 mb-4"></i>
            <p class="text-gray-400 text-lg">No hay tareas disponibles</p>
            <p class="text-gray-500 text-sm mt-2">Las tareas asignadas aparecerán aquí</p>
        </div>
        @endif
    </div>
</div>
@endsection