@extends('director.layout')
@section('title', 'Perfil de Estudiante - Director')
@section('page-title', 'Perfil de Estudiante')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Header con acciones -->
    <div class="flex justify-between items-center">
        <a href="{{ route('director.estudiantes') }}" class="text-red-400 hover:text-red-300">
            <i class="fas fa-arrow-left mr-2"></i>Volver a lista
        </a>
        <!-- <div class="space-x-2">
            <button onclick="abrirModalAsignarTutor({{ $estudiante->id }})" 
                    class="bg-green-600 hover:bg-green-500 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-chalkboard-teacher mr-2"></i>Asignar Tutor
            </button>
            <button onclick="abrirModalAsignarTribunal({{ $estudiante->id }})" 
                    class="bg-purple-600 hover:bg-purple-500 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-gavel mr-2"></i>Asignar Tribunal
            </button>
        </div> -->
    </div>

    <!-- Información principal -->
    <div class="card-dark rounded-lg shadow p-6">
        <div class="flex items-center space-x-6 mb-6">
            <div class="h-20 w-20 rounded-full bg-red-700 flex items-center justify-center text-white text-2xl font-bold">
                {{ substr($estudiante->nombres, 0, 1) }}
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white">{{ $estudiante->nombres }} {{ $estudiante->apellidos }}</h2>
                <p class="text-gray-400">{{ $estudiante->email_institucional }}</p>
                <span class="inline-block mt-2 px-3 py-1 text-xs font-semibold rounded-full 
                    {{ $estudiante->activo ? 'bg-green-900 bg-opacity-50 text-green-300' : 'bg-red-900 bg-opacity-50 text-red-300' }}">
                    {{ $estudiante->activo ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Inscripciones -->
    <div class="card-dark rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-white mb-4">
            <i class="fas fa-clipboard-list text-blue-400 mr-2"></i>Inscripciones
        </h3>
        @if($estudiante->inscripciones->isNotEmpty())
            <div class="space-y-4">
                @foreach($estudiante->inscripciones as $inscripcion)
                <div class="bg-black bg-opacity-50 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="text-white font-semibold">{{ $inscripcion->titulo_proyecto ?? 'Sin título' }}</h4>
                            <p class="text-gray-400 text-sm">{{ $inscripcion->materia->nombre ?? 'N/A' }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $inscripcion->estado_inscripcion === 'activo' ? 'bg-green-900 bg-opacity-50 text-green-300' : 'bg-gray-900 bg-opacity-50 text-gray-300' }}">
                            {{ ucfirst($inscripcion->estado_inscripcion) }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-400">Tutor:</span>
                            <p class="text-white">{{ $inscripcion->tutores->first()?->nombres ?? 'Sin asignar' }} {{ $inscripcion->tutores->first()?->apellidos ?? '' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-400">Tribunales:</span>
                            <p class="text-white">{{ $inscripcion->tribunales->count() }}/2 asignados</p>
                        </div>
                    </div>
                    
                    <!-- Documentos del estudiante en esta inscripción -->
                    @if($inscripcion->documentos->isNotEmpty())
                    <div class="mt-4 pt-4 border-t border-gray-700">
                        <p class="text-sm text-gray-400 mb-2">Documentos entregados:</p>
                        <div class="space-y-2">
                            @foreach($inscripcion->documentos->take(3) as $doc)
                            <div class="flex items-center justify-between bg-black bg-opacity-30 rounded px-3 py-2">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-file-pdf text-red-400"></i>
                                    <span class="text-sm text-white truncate max-w-xs">{{ $doc->archivo_nombre_original }}</span>
                                </div>
                                <span class="text-xs text-gray-400">v{{ $doc->version }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-400">No tiene inscripciones registradas</p>
        @endif
    </div>
</div>

<!-- Modales para asignar tutor/tribunal (puedes reutilizar los del dashboard) -->
@endsection