@extends('tutor.layout')

@section('title', 'Historial de Documentos - Tutor')
@section('page-title', 'Historial de Documentos')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    
    <!-- Header -->
    <div class="flex justify-between items-center">
        <a href="{{ route('tutor.tutorados') }}" 
           class="text-red-400 hover:text-red-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Volver a tutorados
        </a>
        <button onclick="generarReportePDF()" 
                class="bg-red-700 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition shadow-lg">
            <i class="fas fa-file-pdf mr-2"></i>Generar Reporte PDF
        </button>
    </div>

    <!-- Información del estudiante -->
    <div class="card-dark rounded-lg shadow p-6">
        <div class="flex items-center space-x-4">
            <div class="h-16 w-16 rounded-full bg-red-700 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                {{ strtoupper(substr($estudiante->nombres, 0, 1)) }}
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-white">{{ $estudiante->nombres }} {{ $estudiante->apellidos }}</h2>
                <p class="text-gray-400">{{ $estudiante->email_institucional }}</p>
                
                <p class="text-sm mt-2">
                    <i class="fas fa-book mr-1 text-red-400"></i>
                    <span class="text-gray-400">Proyecto:</span>
                    @if($inscripcion && $inscripcion->titulo_proyecto)
                        <span class="text-white font-medium">{{ $inscripcion->titulo_proyecto }}</span>
                    @else
                        <span class="text-gray-500 italic">Sin título asignado</span>
                    @endif
                </p>
                
                @if($inscripcion)
                    <div class="flex flex-wrap gap-4 mt-2">
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-graduation-cap mr-1"></i>
                            Materia: {{ $inscripcion->materia->nombre ?? 'N/A' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Estado: 
                            <span class="text-{{ $inscripcion->estado_inscripcion === 'activo' ? 'green' : 'gray' }}-400">
                                {{ ucfirst($inscripcion->estado_inscripcion) }}
                            </span>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Historial de documentos -->
    <div class="card-dark rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-white">
                <i class="fas fa-history mr-2"></i>Historial de Documentos Entregados
            </h3>
            <span class="text-sm text-gray-400">
                {{ $documentos->count() }} documento(s) en total
            </span>
        </div>
        
        @if($documentos->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-black bg-opacity-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Documento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Versión</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Fecha de entrega</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($documentos as $index => $doc)
                        <tr class="hover:bg-white hover:bg-opacity-5 transition">
                            <td class="px-6 py-4 text-gray-500 text-sm">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="text-white font-medium">{{ $doc->archivo_nombre_original }}</div>
                                <div class="text-sm text-gray-400">
                                    <i class="fas fa-tasks text-xs mr-1"></i>
                                    {{ $doc->tarea->titulo ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $doc->tamaño_formateado }}
                                </div>
                            </td>
                            
                            {{-- ✅ VERSIÓN MEJORADA --}}
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold
                                    {{ $doc->version == 1 ? 'bg-red-900 bg-opacity-40 text-red-300 border border-red-700' : 
                                       ($doc->version == 2 ? 'bg-orange-900 bg-opacity-40 text-orange-300 border border-orange-700' : 
                                       ($doc->version >= 5 ? 'bg-purple-900 bg-opacity-40 text-purple-300 border border-purple-700' : 
                                       'bg-blue-900 bg-opacity-40 text-blue-300 border border-blue-700')) }}">
                                    <i class="fas fa-code-branch mr-1.5 text-[10px]"></i>
                                    v{{ $doc->version }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 text-gray-300 text-sm">
                                {{ $doc->entregado_en->format('d/m/Y') }}
                                <div class="text-xs text-gray-500">{{ $doc->entregado_en->format('H:i') }}</div>
                            </td>
                            
                           
                            <td class="px-6 py-4">
                                @php
                                    $estadoNombre = $doc->estado->nombre ?? 'desconocido';
                                    $estadoConfig = match($estadoNombre) {
                                        'aprobado_tribunal' => [
                                            'clase' => 'bg-green-900 bg-opacity-40 text-green-300 border border-green-700',
                                            'icono' => 'fa-trophy'
                                        ],
                                        'visto_bueno' => [
                                            'clase' => 'bg-blue-900 bg-opacity-40 text-blue-300 border border-blue-700',
                                            'icono' => 'fa-check-circle'
                                        ],
                                        'con_observaciones' => [
                                            'clase' => 'bg-orange-900 bg-opacity-40 text-orange-300 border border-orange-700',
                                            'icono' => 'fa-exclamation-triangle'
                                        ],
                                        'entregado' => [
                                            'clase' => 'bg-yellow-900 bg-opacity-40 text-yellow-300 border border-yellow-700',
                                            'icono' => 'fa-clock'
                                        ],
                                        default => [
                                            'clase' => 'bg-gray-700 text-gray-300',
                                            'icono' => 'fa-file'
                                        ]
                                    };
                                @endphp
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold {{ $estadoConfig['clase'] }}">
                                    <i class="fas {{ $estadoConfig['icono'] }} mr-1.5"></i>
                                    {{ ucfirst(str_replace('_', ' ', $estadoNombre)) }}
                                </span>
                            </td>
                            
                            {{-- ✅ ACCIONES MEJORADAS --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ asset('storage/' . $doc->archivo_ruta) }}" 
                                       target="_blank"
                                       class="inline-flex items-center px-3 py-1.5 bg-blue-900 bg-opacity-30 hover:bg-blue-800 hover:bg-opacity-50 text-blue-300 text-xs rounded-lg transition border border-blue-800 hover:border-blue-600">
                                        <i class="fas fa-download mr-1.5"></i>Descargar
                                    </a>
                                    <a href="{{ route('tutor.revisar', $doc->id) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-green-900 bg-opacity-30 hover:bg-green-800 hover:bg-opacity-50 text-green-300 text-xs rounded-lg transition border border-green-800 hover:border-green-600">
                                        <i class="fas fa-eye mr-1.5"></i>Revisar
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <div class="bg-gray-800 bg-opacity-50 rounded-full p-4 inline-block mb-4">
                    <i class="fas fa-inbox text-gray-500 text-4xl"></i>
                </div>
                <p class="text-gray-400 text-lg">No hay documentos entregados aún</p>
                <p class="text-gray-500 text-sm mt-2">Este estudiante aún no ha subido ningún documento.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function generarReportePDF() {
    window.open('{{ route("tutor.reporte-pdf", $estudiante->id) }}', '_blank');
}
</script>
@endpush
@endsection