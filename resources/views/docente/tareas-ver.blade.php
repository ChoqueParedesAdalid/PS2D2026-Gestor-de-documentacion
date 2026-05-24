@extends('docente.layout')
@section('title', 'Detalle de Tarea - Docente a Cargo')
@section('page-title', 'Detalle de Tarea')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    
    @if(session('success'))
        <div class="bg-green-900 bg-opacity-70 border border-green-600 text-green-100 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Header con acciones -->
    <div class="flex justify-between items-center">
        <a href="{{ route('docente.tareas', ['materiaId' => $tarea->materia_id]) }}" 
           class="text-red-400 hover:text-red-300">
            <i class="fas fa-arrow-left mr-2"></i>Volver a tareas
        </a>
        <div class="space-x-2">
            <button onclick="document.getElementById('modalEditarTarea').classList.remove('hidden')" 
                    class="bg-yellow-600 hover:bg-yellow-500 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-edit mr-2"></i>Editar Tarea
            </button>
        </div>
    </div>

    <!-- Información de la tarea -->
    <div class="card-dark rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-white mb-2">{{ $tarea->titulo }}</h2>
        <p class="text-gray-400 mb-4">{{ $tarea->descripcion }}</p>
        
        <div class="grid grid-cols-3 gap-4">
            <div>
                <span class="text-sm text-gray-400">Fecha límite</span>
                <p class="text-white font-semibold">{{ $tarea->fecha_limite->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-400">Tipo de documento</span>
                <p class="text-white font-semibold">{{ ucfirst(str_replace('_', ' ', $tarea->tipo_documento)) }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-400">Entregas</span>
                <p class="text-white font-semibold">
                    {{ $entregados->count() }} / {{ $estudiantesMateria->count() }} estudiantes
                </p>
            </div>
        </div>
    </div>

    <!-- ESTUDIANTES QUE YA ENTREGARON -->
    <div class="card-dark rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-white mb-4">
            <i class="fas fa-check-circle text-green-400 mr-2"></i>Entregados ({{ $entregados->count() }})
        </h3>
        @if($entregados->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-xs font-medium text-gray-300 uppercase border-b border-gray-700">
                            <th class="pb-3">Estudiante</th>
                            <th class="pb-3">Versión</th>
                            <th class="pb-3">Fecha de entrega</th>
                            <th class="pb-3">Archivo</th>
                            <th class="pb-3">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($entregados as $doc)
                        <tr>
                            <td class="py-3">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-red-700 flex items-center justify-center text-white text-sm font-semibold">
                                        {{ substr($doc->estudiante->nombres, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-white font-medium">{{ $doc->estudiante->nombres }} {{ $doc->estudiante->apellidos }}</p>
                                        <p class="text-xs text-gray-400">{{ $doc->estudiante->email_institucional }}</p>
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
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-900 bg-opacity-50 text-green-300">
                                    {{ ucfirst($doc->estado->nombre ?? 'entregado') }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-400 text-center py-4">Ningún estudiante ha entregado aún</p>
        @endif
    </div>

    <!-- ESTUDIANTES QUE AÚN NO ENTREGAN -->
    @if($pendientes->isNotEmpty())
    <div class="card-dark rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-white mb-4">
            <i class="fas fa-clock text-yellow-400 mr-2"></i>Pendientes de entrega ({{ $pendientes->count() }})
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs font-medium text-gray-300 uppercase border-b border-gray-700">
                        <th class="pb-3">Estudiante</th>
                        <th class="pb-3">Correo</th>
                        <th class="pb-3">Tutor asignado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @foreach($pendientes as $estudiante)
                    <tr>
                        <td class="py-3 text-white">{{ $estudiante->nombres }} {{ $estudiante->apellidos }}</td>
                        <td class="py-3 text-gray-300">{{ $estudiante->email_institucional }}</td>
                        <td class="py-3 text-gray-300">
                            @php
                                $inscripcion = $estudiantesMateria->firstWhere('estudiante_id', $estudiante->id);
                                $tutor = $inscripcion?->tutores->first();
                            @endphp
                            {{ $tutor ? $tutor->nombres . ' ' . $tutor->apellidos : 'Sin asignar' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Modal Editar Tarea -->
<div id="modalEditarTarea" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center">
    <div class="bg-gray-900 rounded-lg p-6 max-w-md w-full mx-4 border border-gray-700">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-white">Editar Tarea</h3>
            <button onclick="document.getElementById('modalEditarTarea').classList.add('hidden')" 
                    class="text-gray-400 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form action="{{ route('docente.tareas.actualizar', $tarea->id) }}" method="POST">
            @csrf @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Título</label>
                    <input type="text" name="titulo" value="{{ old('titulo', $tarea->titulo) }}" required 
                           class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Descripción</label>
                    <textarea name="descripcion" rows="3" required 
                              class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('descripcion', $tarea->descripcion) }}</textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Fecha Límite</label>
                    <input type="datetime-local" name="fecha_limite" value="{{ old('fecha_limite', $tarea->fecha_limite->format('Y-m-d\TH:i')) }}" required 
                           class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Tipo de Documento</label>
                    <select name="tipo_documento" required 
                            class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="anteproyecto" {{ $tarea->tipo_documento == 'anteproyecto' ? 'selected' : '' }}>Anteproyecto</option>
                        <option value="documento_final" {{ $tarea->tipo_documento == 'documento_final' ? 'selected' : '' }}>Documento Final</option>
                        <option value="anexos" {{ $tarea->tipo_documento == 'anexos' ? 'selected' : '' }}>Anexos</option>
                        <option value="otro" {{ $tarea->tipo_documento == 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-6 flex space-x-3">
                <button type="button" onclick="document.getElementById('modalEditarTarea').classList.add('hidden')" 
                        class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                    Cancelar
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-red-700 hover:bg-red-600 text-white rounded-lg transition">
                    Actualizar Tarea
                </button>
            </div>
        </form>
    </div>
</div>
@endsection