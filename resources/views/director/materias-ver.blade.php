@extends('director.layout')
@section('title', 'Detalle Materia')
@section('page-title', 'Detalle Materia')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <a href="{{ route('director.materias') }}" class="text-red-400 hover:text-red-300"><i class="fas fa-arrow-left mr-2"></i>Volver</a>
    </div>

    <div class="card-dark rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-white">{{ $materia->nombre }}</h2>
        <p class="text-gray-400">{{ $materia->descripcion }}</p>
        <div class="grid grid-cols-2 gap-4 mt-4 pt-4 border-t border-gray-700">
            <div>
                <span class="text-gray-400">Docente:</span>
                <p class="text-white">{{ $materia->docenteCargo ? $materia->docenteCargo->nombres : 'Sin asignar' }}</p>
            </div>
            <div>
                <span class="text-gray-400">Estudiantes:</span>
                <p class="text-white">{{ $materia->inscripciones->count() }}</p>
            </div>
        </div>
    </div>

    <div class="card-dark rounded-lg shadow p-6">
        <h3 class="text-white mb-4">Estudiantes Inscritos</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-gray-300">
                <thead><tr><th class="text-left pb-2">Nombre</th><th class="text-left pb-2">Proyecto</th><th class="text-left pb-2">Tutor</th></tr></thead>
                <tbody class="divide-y divide-gray-700">
                    @foreach($materia->inscripciones as $ins)
                    <tr>
                        <td class="py-2 text-white">{{ $ins->estudiante->nombres }}</td>
                        <td class="py-2">{{ $ins->titulo_proyecto }}</td>
                        <td class="py-2">{{ $ins->tutores->first()?->nombres ?? 'Sin asignar' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection