@extends('tribunal.layout')
@section('title', 'Estudiantes - Tribunal')
@section('page-title', 'Estudiantes Asignados')

@section('content')
<div class="space-y-6">
    <div class="card-dark rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-black/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Estudiante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Proyecto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($estudiantes as $est)
                    <tr class="hover:bg-white/5 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-red-700 flex items-center justify-center text-white font-bold">{{ substr($est->nombres, 0, 1) }}</div>
                                <div>
                                    <p class="text-white font-medium">{{ $est->nombres }} {{ $est->apellidos }}</p>
                                    <p class="text-xs text-gray-400">{{ $est->email_institucional }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300">{{ $est->titulo_proyecto ?? 'Sin título' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-900/50 text-green-300">{{ ucfirst($est->estado_inscripcion) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-6 py-12 text-center text-gray-400">No tienes estudiantes asignados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($estudiantes->hasPages()) <div class="px-6 py-4 border-t border-gray-700">{{ $estudiantes->links() }}</div> @endif
    </div>
</div>
@endsection