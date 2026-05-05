@extends('tutor.layout')

@section('title', 'Dashboard - Tutor')
@section('page-title', 'Panel de Control')

@section('content')
<div class="space-y-6">
    
    <!-- STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Total Tutorados</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $totalTutorados ?? 12 }}</p>
                </div>
                <div class="bg-red-900 bg-opacity-50 rounded-full p-3">
                    <i class="fas fa-users text-red-400 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Documentos Pendientes</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $pendientes ?? 8 }}</p>
                </div>
                <div class="bg-yellow-900 bg-opacity-50 rounded-full p-3">
                    <i class="fas fa-clock text-yellow-400 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Documentos Aprobados</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $aprobados ?? 24 }}</p>
                </div>
                <div class="bg-green-900 bg-opacity-50 rounded-full p-3">
                    <i class="fas fa-check-circle text-green-400 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">En Revisión</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $enRevision ?? 5 }}</p>
                </div>
                <div class="bg-purple-900 bg-opacity-50 rounded-full p-3">
                    <i class="fas fa-eye text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- RECENT ACTIVITY & QUICK ACTIONS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- ACTIVIDAD RECIENTE -->
        <div class="lg:col-span-2 card-dark rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white">Actividad Reciente</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @for($i = 0; $i < 5; $i++)
                    <div class="flex items-start space-x-4 p-3 hover:bg-white hover:bg-opacity-10 rounded-lg transition">
                        <div class="bg-red-900 bg-opacity-50 rounded-full p-2">
                            <i class="fas fa-file-upload text-red-400"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-white">Estudiante {{ $i + 1 }} subió un nuevo documento</p>
                            <p class="text-xs text-gray-400">Proyecto de Grado - Capítulo {{ $i + 1 }}</p>
                            <p class="text-xs text-gray-500 mt-1">Hace {{ $i + 1 }} hora{{ $i > 0 ? 's' : '' }}</p>
                        </div>
                        <button class="text-red-400 hover:text-red-300 text-sm font-medium">
                            Revisar
                        </button>
                    </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- ACCIONES RÁPIDAS -->
        <div class="card-dark rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white">Acciones Rápidas</h3>
            </div>
            <div class="p-6 space-y-3">
                <a href="{{ route('tutor.tutorados') }}" 
                   class="flex items-center p-3 bg-black bg-opacity-50 hover:bg-opacity-70 rounded-lg transition">
                    <i class="fas fa-users text-red-400 mr-3"></i>
                    <span class="text-white font-medium">Ver Tutorados</span>
                </a>
                
                <a href="{{ route('tutor.documentos') }}" 
                   class="flex items-center p-3 bg-black bg-opacity-50 hover:bg-opacity-70 rounded-lg transition">
                    <i class="fas fa-file-alt text-yellow-400 mr-3"></i>
                    <span class="text-white font-medium">Documentos Pendientes</span>
                </a>
                
                <a href="{{ route('tutor.revisar') }}" 
                   class="flex items-center p-3 bg-black bg-opacity-50 hover:bg-opacity-70 rounded-lg transition">
                    <i class="fas fa-tasks text-green-400 mr-3"></i>
                    <span class="text-white font-medium">Revisar Documentos</span>
                </a>
                
                <button class="w-full flex items-center p-3 bg-black bg-opacity-50 hover:bg-opacity-70 rounded-lg transition">
                    <i class="fas fa-chart-bar text-purple-400 mr-3"></i>
                    <span class="text-white font-medium">Generar Reporte</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection