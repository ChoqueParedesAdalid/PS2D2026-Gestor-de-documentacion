@extends('director.layout')

@section('title', 'Materias - Director')
@section('page-title', 'Gestión de Materias')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <p class="text-gray-300">Administra las materias y asigna docentes a cargo</p>
        </div>
        <button onclick="document.getElementById('modalCrearMateria').classList.remove('hidden')" class="bg-red-700 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i>Crear Materia
        </button>
    </div>

    <div class="card-dark rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-black bg-opacity-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Materia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Semestre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Docente a Cargo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Estudiantes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($materias as $materia)
                    <tr class="hover:bg-white hover:bg-opacity-5 transition">
                        <td class="px-6 py-4 text-sm font-medium text-white">{{ $materia->nombre }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $materia->semestre_requerido === '7mo' ? 'bg-blue-900 bg-opacity-50 text-blue-300' : 'bg-purple-900 bg-opacity-50 text-purple-300' }}">
                                {{ $materia->semestre_requerido }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-white">
                            @if($materia->docenteCargo)
                                <span class="text-green-400"><i class="fas fa-check-circle mr-1"></i>{{ $materia->docenteCargo->nombres }}</span>
                            @else
                                <span class="text-yellow-400">Sin asignar</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-white">{{ $materia->inscripciones->count() }}</td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('director.materias.ver', $materia->id) }}" class="text-blue-400 hover:text-blue-300 mr-3" title="Ver"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('director.materias.editar', $materia->id) }}" class="text-yellow-400 hover:text-yellow-300 mr-3" title="Editar"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">No hay materias registradas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Crear -->
<div id="modalCrearMateria" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center">
    <div class="bg-gray-900 rounded-lg p-6 max-w-md w-full mx-4 border border-gray-700">
        <h3 class="text-lg font-semibold text-white mb-4">Crear Materia</h3>
        <form action="{{ route('director.materias.crear') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <input type="text" name="nombre" required class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white" placeholder="Nombre (Ej: Proyecto Sistemas 1)">
                <select name="semestre" required class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white">
                    <option value="7mo">7mo Semestre</option>
                    <option value="8vo">8vo Semestre</option>
                </select>
                <select name="gestion_id" required class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white">
                    @foreach(\App\Models\Gestion::where('activa', true)->get() as $g)
                        <option value="{{ $g->id }}">{{ $g->nombre }}</option>
                    @endforeach
                </select>
                <select name="docente_cargo_id" class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white">
                    <option value="">Asignar Docente a Cargo (Opcional)</option>
                    @foreach(\App\Models\User::whereHas('rol', fn($q)=>$q->where('nombre','docente_cargo'))->where('activo',true)->get() as $d)
                        <option value="{{ $d->id }}">{{ $d->nombres }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mt-6 flex space-x-3">
                <button type="button" onclick="document.getElementById('modalCrearMateria').classList.add('hidden')" class="flex-1 px-4 py-2 bg-gray-700 text-white rounded-lg">Cancelar</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-red-700 text-white rounded-lg">Crear</button>
            </div>
        </form>
    </div>
</div>
@endsection