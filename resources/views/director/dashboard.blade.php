@extends('director.layout')

@section('title', 'Panel de Control - Director')
@section('page-title', 'Panel de Control')

@section('content')
<div class="space-y-6">
    
    <!-- STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Card 1: Materias (antes Paralelos) -->
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Total Materias</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $totalParalelos ?? 0 }}</p>
                </div>
                <div class="bg-red-900 bg-opacity-50 rounded-full p-3">
                    <i class="fas fa-book text-red-400 text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Card 2: Docentes -->
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Docentes Registrados</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $docentesRegistrados ?? 0 }}</p>
                </div>
                <div class="bg-yellow-900 bg-opacity-50 rounded-full p-3">
                    <i class="fas fa-chalkboard-teacher text-yellow-400 text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Card 3: Estudiantes -->
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Estudiantes Activos</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $estudiantesActivos ?? 0 }}</p>
                </div>
                <div class="bg-green-900 bg-opacity-50 rounded-full p-3">
                    <i class="fas fa-user-graduate text-green-400 text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Card 4: Inscripciones -->
        <div class="card-dark rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm">Inscripciones Activas</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $inscripcionesActivas ?? 0 }}</p>
                </div>
                <div class="bg-purple-900 bg-opacity-50 rounded-full p-3">
                    <i class="fas fa-clipboard-list text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ACTIVIDAD RECIENTE Y ACCIONES RÁPIDAS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
<div class="lg:col-span-2 card-dark rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-700">
        <h3 class="text-lg font-semibold text-white">
            <i class="fas fa-clock text-red-400 mr-2"></i>Actividad Reciente
        </h3>
    </div>
    <div class="p-6">
        <div class="space-y-4">
            @forelse($actividadReciente ?? [] as $actividad)
            
            @empty
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-inbox text-4xl mb-3"></i>
                <p>No hay actividad reciente</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

        <!-- ACCIONES RÁPIDAS -->
        <div class="card-dark rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white">
                    <i class="fas fa-bolt text-yellow-400 mr-2"></i>Acciones Rápidas
                </h3>
            </div>
            <div class="p-6 space-y-3">
                <a href="{{ route('director.materias') }}" 
                   class="flex items-center p-3 bg-black bg-opacity-50 hover:bg-opacity-70 rounded-lg transition">
                    <i class="fas fa-book text-red-400 mr-3"></i>
                    <span class="text-white font-medium">Ver Materias</span>
                </a>
                
                <button onclick="document.getElementById('modalCrearMateria').classList.remove('hidden')" 
                        class="w-full flex items-center p-3 bg-black bg-opacity-50 hover:bg-opacity-70 rounded-lg transition">
                    <i class="fas fa-plus text-green-400 mr-3"></i>
                    <span class="text-white font-medium">Crear Materia</span>
                </button>
                
                <a href="{{ route('director.docentes') }}" 
                   class="flex items-center p-3 bg-black bg-opacity-50 hover:bg-opacity-70 rounded-lg transition">
                    <i class="fas fa-chalkboard-teacher text-blue-400 mr-3"></i>
                    <span class="text-white font-medium">Docentes Registrados</span>
                </a>
                
                <a href="{{ route('director.reportes') }}" 
                   class="flex items-center p-3 bg-black bg-opacity-50 hover:bg-opacity-70 rounded-lg transition">
                    <i class="fas fa-chart-bar text-purple-400 mr-3"></i>
                    <span class="text-white font-medium">Generar Reporte</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Materia -->
<div id="modalCrearMateria" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center">
    <div class="bg-gray-900 rounded-lg p-6 max-w-md w-full mx-4 border border-gray-700">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-white">Crear Nueva Materia</h3>
            <button onclick="document.getElementById('modalCrearMateria').classList.add('hidden')" 
                    class="text-gray-400 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <!-- ✅ Ruta corregida para coincidir con el controlador -->
        <form action="{{ route('director.materias.crear') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Nombre de la Materia</label>
                    <input type="text" name="nombre" required 
                           class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500"
                           placeholder="Ej: Proyecto de Sistemas 2">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Descripción</label>
                    <textarea name="descripcion" rows="3" 
                              class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500"
                              placeholder="Descripción de la materia..."></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Semestre</label>
                    <select name="semestre" required 
                            class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="7mo">7mo Semestre</option>
                        <option value="8vo">8vo Semestre</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Gestión Académica</label>
                    <select name="gestion_id" required 
                            class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        @foreach(\App\Models\Gestion::where('activa', true)->get() as $gestion)
                            <option value="{{ $gestion->id }}">{{ $gestion->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Opcional: Asignar docente al crear -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Docente a Cargo (Opcional)</label>
                    <select name="docente_cargo_id" 
                            class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">Sin asignar</option>
                        @foreach(\App\Models\User::whereHas('rol', fn($q)=>$q->where('nombre','docente_cargo'))->where('activo',true)->get() as $docente)
                            <option value="{{ $docente->id }}">{{ $docente->nombres }} {{ $docente->apellidos }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="mt-6 flex space-x-3">
                <button type="button" onclick="document.getElementById('modalCrearMateria').classList.add('hidden')" 
                        class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                    Cancelar
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-red-700 hover:bg-red-600 text-white rounded-lg transition">
                    Crear Materia
                </button>
            </div>
        </form>
    </div>
</div>
@endsection