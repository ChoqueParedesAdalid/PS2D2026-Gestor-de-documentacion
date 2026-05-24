@extends('docente.layout')
@section('title', 'Dashboard - Docente a Cargo')
@section('page-title', 'Panel de Control')

@section('content')
<div class="space-y-6">
    <!-- STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Estudiantes Inscritos</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $totalEstudiantes ?? 0 }}</p>
                </div>
                <div class="bg-red-900 bg-opacity-50 rounded-full p-3"><i class="fas fa-user-graduate text-red-400 text-xl"></i></div>
            </div>
        </div>
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Tutores Asignados</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $totalTutores ?? 0 }}</p>
                </div>
                <div class="bg-blue-900 bg-opacity-50 rounded-full p-3"><i class="fas fa-chalkboard-teacher text-blue-400 text-xl"></i></div>
            </div>
        </div>
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Tribunales Asignados</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $totalTribunales ?? 0 }}</p>
                </div>
                <div class="bg-purple-900 bg-opacity-50 rounded-full p-3"><i class="fas fa-gavel text-purple-400 text-xl"></i></div>
            </div>
        </div>
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Tareas Activas</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $totalTareas ?? 0 }}</p>
                </div>
                <div class="bg-green-900 bg-opacity-50 rounded-full p-3"><i class="fas fa-tasks text-green-400 text-xl"></i></div>
            </div>
        </div>
    </div>

    <!-- MATERIAS A CARGO -->
    <div class="card-dark rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white">Materias a Cargo</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($materias ?? [] as $materia)
                <div class="bg-black bg-opacity-50 rounded-lg p-4 border border-gray-700">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="text-white font-semibold">{{ $materia->nombre }}</h4>
                            <p class="text-gray-400 text-sm">{{ $materia->descripcion }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-900 bg-opacity-50 text-blue-300">{{ $materia->semestre_requerido }}</span>
                    </div>
                    <div class="text-sm text-gray-400 mb-3"><i class="fas fa-users mr-2"></i>{{ $materia->inscripciones->count() }} estudiantes</div>
                    <div class="flex space-x-2">
                        <a href="{{ route('docente.estudiantes', $materia->id) }}" class="flex-1 text-center bg-red-700 hover:bg-red-600 text-white px-3 py-2 rounded-lg transition text-sm">Ver Estudiantes</a>
                        <a href="{{ route('docente.tareas', $materia->id) }}" class="flex-1 text-center bg-black bg-opacity-50 hover:bg-opacity-70 text-white px-3 py-2 rounded-lg transition text-sm">Ver Tareas</a>
                    </div>
                </div>
                @empty
                <p class="text-gray-400 text-center col-span-3 py-8">No tienes materias asignadas. Contacta al Director.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ACTIVIDAD RECIENTE -->
    <div class="card-dark rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white"><i class="fas fa-clock text-red-400 mr-2"></i>Actividad Reciente</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($actividadReciente ?? [] as $actividad)
                <div class="flex items-start space-x-4 p-3 hover:bg-white hover:bg-opacity-5 rounded-lg transition">
                    <div class="bg-red-900 bg-opacity-50 rounded-full p-2"><i class="fas fa-file-upload text-red-400"></i></div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-white">{{ $actividad->estudiante?->nombres ?? 'Estudiante' }} {{ $actividad->estudiante?->apellidos ?? '' }} entregó un documento</p>
                        <p class="text-xs text-gray-400">{{ $actividad->tarea?->titulo ?? 'Documento de proyecto' }}</p>
                        <p class="text-xs text-gray-500 mt-1">Hace {{ $actividad->created_at?->diffForHumans() ?? 'unos momentos' }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-12 text-gray-400"><i class="fas fa-inbox text-4xl mb-3"></i><p>No hay actividad reciente</p></div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection