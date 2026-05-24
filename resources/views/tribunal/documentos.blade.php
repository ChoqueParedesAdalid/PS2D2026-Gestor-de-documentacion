@extends('tribunal.layout')

@section('title', 'Documentos - Tribunal')
@section('page-title', 'Documentos para Revisión')

@section('content')
<div class="space-y-6">
    
    <!-- FILTROS -->
    <div class="card-dark rounded-lg shadow p-2">
        <div class="flex space-x-2">
            <a href="{{ route('tribunal.documentos', ['filtro' => 'pendientes']) }}" 
               class="flex-1 px-4 py-3 rounded-lg text-center transition {{ $filtro === 'pendientes' ? 'bg-purple-900 bg-opacity-70 text-white' : 'text-gray-400 hover:bg-black hover:bg-opacity-50' }}">
                <i class="fas fa-clock mr-2"></i>Pendientes ({{ $documentos->total() }})
            </a>
            <a href="{{ route('tribunal.documentos', ['filtro' => 'aprobados']) }}" 
               class="flex-1 px-4 py-3 rounded-lg text-center transition {{ $filtro === 'aprobados' ? 'bg-green-900 bg-opacity-70 text-white' : 'text-gray-400 hover:bg-black hover:bg-opacity-50' }}">
                <i class="fas fa-check-circle mr-2"></i>Aprobados
            </a>
        </div>
    </div>

    <!-- LISTA DE DOCUMENTOS -->
    <div class="card-dark rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-black bg-opacity-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Estudiante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Documento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Tarea</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Fecha Entrega</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($documentos as $doc)
                    <tr class="hover:bg-white hover:bg-opacity-5 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-red-700 flex items-center justify-center text-white font-semibold">
                                    {{ substr($doc->estudiante->nombres, 0, 1) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-white">
                                        {{ $doc->estudiante->nombres }} {{ $doc->estudiante->apellidos }}
                                    </div>
                                    <div class="text-xs text-gray-400">{{ $doc->estudiante->email_institucional }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300">
                            {{ $doc->archivo_nombre_original }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300">
                            {{ $doc->tarea->titulo ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300">
                            {{ $doc->entregado_en?->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($doc->estado->nombre === 'visto_bueno')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-900 bg-opacity-50 text-purple-300">
                                    <i class="fas fa-clock mr-1"></i>Pendiente revisión
                                </span>
                            @elseif($doc->estado->nombre === 'aprobado_tribunal')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-900 bg-opacity-50 text-green-300">
                                    <i class="fas fa-check-circle mr-1"></i>Aprobado
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('tribunal.revisar', $doc->id) }}" 
                               class="text-purple-400 hover:text-purple-300" title="Revisar documento">
                                <i class="fas fa-eye mr-2"></i>Revisar
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-3"></i>
                            <p>No hay documentos para revisar</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if($documentos->hasPages())
        <div class="px-6 py-4 border-t border-gray-700">
            {{ $documentos->links() }}
        </div>
        @endif
    </div>
</div>
@endsection