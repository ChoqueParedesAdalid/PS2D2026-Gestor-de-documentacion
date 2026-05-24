@extends('tutor.layout')

@section('title', 'Mis Tutorados - Tutor')
@section('page-title', 'Mis Tutorados')

@section('content')
<div class="space-y-6">
    
    <!-- Header con búsqueda -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-white">Lista de Estudiantes Tutorados</h3>
            <p class="text-gray-400 text-sm">{{ $tutorados->total() }} estudiantes asignados</p>
        </div>
        <div class="flex space-x-2">
            <input type="text" 
                   id="search"
                   onkeyup="filterTable()"
                   placeholder="Buscar estudiante..." 
                   class="px-4 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
        </div>
    </div>

    <!-- Tabla de tutorados -->
    <div class="card-dark rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="table-tutorados">
                <thead class="bg-black bg-opacity-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Código</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Estudiante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Proyecto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($tutorados as $estudiante)
                    <tr class="hover:bg-white hover:bg-opacity-5 transition">
                        <td class="px-6 py-4 text-sm text-gray-300">
                            {{ $estudiante->codigo ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-red-700 flex items-center justify-center text-white font-semibold">
                                    {{ substr($estudiante->nombres, 0, 1) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-white">
                                        {{ $estudiante->nombres }} {{ $estudiante->apellidos }}
                                    </div>
                                    <div class="text-xs text-gray-400">{{ $estudiante->email_institucional }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300 max-w-xs">
                            {{ $estudiante->titulo_proyecto ?? 'Sin proyecto asignado' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($estudiante->estado_inscripcion === 'activo')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-900 bg-opacity-50 text-green-300">
                                    <i class="fas fa-check-circle mr-1"></i>Activo
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-900 bg-opacity-50 text-gray-300">
                                    {{ ucfirst($estudiante->estado_inscripcion) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <!-- Ver historial de documentos -->
                            <a href="{{ route('tutor.historial-documentos', $estudiante->id) }}" 
                               class="text-blue-400 hover:text-blue-300 mr-3" 
                               title="Ver historial de documentos">
                                <i class="fas fa-folder"></i>
                            </a>
                            
                            <!-- Revisar documento (si existe) -->
                            @if($estudiante->ultimo_documento)
                            <a href="{{ route('tutor.revisar', $estudiante->ultimo_documento->id) }}" 
                               class="text-green-400 hover:text-green-300" 
                               title="Revisar documento">
                                <i class="fas fa-tasks"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-user-graduate text-4xl mb-3"></i>
                            <p>No tienes estudiantes tutorados asignados</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if($tutorados->hasPages())
        <div class="px-6 py-4 border-t border-gray-700">
            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-400">
                    Mostrando {{ $tutorados->firstItem() ?? 0 }}-{{ $tutorados->lastItem() ?? 0 }} de {{ $tutorados->total() }} estudiantes
                </p>
                <div class="flex space-x-1">
                    {{ $tutorados->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Función para buscar/filtrar en la tabla
function filterTable() {
    const input = document.getElementById('search');
    const filter = input.value.toUpperCase();
    const table = document.getElementById('table-tutorados');
    const tr = table.getElementsByTagName('tr');
    
    for (let i = 1; i < tr.length; i++) {
        const tdNombre = tr[i].getElementsByTagName('td')[1]; // Columna Estudiante
        const tdProyecto = tr[i].getElementsByTagName('td')[2]; // Columna Proyecto
        
        if (tdNombre || tdProyecto) {
            const nombreValue = tdNombre ? tdNombre.textContent || tdNombre.innerText : '';
            const proyectoValue = tdProyecto ? tdProyecto.textContent || tdProyecto.innerText : '';
            
            if (nombreValue.toUpperCase().indexOf(filter) > -1 || 
                proyectoValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = '';
            } else {
                tr[i].style.display = 'none';
            }
        }
    }
}
</script>
@endpush
@endsection