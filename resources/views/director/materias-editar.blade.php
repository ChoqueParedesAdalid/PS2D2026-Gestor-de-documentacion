@extends('director.layout')
@section('title', 'Editar Materia')
@section('page-title', 'Editar Materia')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card-dark rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-white mb-6">Editar Materia</h3>
        <form action="{{ route('director.materias.actualizar', $materia->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Nombre de la Materia</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $materia->nombre) }}" required 
                           class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Descripción</label>
                    <textarea name="descripcion" rows="3"
                              class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('descripcion', $materia->descripcion) }}</textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Semestre</label>
                    <select name="semestre" required
                            class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="7mo" {{ old('semestre', $materia->semestre_requerido) == '7mo' ? 'selected' : '' }}>7mo Semestre</option>
                        <option value="8vo" {{ old('semestre', $materia->semestre_requerido) == '8vo' ? 'selected' : '' }}>8vo Semestre</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Gestión Académica</label>
                    <select name="gestion_id" required
                            class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        @foreach($gestiones as $gestion)
                            <option value="{{ $gestion->id }}" {{ old('gestion_id', $materia->gestion_id) == $gestion->id ? 'selected' : '' }}>
                                {{ $gestion->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Docente a Cargo</label>
                    <select name="docente_cargo_id"
                            class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">Sin asignar</option>
                        @foreach($docentes as $docente)
                            <option value="{{ $docente->id }}" {{ old('docente_cargo_id', $materia->docente_cargo_id) == $docente->id ? 'selected' : '' }}>
                                {{ $docente->nombres }} {{ $docente->apellidos }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">El docente asignado podrá gestionar estudiantes en esta materia</p>
                </div>
            </div>
            
            <div class="mt-6 flex space-x-3">
                <a href="{{ route('director.materias') }}" class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition text-center">
                    Cancelar
                </a>
                <button type="submit" class="flex-1 px-4 py-2 bg-red-700 hover:bg-red-600 text-white rounded-lg transition">
                    Actualizar Materia
                </button>
            </div>
        </form>
    </div>
</div>
@endsection