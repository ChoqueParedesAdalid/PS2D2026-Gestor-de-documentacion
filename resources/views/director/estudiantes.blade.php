@extends('director.layout')

@section('title', 'Estudiantes - Director')
@section('page-title', 'Gestión de Estudiantes')

@section('content')
<div class="space-y-6">
    
    <!-- Header con búsqueda -->
    <div class="flex justify-between items-center">
        <div>
            <p class="text-gray-300">Administra los estudiantes y sus asignaciones</p>
        </div>
        <div class="flex space-x-2">
            <input type="text" placeholder="Buscar estudiante..." 
                   class="px-4 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500">
            <button class="bg-red-700 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>

    <!-- Tabla de estudiantes -->
    <div class="card-dark rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-black bg-opacity-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Estudiante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Correo Institucional</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Proyecto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Tutor Asignado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($estudiantes as $estudiante)
                    <tr class="hover:bg-white hover:bg-opacity-5 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-red-700 flex items-center justify-center text-white font-semibold">
                                    {{ substr($estudiante->nombres, 0, 1) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-white">
                                        {{ $estudiante->nombres }} {{ $estudiante->apellidos }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300">
                            {{ $estudiante->email_institucional }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300">
                            {{ $estudiante->inscripciones->first()?->titulo_proyecto ?? 'Sin proyecto' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-white">
                            @php
                                $inscripcion = $estudiante->inscripciones->first();
                                $tutor = $inscripcion?->tutores?->first();
                            @endphp
                            {{ $tutor ? $tutor->nombres . ' ' . $tutor->apellidos : 'Sin asignar' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $estudiante->activo ? 'bg-green-900 bg-opacity-50 text-green-300' : 'bg-red-900 bg-opacity-50 text-red-300' }}">
                                {{ $estudiante->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
    <!-- Ver perfil -->
    <a href="{{ route('director.estudiantes.ver', $estudiante->id) }}" 
       class="text-blue-400 hover:text-blue-300 mr-3" title="Ver perfil">
        <i class="fas fa-user"></i>
    </a>
    
   
    
    <!-- Desactivar estudiante -->
    <form action="{{ route('director.estudiantes.eliminar', $estudiante->id) }}" 
          method="POST" class="inline" 
          onsubmit="return confirm('¿Desactivar este estudiante? Esta acción es reversible.')">
        @csrf @method('DELETE')
        <button type="submit" class="text-red-400 hover:text-red-300 ml-3" title="Desactivar">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-user-graduate text-4xl mb-3"></i>
                            <p>No hay estudiantes registrados</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if(isset($estudiantes) && $estudiantes->hasPages())
        <div class="px-6 py-4 border-t border-gray-700">
            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-400">
                    Mostrando {{ $estudiantes->firstItem() ?? 0 }}-{{ $estudiantes->lastItem() ?? 0 }} de {{ $estudiantes->total() }} estudiantes
                </p>
                <div class="flex space-x-1">
                    {{ $estudiantes->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection