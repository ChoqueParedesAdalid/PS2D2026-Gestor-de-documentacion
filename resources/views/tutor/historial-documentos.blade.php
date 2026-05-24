@extends('tutor.layout')

@section('title', 'Historial de Documentos - Tutor')
@section('page-title', 'Historial de Documentos')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    
    <!-- Header -->
    <div class="flex justify-between items-center">
        <a href="{{ route('tutor.tutorados') }}" 
           class="text-red-400 hover:text-red-300">
            <i class="fas fa-arrow-left mr-2"></i>Volver a tutorados
        </a>
        <button onclick="generarReportePDF()" 
                class="bg-red-700 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-file-pdf mr-2"></i>Generar Reporte PDF
        </button>
    </div>

    <!-- Información del estudiante -->
    <div class="card-dark rounded-lg shadow p-6">
        <div class="flex items-center space-x-4">
            <div class="h-16 w-16 rounded-full bg-red-700 flex items-center justify-center text-white text-2xl font-bold">
                {{ substr($estudiante->nombres, 0, 1) }}
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white">{{ $estudiante->nombres }} {{ $estudiante->apellidos }}</h2>
                <p class="text-gray-400">{{ $estudiante->email_institucional }}</p>
                <p class="text-sm text-gray-500 mt-1">
                    <i class="fas fa-book mr-1"></i>
                    Proyecto: {{ $estudiante->titulo_proyecto ?? 'Sin proyecto asignado' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Historial de documentos -->
    <div class="card-dark rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white">
                <i class="fas fa-history mr-2"></i>Historial de Documentos Entregados
            </h3>
        </div>
        
        @if($documentos->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-black bg-opacity-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Documento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Versión</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Fecha de entrega</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($documentos as $doc)
                        <tr class="hover:bg-white hover:bg-opacity-5">
                            <td class="px-6 py-4">
                                <div class="text-white font-medium">{{ $doc->archivo_nombre_original }}</div>
                                <div class="text-sm text-gray-400">{{ $doc->tarea->titulo ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-300">v{{ $doc->version }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $doc->entregado_en->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $doc->estado->nombre === 'aprobado' ? 'bg-green-900 bg-opacity-50 text-green-300' : 
                                       ($doc->estado->nombre === 'con_observaciones' ? 'bg-yellow-900 bg-opacity-50 text-yellow-300' : 
                                       'bg-blue-900 bg-opacity-50 text-blue-300') }}">
                                    {{ ucfirst($doc->estado->nombre) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ asset('storage/' . $doc->archivo_ruta) }}" 
                                   target="_blank"
                                   class="text-blue-400 hover:text-blue-300 mr-3">
                                    <i class="fas fa-download mr-1"></i>Descargar
                                </a>
                                <a href="{{ route('tutor.revisar', $doc->id) }}" 
                                   class="text-green-400 hover:text-green-300">
                                    <i class="fas fa-eye mr-1"></i>Revisar
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-400 text-center py-12">
                <i class="fas fa-inbox text-4xl mb-3"></i>
                <p>No hay documentos entregados aún</p>
            </p>
        @endif
    </div>
</div>

@push('scripts')
<script>
function generarReportePDF() {
    // Redirigir a la ruta de generación de PDF
    window.location.href = '{{ route("tutor.reporte-pdf", $estudiante->id) }}';
}
</script>
@endpush
@endsection