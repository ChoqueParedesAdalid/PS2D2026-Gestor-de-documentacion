@extends('tutor.layout')

@section('title', 'Tutorados - Tutor')
@section('page-title', 'Mis Tutorados')

@section('content')
<div class="card-dark rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-white">Lista de Estudiantes Tutorados</h3>
        <div class="flex space-x-2">
            <input type="text" placeholder="Buscar estudiante..." 
                   class="px-4 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500">
            <button class="bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                <i class="fas fa-search"></i>
            </button>
        </div>
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
                @for($i = 0; $i < 8; $i++)
                <tr class="hover:bg-white hover:bg-opacity-5 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">
                        202100{{ $i + 1 }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-red-900 bg-opacity-50 flex items-center justify-center text-red-400 font-semibold">
                                {{ chr(65 + $i) }}
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-white">Estudiante {{ $i + 1 }}</div>
                                <div class="text-sm text-gray-400">estudiante{{ $i + 1 }}@univalle.edu</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-300">
                        Sistema de Gestión de {{ ['Biblioteca', 'Ventas', 'Inventario', 'Hospital', 'Hotel', 'Restaurante', 'Universidad', 'Farmacia'][$i] }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $i % 3 == 0 ? 'bg-green-900 bg-opacity-50 text-green-300' : ($i % 3 == 1 ? 'bg-yellow-900 bg-opacity-50 text-yellow-300' : 'bg-blue-900 bg-opacity-50 text-blue-300') }}">
                            {{ $i % 3 == 0 ? 'En Progreso' : ($i % 3 == 1 ? 'En Revisión' : 'Aprobado') }}
                        </span>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('tutor.documentos') }}" class="text-red-400 hover:text-red-300 mr-3">
                            <i class="fas fa-folder-open"></i> Documentos
                        </a>
                        <a href="{{ route('tutor.revisar') }}" class="text-green-400 hover:text-green-300">
                            <i class="fas fa-tasks"></i> Revisar
                        </a>
                    </td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-gray-700">
        <div class="flex justify-between items-center">
            <p class="text-sm text-gray-400">Mostrando 1-8 de 12 estudiantes</p>
            <div class="flex space-x-2">
                <button class="px-3 py-1 border border-gray-600 rounded hover:bg-white hover:bg-opacity-10 text-white">Anterior</button>
                <button class="px-3 py-1 bg-red-700 text-white rounded">1</button>
                <button class="px-3 py-1 border border-gray-600 rounded hover:bg-white hover:bg-opacity-10 text-white">2</button>
                <button class="px-3 py-1 border border-gray-600 rounded hover:bg-white hover:bg-opacity-10 text-white">Siguiente</button>
            </div>
        </div>
    </div>
</div>
@endsection