@extends('director.layout')
@section('title', 'Perfil de Docente - Director')
@section('page-title', 'Perfil de Docente')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Header con acciones -->
    <div class="flex justify-between items-center">
        <a href="{{ route('director.docentes') }}" class="text-red-400 hover:text-red-300">
            <i class="fas fa-arrow-left mr-2"></i>Volver a lista
        </a>
        <div class="space-x-2">
            <a href="{{ route('director.docentes.editar', $docente->id) }}" class="bg-yellow-600 hover:bg-yellow-500 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-edit mr-2"></i>Editar
            </a>
        </div>
    </div>

    <!-- Información principal -->
    <div class="card-dark rounded-lg shadow p-6">
        <div class="flex items-center space-x-6 mb-6">
            <div class="h-20 w-20 rounded-full bg-red-700 flex items-center justify-center text-white text-2xl font-bold">
                {{ substr($docente->nombres, 0, 1) }}
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white">{{ $docente->nombres }} {{ $docente->apellidos }}</h2>
                <p class="text-gray-400">{{ $docente->email_institucional }}</p>
                <span class="inline-block mt-2 px-3 py-1 text-xs font-semibold rounded-full 
                    {{ $docente->activo ? 'bg-green-900 bg-opacity-50 text-green-300' : 'bg-red-900 bg-opacity-50 text-red-300' }}">
                    {{ $docente->activo ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-semibold text-gray-300 mb-2">Rol Actual</h4>
                <p class="text-white">{{ ucfirst(str_replace('_', ' ', $docente->rol->nombre)) }}</p>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-300 mb-2">Estado</h4>
                <p class="text-white">{{ $docente->activo ? 'Activo' : 'Inactivo' }}</p>
            </div>
        </div>
    </div>

    <!-- Asignaciones como Tutor -->
    <div class="card-dark rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-white mb-4">
            <i class="fas fa-chalkboard-teacher text-blue-400 mr-2"></i>Estudiantes como Tutor
        </h3>
        @if($docente->asignacionesTutor->isNotEmpty())
            <div class="space-y-3">
                @foreach($docente->asignacionesTutor as $asignacion)
                <div class="bg-black bg-opacity-50 rounded-lg p-3 flex justify-between items-center">
                    <div>
                        <p class="text-white font-medium">{{ $asignacion->inscripcion->estudiante->nombres ?? 'N/A' }} {{ $asignacion->inscripcion->estudiante->apellidos ?? '' }}</p>
                        <p class="text-gray-400 text-sm">{{ $asignacion->inscripcion->titulo_proyecto ?? 'Sin proyecto' }}</p>
                    </div>
                    <span class="text-xs text-green-400">
                        <i class="fas fa-check-circle mr-1"></i>Activo
                    </span>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-400">No tiene estudiantes asignados como tutor</p>
        @endif
    </div>

    <!-- Asignaciones como Tribunal -->
    <div class="card-dark rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-white mb-4">
            <i class="fas fa-gavel text-purple-400 mr-2"></i>Estudiantes como Tribunal
        </h3>
        @if($docente->asignacionesTribunal->isNotEmpty())
            <div class="space-y-3">
                @foreach($docente->asignacionesTribunal as $asignacion)
                <div class="bg-black bg-opacity-50 rounded-lg p-3 flex justify-between items-center">
                    <div>
                        <p class="text-white font-medium">{{ $asignacion->inscripcion->estudiante->nombres ?? 'N/A' }} {{ $asignacion->inscripcion->estudiante->apellidos ?? '' }}</p>
                        <p class="text-gray-400 text-sm">{{ $asignacion->inscripcion->titulo_proyecto ?? 'Sin proyecto' }}</p>
                    </div>
                    <span class="text-xs text-purple-400">
                        <i class="fas fa-gavel mr-1"></i>Tribunal
                    </span>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-400">No tiene estudiantes asignados como tribunal</p>
        @endif
    </div>
</div>
@endsection