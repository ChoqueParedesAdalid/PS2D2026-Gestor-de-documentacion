@extends('tutor.layout')

@section('title', 'Documentos - Tutor')
@section('page-title', 'Lista de Documentos')

@section('content')
<div class="card-dark rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-700">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-white">Gestión de Documentos</h3>
            
            <!-- TABS -->
            <div class="flex space-x-2">
                <button onclick="switchTab('pendientes')" id="tab-pendientes" 
                        class="px-4 py-2 bg-yellow-900 bg-opacity-50 text-yellow-300 rounded-lg font-medium hover:bg-opacity-70 transition">
                    <i class="fas fa-clock mr-2"></i>Pendientes
                </button>
                <button onclick="switchTab('aprobados')" id="tab-aprobados" 
                        class="px-4 py-2 bg-black bg-opacity-50 text-gray-300 rounded-lg font-medium hover:bg-opacity-70 transition">
                    <i class="fas fa-check-circle mr-2"></i>Aprobados
                </button>
            </div>
        </div>
        
        <!-- FILTROS -->
        <div class="flex space-x-4">
            <input type="text" placeholder="Buscar documento..." 
                   class="flex-1 px-4 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500">
            
        </div>
    </div>
    
    <!-- TABLA PENDIENTES -->
    <div id="content-pendientes" class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-black bg-opacity-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Estudiante</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Documento</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Versión</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Fecha Entrega</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @for($i = 0; $i < 6; $i++)
                <tr class="hover:bg-white hover:bg-opacity-5 transition">
                    <td class="px-6 py-4 text-sm font-medium text-white">
                        Estudiante {{ $i + 1 }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-300">
                        <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                        Capítulo {{ $i + 1 }} - Proyecto.pdf
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-400">
                        v{{ $i + 1 }}.0
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-400">
                        {{ now()->subDays($i)->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-900 bg-opacity-50 text-yellow-300">
                            Pendiente
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium">
                        <!-- El botón de revisar redirige a la página de revisión del documento -->
                        <a href="{{ route('tutor.revisar') }}" 
                           class="bg-red-700 text-white px-3 py-1 rounded hover:bg-red-600 transition mr-2">
                            <i class="fas fa-eye mr-1"></i> Revisar
                        </a>
                        <button class="text-gray-400 hover:text-white">
                            <i class="fas fa-download"></i>
                        </button>
                    </td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>
    
    <!-- TABLA APROBADOS (oculta por defecto) -->
    <div id="content-aprobados" class="overflow-x-auto hidden">
        <table class="w-full">
            <thead class="bg-black bg-opacity-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Estudiante</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Documento</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Versión</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Fecha Aprobación</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @for($i = 0; $i < 4; $i++)
                <tr class="hover:bg-white hover:bg-opacity-5 transition">
                    <td class="px-6 py-4 text-sm font-medium text-white">
                        Estudiante {{ $i + 1 }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-300">
                        <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                        Capítulo {{ $i + 1 }} - Proyecto.pdf
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-400">
                        v{{ $i + 2 }}.0
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-400">
                        {{ now()->subDays($i + 7)->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-900 bg-opacity-50 text-green-300">
                            Aprobado
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium">
                        <button class="text-red-400 hover:text-red-300 mr-2">
                            <i class="fas fa-eye mr-1"></i> Ver
                        </button>
                        <button class="text-gray-400 hover:text-white">
                            <i class="fas fa-download"></i>
                        </button>
                    </td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
function switchTab(tab) {
    document.getElementById('content-pendientes').classList.add('hidden');
    document.getElementById('content-aprobados').classList.add('hidden');
    
    document.getElementById('tab-pendientes').classList.remove('bg-yellow-900', 'bg-opacity-50', 'text-yellow-300');
    document.getElementById('tab-pendientes').classList.add('bg-black', 'bg-opacity-50', 'text-gray-300');
    document.getElementById('tab-aprobados').classList.remove('bg-green-900', 'bg-opacity-50', 'text-green-300');
    document.getElementById('tab-aprobados').classList.add('bg-black', 'bg-opacity-50', 'text-gray-300');
    
    document.getElementById('content-' + tab).classList.remove('hidden');
    document.getElementById('tab-' + tab).classList.remove('bg-black', 'bg-opacity-50', 'text-gray-300');
    
    if(tab === 'pendientes') {
        document.getElementById('tab-pendientes').classList.add('bg-yellow-900', 'bg-opacity-50', 'text-yellow-300');
    } else {
        document.getElementById('tab-aprobados').classList.add('bg-green-900', 'bg-opacity-50', 'text-green-300');
    }
}
</script>
@endpush
@endsection