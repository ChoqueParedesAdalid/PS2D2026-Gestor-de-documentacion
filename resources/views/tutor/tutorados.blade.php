@extends('tutor.layout')

@section('title', 'Tutorados - Tutor')
@section('page-title', 'Mis Tutorados')

@section('content')
<div class="card-dark rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-700 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <h3 class="text-lg font-semibold text-white">Lista de Estudiantes Tutorados</h3>
        
        <!-- Formulario de búsqueda -->
        <form method="GET" action="{{ route('tutor.tutorados') }}" class="flex w-full md:w-auto space-x-2">
            <input type="text" name="search" placeholder="Buscar estudiante..." 
                   value="{{ request('search') }}"
                   class="flex-1 md:flex-none w-full md:w-64 px-4 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500">
            <button type="submit" class="bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-black bg-opacity-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Código</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Estudiante</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Proyecto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse($tutorados ?? [] as $estudiante)
                <tr class="hover:bg-white hover:bg-opacity-5 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">
                        {{ explode('@', $estudiante->email_institucional)[0] }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-red-900 bg-opacity-50 flex items-center justify-center text-red-400 font-semibold">
                                {{ substr($estudiante->nombres, 0, 1) }}
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-white">
                                    {{ $estudiante->nombres }} {{ $estudiante->apellidos }}
                                </div>
                                <div class="text-sm text-gray-400">
                                    {{ $estudiante->email_institucional }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-300 max-w-xs truncate">
                        {{ $estudiante->titulo_proyecto ?? 'Sin título asignado' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $estadoClass = match($estudiante->estado_inscripcion) {
                                'activo' => 'bg-green-900 bg-opacity-50 text-green-300',
                                'finalizado' => 'bg-blue-900 bg-opacity-50 text-blue-300',
                                'abandonado' => 'bg-gray-900 bg-opacity-50 text-gray-300',
                                default => 'bg-yellow-900 bg-opacity-50 text-yellow-300',
                            };
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $estadoClass }}">
                            {{ ucfirst($estudiante->estado_inscripcion ?? 'activo') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('tutor.documentos') }}?estudiante={{ $estudiante->id }}" 
                           class="text-red-400 hover:text-red-300" title="Ver documentos">
                            <i class="fas fa-folder-open"></i>
                        </a>
                        <a href="{{ route('tutor.revisar') }}?estudiante={{ $estudiante->id }}" 
                           class="text-green-400 hover:text-green-300" title="Revisar">
                            <i class="fas fa-tasks"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                        <i class="fas fa-users text-4xl mb-3 text-gray-600"></i>
                        <p class="text-lg">No tienes estudiantes tutorados asignados</p>
                        <p class="text-sm mt-1">Contacta al docente a cargo para recibir asignaciones</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Paginación -->
    @if(isset($tutorados) && $tutorados->hasPages())
    <div class="px-6 py-4 border-t border-gray-700">
        <div class="flex justify-between items-center">
            <p class="text-sm text-gray-400">
                Mostrando {{ $tutorados->firstItem() ?? 0 }}-{{ $tutorados->lastItem() ?? 0 }} de {{ $tutorados->total() }} estudiantes
            </p>
            <div class="flex space-x-1">
                {{ $tutorados->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    /* Estilos para paginación de Laravel con tema oscuro */
    .pagination { display: flex; gap: 0.25rem; }
    .pagination li { display: inline; }
    .pagination span, .pagination a {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 2rem; height: 2rem; padding: 0 0.5rem;
        border: 1px solid #374151; border-radius: 0.375rem;
        background-color: rgba(0,0,0,0.5); color: #e5e7eb;
        font-size: 0.875rem; text-decoration: none; transition: all 0.2s;
    }
    .pagination span.active, .pagination a:hover {
        background-color: #991b1b; border-color: #991b1b; color: white;
    }
    .pagination span.disabled { opacity: 0.5; cursor: not-allowed; }
</style>
@endpush