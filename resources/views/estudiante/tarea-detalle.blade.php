@extends('layouts.app')

@section('title', 'Detalle de Tarea')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-950 via-red-900 to-red-950">
    <!-- Header -->
    <header class="bg-black/40 backdrop-blur-md border-b border-red-800/30">
        <div class="px-6 py-4">
            <a href="{{ route('estudiante.dashboard') }}" class="text-red-300 hover:text-white flex items-center space-x-2 mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Volver al Dashboard</span>
            </a>
            <h1 class="text-2xl font-bold text-white">{{ $tarea->titulo }}</h1>
        </div>
    </header>

    <div class="p-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Información de la tarea -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Detalles -->
                <div class="bg-gradient-to-br from-red-900/40 to-red-950/40 backdrop-blur-md rounded-xl p-6 border border-red-700/30">
                    <h2 class="text-xl font-bold text-white mb-4">Información de la Tarea</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="text-red-300 text-sm">Descripción</label>
                            <p class="text-white mt-1">{{ $tarea->descripcion }}</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-red-300 text-sm">Fecha Límite</label>
                                <p class="text-white mt-1">{{ \Carbon\Carbon::parse($tarea->fecha_limite)->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <label class="text-red-300 text-sm">Tipo de Documento</label>
                                <p class="text-white mt-1">{{ ucfirst(str_replace('_', ' ', $tarea->tipo_documento)) }}</p>
                            </div>
                        </div>

                        @if($documentoActual)
                            <div>
                                <label class="text-red-300 text-sm">Estado Actual</label>
                                <div class="mt-2">
                                    @if($documentoActual->estado->nombre === 'aprobado_tribunal')
                                        <span class="px-3 py-1 bg-green-600/50 text-green-200 text-sm rounded-full border border-green-500/50">
                                            Aprobado por Tribunal
                                        </span>
                                    @elseif($documentoActual->estado->nombre === 'con_observaciones')
                                        <span class="px-3 py-1 bg-yellow-600/50 text-yellow-200 text-sm rounded-full border border-yellow-500/50">
                                            Con Observaciones
                                        </span>
                                    @elseif($documentoActual->estado->nombre === 'entregado')
                                        <span class="px-3 py-1 bg-blue-600/50 text-blue-200 text-sm rounded-full border border-blue-500/50">
                                            En Revisión
                                        </span>
                                    @elseif($documentoActual->estado->nombre === 'visto_bueno')
                                        <span class="px-3 py-1 bg-indigo-600/50 text-indigo-200 text-sm rounded-full border border-indigo-500/50">
                                            Visto Bueno del Tutor
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Historial de Versiones -->
                @if($tarea->documentos->count() > 0)
                    <div class="bg-gradient-to-br from-red-900/40 to-red-950/40 backdrop-blur-md rounded-xl p-6 border border-red-700/30">
                        <h2 class="text-xl font-bold text-white mb-4">Historial de Entregas</h2>
                        <div class="space-y-3">
                            @foreach($tarea->documentos as $doc)
                                <div class="bg-red-950/50 rounded-lg p-4 border border-red-800/30">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-white font-medium">Versión {{ $doc->version }}</p>
                                                <p class="text-red-300 text-sm">{{ $doc->archivo_nombre_original }}</p>
                                                <p class="text-red-400 text-xs mt-1">
                                                    Subido: {{ $doc->entregado_en ? \Carbon\Carbon::parse($doc->entregado_en)->format('d/m/Y H:i') : 'N/A' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ asset('storage/' . $doc->archivo_ruta) }}" 
                                               target="_blank"
                                               class="p-2 text-red-300 hover:text-white transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            <span class="px-2 py-1 bg-red-800/50 text-red-200 text-xs rounded">
                                                {{ $doc->estado->nombre }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar - Observaciones -->
            <div class="space-y-6">
                <!-- Formulario de subida -->
                @if(!$documentoActual || $documentoActual->estado->nombre === 'con_observaciones')
                    <div class="bg-gradient-to-br from-green-900/40 to-green-950/40 backdrop-blur-md rounded-xl p-6 border border-green-700/30">
                        <h3 class="text-lg font-bold text-white mb-4">Subir Entrega</h3>
                        <form action="{{ route('estudiante.tarea.entregar', $tarea->id) }}" 
                              method="POST" 
                              enctype="multipart/form-data"
                              id="uploadForm">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-red-200 text-sm mb-2">Archivo</label>
                                <input type="file" name="archivo" 
                                       accept=".pdf,.doc,.docx"
                                       required
                                       class="w-full px-4 py-2 bg-red-950/50 border border-red-700/50 rounded-lg text-white focus:outline-none focus:border-green-500">
                            </div>
                            <button type="submit" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition-colors font-medium">
                                {{ $documentoActual ? 'Subir Corrección' : 'Subir Documento' }}
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Observaciones -->
                <div class="bg-gradient-to-br from-red-900/40 to-red-950/40 backdrop-blur-md rounded-xl p-6 border border-red-700/30">
                    <h3 class="text-lg font-bold text-white mb-4">Observaciones</h3>
                    
                    @if($observaciones->count() > 0)
                        <div class="space-y-4">
                            @foreach($observaciones as $obs)
                                <div class="bg-red-950/50 rounded-lg p-4 border border-red-800/30">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <p class="text-white font-medium text-sm">{{ $obs->revisor->nombres }} {{ $obs->revisor->apellidos }}</p>
                                            <p class="text-red-400 text-xs">{{ $obs->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        @if($obs->rol_revisor === 'tutor')
                                            <span class="px-2 py-1 bg-blue-600/30 text-blue-200 text-xs rounded">Tutor</span>
                                        @else
                                            <span class="px-2 py-1 bg-purple-600/30 text-purple-200 text-xs rounded">Tribunal</span>
                                        @endif
                                    </div>
                                    @if($obs->seccion_documento)
                                        <p class="text-red-300 text-xs mb-2">Sección: {{ $obs->seccion_documento }}</p>
                                    @endif
                                    <p class="text-red-100 text-sm">{{ $obs->comentario }}</p>
                                    @if($obs->resuelta)
                                        <p class="text-green-400 text-xs mt-2 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Resuelta
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-red-300 text-sm text-center py-4">No hay observaciones registradas</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection