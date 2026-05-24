@extends('tribunal.layout')

@section('title', 'Dashboard - Tribunal')
@section('page-title', 'Panel de Control')

@section('content')
<div class="space-y-6">
    
    <!-- STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Documentos Pendientes</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $pendientes ?? 0 }}</p>
                </div>
                <div class="bg-purple-900 bg-opacity-50 rounded-full p-3">
                    <i class="fas fa-clock text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Documentos Aprobados</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $aprobados ?? 0 }}</p>
                </div>
                <div class="bg-green-900 bg-opacity-50 rounded-full p-3">
                    <i class="fas fa-check-circle text-green-400 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Total Asignados</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $totalDocumentos ?? 0 }}</p>
                </div>
                <div class="bg-blue-900 bg-opacity-50 rounded-full p-3">
                    <i class="fas fa-gavel text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- DOCUMENTOS PENDIENTES -->
    <div class="card-dark rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-white">
                <i class="fas fa-clock text-purple-400 mr-2"></i>Documentos Pendientes de Revisión
            </h3>
            <a href="{{ route('tribunal.documentos') }}" class="text-red-400 hover:text-red-300 text-sm">
                Ver todos <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="p-6">
            @if(isset($actividadReciente) && $actividadReciente->isNotEmpty())
                <div class="space-y-4">
                    @foreach($actividadReciente as $doc)
                    <div class="flex items-start space-x-4 p-3 hover:bg-white hover:bg-opacity-5 rounded-lg transition">
                        <div class="bg-red-900 bg-opacity-50 rounded-full p-2 flex-shrink-0">
                            <i class="fas fa-file-pdf text-red-400"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">
                                {{ $doc->estudiante->nombres }} {{ $doc->estudiante->apellidos }} - {{ $doc->archivo_nombre_original }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">{{ $doc->tarea->titulo ?? 'Proyecto de grado' }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-history mr-1"></i>Entregado: {{ $doc->entregado_en?->format('d/m/Y') }}
                            </p>
                        </div>
                        <a href="{{ route('tribunal.revisar', $doc->id) }}" class="text-purple-400 hover:text-purple-300 text-sm font-medium">
                            Revisar
                        </a>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-center py-12">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p>No hay documentos pendientes de revisión</p>
                </p>
            @endif
        </div>
    </div>
</div>
@endsection