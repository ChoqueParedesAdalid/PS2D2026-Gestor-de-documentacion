@extends('tutor.layout')

@section('title', 'Documentos - Tutor')
@section('page-title', 'Lista de Documentos')

@section('content')
<div class="card-dark rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-700">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-4">
            <h3 class="text-lg font-semibold text-white">Gestión de Documentos</h3>
            
            <!-- Tabs de filtro -->
            <div class="flex space-x-2 bg-black bg-opacity-50 p-1 rounded-lg">
                <a href="{{ route('tutor.documentos', array_merge(request()->query(), ['filtro' => 'pendientes'])) }}" 
                   class="px-4 py-2 rounded-md font-medium transition {{ request('filtro', 'pendientes') === 'pendientes' ? 'bg-yellow-900 bg-opacity-50 text-yellow-300' : 'text-gray-400 hover:text-white' }}">
                    <i class="fas fa-clock mr-2"></i>Pendientes
                </a>
                <a href="{{ route('tutor.documentos', array_merge(request()->query(), ['filtro' => 'aprobados'])) }}" 
                   class="px-4 py-2 rounded-md font-medium transition {{ request('filtro') === 'aprobados' ? 'bg-green-900 bg-opacity-50 text-green-300' : 'text-gray-400 hover:text-white' }}">
                    <i class="fas fa-check-circle mr-2"></i>Aprobados
                </a>
            </div>
        </div>
        
        <!-- Filtros de búsqueda -->
        <form method="GET" action="{{ route('tutor.documentos') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="hidden" name="filtro" value="{{ request('filtro', 'pendientes') }}">
            <input type="text" name="search" placeholder="Buscar documento o estudiante..." 
                   value="{{ request('search') }}"
                   class="flex-1 px-4 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500">
            <select name="estudiante" onchange="this.form.submit()" 
                    class="px-4 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="">Todos los estudiantes</option>
                @if(isset($tutorados))
                    @foreach($tutorados as $est)
                        <option value="{{ $est->id }}" {{ request('estudiante') == $est->id ? 'selected' : '' }}>
                            {{ $est->nombres }} {{ $est->apellidos }}
                        </option>
                    @endforeach
                @endif
            </select>
            <button type="submit" class="bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                <i class="fas fa-search mr-2"></i>Filtrar
            </button>
        </form>
    </div>
    
    <!-- Tabla de documentos -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-black bg-opacity-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Estudiante</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Documento</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Versión</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse($documentos ?? [] as $doc)
                <tr class="hover:bg-white hover:bg-opacity-5 transition">
                    <td class="px-6 py-4 text-sm font-medium text-white">
                        {{ $doc->estudiante->nombres }} {{ $doc->estudiante->apellidos }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-300">
                        <div class="flex items-center">
                            <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                            <span class="truncate max-w-xs" title="{{ $doc->archivo_nombre_original }}">
                                {{ $doc->archivo_nombre_original }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-400">
                        v{{ $doc->version }}.0
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-400">
                        {{ $doc->entregado_en ? $doc->entregado_en->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $estadoClass = match($doc->estado->nombre ?? '') {
                                'entregado' => 'bg-yellow-900 bg-opacity-50 text-yellow-300',
                                'con_observaciones' => 'bg-orange-900 bg-opacity-50 text-orange-300',
                                'visto_bueno' => 'bg-green-900 bg-opacity-50 text-green-300',
                                'aprobado_tribunal' => 'bg-blue-900 bg-opacity-50 text-blue-300',
                                'rechazado' => 'bg-red-900 bg-opacity-50 text-red-300',
                                default => 'bg-gray-900 bg-opacity-50 text-gray-300',
                            };
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $estadoClass }}">
                            {{ ucfirst(str_replace('_', ' ', $doc->estado->nombre ?? 'no_entregado')) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium space-x-2">
                        @if($doc->estado->nombre === 'entregado')
                            <a href="{{ route('tutor.revisar', $doc->id) }}" 
                               class="bg-red-700 text-white px-3 py-1 rounded hover:bg-red-600 transition" title="Revisar">
                                <i class="fas fa-eye"></i>
                            </a>
                        @else
                            <a href="{{ route('tutor.revisar', $doc->id) }}" 
                               class="text-red-400 hover:text-red-300" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                        @endif
                        <a href="{{ asset($doc->archivo_ruta) }}" target="_blank" 
                           class="text-gray-400 hover:text-white" title="Descargar">
                            <i class="fas fa-download"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                        <i class="fas fa-file-alt text-4xl mb-3 text-gray-600"></i>
                        <p class="text-lg">
                            No hay documentos 
                            @if(request('filtro') === 'aprobados') aprobados @else pendientes de revisión @endif
                        </p>
                        <p class="text-sm mt-1">
                            @if(request('filtro') !== 'aprobados')
                                Los estudiantes aún no han subido documentos para revisar
                            @else
                                Los documentos aprobados aparecerán aquí
                            @endif
                        </p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Paginación -->
    @if(isset($documentos) && $documentos->hasPages())
    <div class="px-6 py-4 border-t border-gray-700">
        <div class="flex justify-between items-center">
            <p class="text-sm text-gray-400">
                Mostrando {{ $documentos->firstItem() ?? 0 }}-{{ $documentos->lastItem() ?? 0 }} de {{ $documentos->total() }} documentos
            </p>
            <div class="flex space-x-1">
                {{ $documentos->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
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