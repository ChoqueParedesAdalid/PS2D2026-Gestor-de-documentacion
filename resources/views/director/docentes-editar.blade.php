@extends('director.layout')
@section('title', 'Editar Docente - Director')
@section('page-title', 'Editar Docente')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card-dark rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-white mb-6">Editar Información del Docente</h3>
        
        <form action="{{ route('director.docentes.actualizar', $docente->id) }}" method="POST">
            @csrf @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Nombres</label>
                    <input type="text" name="nombres" value="{{ old('nombres', $docente->nombres) }}" required
                           class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Apellidos</label>
                    <input type="text" name="apellidos" value="{{ old('apellidos', $docente->apellidos) }}" required
                           class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Correo Institucional</label>
                    <input type="email" name="email_institucional" value="{{ old('email_institucional', $docente->email_institucional) }}" required
                           class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Rol</label>
                    <select name="role_id" required
                            class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id }}" {{ old('role_id', $docente->role_id) == $rol->id ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $rol->nombre)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Estado</label>
                    <select name="activo" required
                            class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="1" {{ old('activo', $docente->activo) ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ !old('activo', $docente->activo) ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-6 flex space-x-3">
                <a href="{{ route('director.docentes') }}" class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition text-center">
                    Cancelar
                </a>
                <button type="submit" class="flex-1 px-4 py-2 bg-red-700 hover:bg-red-600 text-white rounded-lg transition">
                    Actualizar Docente
                </button>
            </div>
        </form>
    </div>
</div>
@endsection