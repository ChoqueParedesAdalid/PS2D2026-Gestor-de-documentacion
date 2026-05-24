@extends('director.layout')

@section('title', 'Docentes - Director')
@section('page-title', 'Gestión de Docentes')

@section('content')
<div class="space-y-6">
    
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <p class="text-gray-300">Administra los docentes y sus roles en el sistema</p>
        </div>
        <button onclick="document.getElementById('modalCrearDocente').classList.remove('hidden')" 
                class="bg-red-700 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i>Registrar Docente
        </button>
    </div>

    <!-- Tabla de docentes -->
    <div class="card-dark rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-black bg-opacity-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Docente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Correo Institucional</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Rol Actual</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Asignar Como</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($docentes as $docente)
                    <tr class="hover:bg-white hover:bg-opacity-5 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-red-700 flex items-center justify-center text-white font-semibold">
                                    {{ substr($docente->nombres, 0, 1) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-white">
                                        {{ $docente->nombres }} {{ $docente->apellidos }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300">
                            {{ $docente->email_institucional }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $docente->rol->nombre === 'tutor' ? 'bg-blue-900 bg-opacity-50 text-blue-300' : 
                                   ($docente->rol->nombre === 'tribunal' ? 'bg-purple-900 bg-opacity-50 text-purple-300' : 
                                   ($docente->rol->nombre === 'docente_cargo' ? 'bg-green-900 bg-opacity-50 text-green-300' : 
                                   ($docente->rol->nombre === 'docente' ? 'bg-gray-700 bg-opacity-50 text-gray-300' : 'bg-gray-900 bg-opacity-50 text-gray-300'))) }}">
                                {{ ucfirst(str_replace('_', ' ', $docente->rol->nombre)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <!-- ✅ CORRECCIÓN: Usar nombre de ruta correcto -->
                            <form action="{{ route('director.docentes.crear') }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="docente_id" value="{{ $docente->id }}">
                                <select name="nuevo_rol" onchange="this.form.submit()" 
                                        class="px-3 py-1 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <option value="">Seleccionar...</option>
                                    <option value="docente_cargo" {{ $docente->rol->nombre === 'docente_cargo' ? 'selected' : '' }}>Docente a Cargo</option>
                                    <option value="tutor" {{ $docente->rol->nombre === 'tutor' ? 'selected' : '' }}>Tutor</option>
                                    <option value="tribunal" {{ $docente->rol->nombre === 'tribunal' ? 'selected' : '' }}>Tribunal</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $docente->activo ? 'bg-green-900 bg-opacity-50 text-green-300' : 'bg-red-900 bg-opacity-50 text-red-300' }}">
                                {{ $docente->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <button class="text-blue-400 hover:text-blue-300 mr-3" title="Ver perfil">
                                <i class="fas fa-user"></i>
                            </button>
                            <button class="text-yellow-400 hover:text-yellow-300 mr-3" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="text-red-400 hover:text-red-300" title="Desactivar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-chalkboard-teacher text-4xl mb-3"></i>
                            <p>No hay docentes registrados</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if(isset($docentes) && $docentes->hasPages())
        <div class="px-6 py-4 border-t border-gray-700">
            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-400">
                    Mostrando {{ $docentes->firstItem() ?? 0 }}-{{ $docentes->lastItem() ?? 0 }} de {{ $docentes->total() }} docentes
                </p>
                <div class="flex space-x-1">
                    {{ $docentes->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal Crear Docente -->
<div id="modalCrearDocente" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center">
    <div class="bg-gray-900 rounded-lg p-6 max-w-md w-full mx-4 border border-gray-700">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-white">Registrar Nuevo Docente</h3>
            <button onclick="document.getElementById('modalCrearDocente').classList.add('hidden')" 
                    class="text-gray-400 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <!-- ✅ CORRECCIÓN: Usar nombre de ruta correcto -->
        <form action="{{ route('director.docentes.crear') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Nombres</label>
                    <input type="text" name="nombres" required 
                           class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500"
                           placeholder="Nombres del docente">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Apellidos</label>
                    <input type="text" name="apellidos" required 
                           class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500"
                           placeholder="Apellidos del docente">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Correo Institucional</label>
                    <input type="email" name="email_institucional" required 
                           class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500"
                           placeholder="docente@univalle.edu">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Rol Inicial</label>
                    <select name="role_id" required 
                            class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">Seleccionar rol...</option>
                        @foreach(\App\Models\Role::whereIn('nombre', ['docente_cargo', 'tutor', 'tribunal', 'docente'])->get() as $rol)
                            <option value="{{ $rol->id }}">{{ ucfirst(str_replace('_', ' ', $rol->nombre)) }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Contraseña Temporal</label>
                    <input type="password" name="password" required 
                           class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500"
                           placeholder="Contraseña inicial">
                    <p class="text-xs text-gray-500 mt-1">El docente podrá cambiarla al iniciar sesión</p>
                </div>
            </div>
            
            <div class="mt-6 flex space-x-3">
                <button type="button" onclick="document.getElementById('modalCrearDocente').classList.add('hidden')" 
                        class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                    Cancelar
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-red-700 hover:bg-red-600 text-white rounded-lg transition">
                    Registrar Docente
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-submit cuando se cambia el rol
document.querySelectorAll('select[name="nuevo_rol"]').forEach(select => {
    select.addEventListener('change', function() {
        if (this.value) {
            this.closest('form').submit();
        }
    });
});
</script>
@endpush