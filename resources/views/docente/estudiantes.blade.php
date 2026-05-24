@extends('docente.layout')
@section('title', 'Estudiantes - Docente a Cargo')
@section('page-title', 'Gestión de Estudiantes')

@section('content')
<div class="space-y-6">
    
    <!-- Header con información de la materia -->
    <div class="card-dark rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-xl font-semibold text-white">{{ $materia->nombre }}</h3>
                <p class="text-gray-400 mt-1">{{ $materia->descripcion }}</p>
                <p class="text-sm text-gray-500 mt-2">
                    <i class="fas fa-users mr-1"></i> {{ $inscripciones->count() }} estudiantes inscritos
                </p>
            </div>
            <button onclick="document.getElementById('modalRegistrarEstudiante').classList.remove('hidden')" 
                    class="bg-red-700 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>Agregar Estudiante
            </button>
        </div>
    </div>

    <!-- Instrucciones del flujo -->
    <div class="bg-blue-900 bg-opacity-30 border border-blue-700 rounded-lg p-4 mb-6">
        <p class="text-blue-300 text-sm">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Flujo recomendado:</strong> Primero agrega a todos tus estudiantes. Luego, haz clic en "Asignar" para configurar tutor, jurados y título del proyecto de cada uno.
        </p>
    </div>

    <!-- Tabla de estudiantes -->
    <div class="card-dark rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-black bg-opacity-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Estudiante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Proyecto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Tutor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Jurados</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($inscripciones as $inscripcion)
                    <tr class="hover:bg-white hover:bg-opacity-5 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-red-700 flex items-center justify-center text-white font-semibold">
                                    {{ substr($inscripcion->estudiante->nombres, 0, 1) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-white">
                                        {{ $inscripcion->estudiante->nombres }} {{ $inscripcion->estudiante->apellidos }}
                                    </div>
                                    <div class="text-xs text-gray-400">{{ $inscripcion->estudiante->email_institucional }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300 max-w-xs">
                            {{ $inscripcion->titulo_proyecto ?? 'Sin título' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($inscripcion->tutores->first())
                                <span class="text-green-400"><i class="fas fa-check-circle mr-1"></i>Asignado</span>
                            @else
                                <span class="text-yellow-400">Pendiente</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300">
                            {{ $inscripcion->tribunales->count() }}/2
                            @if($inscripcion->tribunales->count() >= 2)
                                <span class="text-green-400 ml-1"><i class="fas fa-check"></i></span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($inscripcion->tutores->first() && $inscripcion->tribunales->count() >= 2 && $inscripcion->titulo_proyecto)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-900 bg-opacity-50 text-green-300">
                                    <i class="fas fa-check mr-1"></i>Completo
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-900 bg-opacity-50 text-yellow-300">
                                    <i class="fas fa-clock mr-1"></i>Pendiente
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <button onclick="abrirModalAsignaciones({{ $inscripcion->id }}, '{{ addslashes($inscripcion->titulo_proyecto ?? '') }}', {{ $inscripcion->tutores->first()?->id ?? 'null' }}, [{{ $inscripcion->tribunales->pluck('id')->join(',') }}])" 
                                    class="text-blue-400 hover:text-blue-300 font-medium" title="Asignar tutor, jurados y título">
                                <i class="fas fa-cog mr-1"></i>Asignar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-user-graduate text-4xl mb-3"></i>
                            <p class="text-lg">No hay estudiantes inscritos aún</p>
                            <p class="text-sm mt-2">Haz clic en "Agregar Estudiante" para comenzar</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Registrar Estudiante -->
<div id="modalRegistrarEstudiante" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center">
    <div class="bg-gray-900 rounded-lg p-6 max-w-md w-full mx-4 border border-gray-700">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-white">Agregar Estudiante</h3>
            <button onclick="document.getElementById('modalRegistrarEstudiante').classList.add('hidden')" class="text-gray-400 hover:text-white"><i class="fas fa-times text-xl"></i></button>
        </div>
        <form action="{{ route('docente.estudiantes.registrar') }}" method="POST">
            @csrf
            <input type="hidden" name="materia_id" value="{{ $materia->id }}">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Correo Institucional</label>
                    <input type="email" name="email_institucional" required class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="estudiante@est.univalle.edu">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Título del Proyecto (opcional)</label>
                    <input type="text" name="titulo_proyecto" class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Puedes editarlo después">
                </div>
            </div>
            <div class="mt-6 flex space-x-3">
                <button type="button" onclick="document.getElementById('modalRegistrarEstudiante').classList.add('hidden')" class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">Cancelar</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-red-700 hover:bg-red-600 text-white rounded-lg transition">Agregar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Asignar  -->
<div id="modalAsignaciones" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center">
    <div class="bg-gray-900 rounded-lg p-6 max-w-2xl w-full mx-4 border border-gray-700">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-white">Configurar estudiante</h3>
            <button onclick="cerrarModalAsignaciones()" class="text-gray-400 hover:text-white"><i class="fas fa-times text-xl"></i></button>
        </div>
        
        <form id="formAsignaciones">
            @csrf
            <input type="hidden" id="inscripcionId" name="inscripcion_id">
            
            <!-- Título del Proyecto -->
            <div class="mb-6 p-4 bg-black bg-opacity-30 rounded-lg">
                <label class="block text-sm font-medium text-gray-300 mb-2"><i class="fas fa-book mr-2"></i>Título del Proyecto</label>
                <input type="text" id="tituloProyecto" name="titulo_proyecto" class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Ingrese el título del proyecto">
                <button type="button" onclick="actualizarTitulo()" class="mt-2 bg-blue-700 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition">
                    <i class="fas fa-save mr-1"></i>Actualizar Título
                </button>
            </div>

            <!-- Tutor -->
            <div class="mb-4 p-4 bg-black bg-opacity-30 rounded-lg">
                <label class="block text-sm font-medium text-gray-300 mb-2"><i class="fas fa-chalkboard-teacher mr-2 text-blue-400"></i>Tutor</label>
                <div class="flex space-x-2">
                    <select id="tutorSelect" name="tutor_id" class="flex-1 px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">Seleccionar tutor...</option>
                        @foreach($docentesDisponibles as $docente)
                            @if(in_array($docente->rol->nombre, ['docente', 'docente_cargo', 'tutor', 'tribunal']))
                                <option value="{{ $docente->id }}">
                                    {{ $docente->nombres }} {{ $docente->apellidos }} 
                                    ({{ ucfirst(str_replace('_', ' ', $docente->rol->nombre)) }})
                        </option>
                            @endif
                        @endforeach
                    </select>
                    <button type="button" onclick="asignarTutor()" id="btn-asignar-tutor" class="bg-red-700 hover:bg-red-600 text-white px-6 py-2 rounded-lg transition">
                        ASIGNAR
                    </button>
                </div>
            </div>

            <!-- Jurado 1 -->
            <div class="mb-4 p-4 bg-black bg-opacity-30 rounded-lg">
                <label class="block text-sm font-medium text-gray-300 mb-2"><i class="fas fa-gavel mr-2 text-purple-400"></i>Jurado 1</label>
                <div class="flex space-x-2">
                    <select id="jurado1Select" name="tribunal_1_id" class="flex-1 px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">Seleccionar jurado...</option>
                            @foreach($docentesDisponibles as $docente)
                             @if(in_array($docente->rol->nombre, ['docente', 'docente_cargo', 'tutor', 'tribunal']))
                                <option value="{{ $docente->id }}">
                                    {{ $docente->nombres }} {{ $docente->apellidos }} 
                                    ({{ ucfirst(str_replace('_', ' ', $docente->rol->nombre)) }})
                                </option>
                             @endif
                            @endforeach
                    </select>
                    <button type="button" onclick="asignarJurado(1)" id="btn-asignar-jurado1" class="bg-red-700 hover:bg-red-600 text-white px-6 py-2 rounded-lg transition">
                        ASIGNAR
                    </button>
                </div>
            </div>

            <!-- Jurado 2 -->
            <div class="mb-6 p-4 bg-black bg-opacity-30 rounded-lg">
                <label class="block text-sm font-medium text-gray-300 mb-2"><i class="fas fa-gavel mr-2 text-purple-400"></i>Jurado 2</label>
                <div class="flex space-x-2">
                   <select id="jurado2Select" name="tribunal_2_id" class="flex-1 px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">Seleccionar jurado...</option>
                            @foreach($docentesDisponibles as $docente)
                                @if(in_array($docente->rol->nombre, ['docente', 'docente_cargo', 'tutor', 'tribunal']))
                                    <option value="{{ $docente->id }}">
                                        {{ $docente->nombres }} {{ $docente->apellidos }} 
                                        ({{ ucfirst(str_replace('_', ' ', $docente->rol->nombre)) }})
                                    </option>
                                @endif
                            @endforeach
                   </select>
                    <button type="button" onclick="asignarJurado(2)" id="btn-asignar-jurado2" class="bg-red-700 hover:bg-red-600 text-white px-6 py-2 rounded-lg transition">
                        ASIGNAR
                    </button>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="button" onclick="cerrarModalAsignaciones()" class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">Cerrar</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Función para mostrar notificaciones toast
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
    
    if (type === 'success') {
        toast.className += ' bg-green-900 bg-opacity-90 border border-green-600 text-green-100';
    } else if (type === 'error') {
        toast.className += ' bg-red-900 bg-opacity-90 border border-red-600 text-red-100';
    } else if (type === 'warning') {
        toast.className += ' bg-yellow-900 bg-opacity-90 border border-yellow-600 text-yellow-100';
    }
    
    let icon = '';
    if (type === 'success') { icon = '<i class="fas fa-check-circle mr-2"></i>'; }
    else if (type === 'error') { icon = '<i class="fas fa-exclamation-circle mr-2"></i>'; }
    else if (type === 'warning') { icon = '<i class="fas fa-exclamation-triangle mr-2"></i>'; }
    
    toast.innerHTML = `${icon}${message}`;
    document.body.appendChild(toast);
    
    setTimeout(() => { toast.classList.remove('translate-x-full'); }, 100);
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => { toast.remove(); }, 300);
    }, 5000);
}

let currentInscripcionId = null;

// Función para abrir el modal
function abrirModalAsignaciones(inscripcionId, tituloActual, tutorId, tribunalesIds) {
    currentInscripcionId = inscripcionId;
    document.getElementById('inscripcionId').value = inscripcionId;
    document.getElementById('tituloProyecto').value = tituloActual || '';
    
    // Pre-seleccionar tutor si ya tiene uno
    if (tutorId && tutorId !== 'null') {
        document.getElementById('tutorSelect').value = tutorId;
    }
    
    // Pre-seleccionar tribunales si ya tiene
    if (tribunalesIds && tribunalesIds.length > 0) {
        const ids = tribunalesIds.filter(id => id !== '');
        if (ids[0]) document.getElementById('jurado1Select').value = ids[0];
        if (ids[1]) document.getElementById('jurado2Select').value = ids[1];
    }
    
    document.getElementById('modalAsignaciones').classList.remove('hidden');
}

function cerrarModalAsignaciones() {
    document.getElementById('modalAsignaciones').classList.add('hidden');
    currentInscripcionId = null;
}

function actualizarTitulo() {
    if (!currentInscripcionId) {
        showToast('Error: No hay inscripción seleccionada', 'error');
        return;
    }
    const titulo = document.getElementById('tituloProyecto').value;
    const btn = event.target;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Guardando...';
    
    fetch(`/docente-cargo/estudiantes/${currentInscripcionId}/actualizar-proyecto`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ 
            inscripcion_id: currentInscripcionId,
            titulo_proyecto: titulo 
        })
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Error al actualizar');
        return data;
    })
    .then(data => {
        if (data.success) {
            showToast('✅ ' + data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('❌ ' + (data.message || 'Error al actualizar'), 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save mr-1"></i>Actualizar Título';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('⚠️ ' + error.message, 'warning');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save mr-1"></i>Actualizar Título';
    });
}

function asignarTutor() {
    if (!currentInscripcionId) {
        showToast('Error: No hay inscripción seleccionada', 'error');
        return;
    }
    const tutorId = document.getElementById('tutorSelect').value;
    if (!tutorId) { 
        showToast('⚠️ Seleccione un tutor', 'warning'); 
        return; 
    }
    
    const btn = document.getElementById('btn-asignar-tutor');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Asignando...';
    
    fetch(`/docente-cargo/estudiantes/${currentInscripcionId}/asignar-tutor`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ 
            inscripcion_id: currentInscripcionId,
            tutor_id: tutorId 
        })
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Error al asignar tutor');
        return data;
    })
    .then(data => {
        if (data.success) {
            showToast('✅ ' + data.message, 'success');
            // NO recargar - mantener modal abierto
            btn.innerHTML = '<i class="fas fa-check mr-2"></i>Asignado';
            btn.classList.remove('bg-red-700', 'hover:bg-red-600');
            btn.classList.add('bg-green-700', 'hover:bg-green-600');
            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = 'ASIGNAR';
                btn.classList.remove('bg-green-700', 'hover:bg-green-600');
                btn.classList.add('bg-red-700', 'hover:bg-red-600');
            }, 2000);
        } else {
            showToast('❌ ' + (data.message || 'Error al asignar'), 'error');
            btn.disabled = false;
            btn.innerHTML = 'ASIGNAR';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('⚠️ ' + error.message, 'warning');
        btn.disabled = false;
        btn.innerHTML = 'ASIGNAR';
    });
}

function asignarJurado(numero) {
    if (!currentInscripcionId) {
        showToast('Error: No hay inscripción seleccionada', 'error');
        return;
    }
    const selectId = numero === 1 ? 'jurado1Select' : 'jurado2Select';
    const tribunalId = document.getElementById(selectId).value;
    if (!tribunalId) { 
        showToast('⚠️ Seleccione un jurado', 'warning'); 
        return; 
    }
    
    const btn = document.getElementById(numero === 1 ? 'btn-asignar-jurado1' : 'btn-asignar-jurado2');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Asignando...';
    
    fetch(`/docente-cargo/estudiantes/${currentInscripcionId}/asignar-tribunal`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ 
            inscripcion_id: currentInscripcionId,
            tribunal_id: tribunalId 
        })
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Error al asignar jurado');
        return data;
    })
    .then(data => {
        if (data.success) {
            showToast(' ' + data.message, 'success');
            //  mantener modal abierto
            btn.innerHTML = '<i class="fas fa-check mr-2"></i>Asignado';
            btn.classList.remove('bg-red-700', 'hover:bg-red-600');
            btn.classList.add('bg-green-700', 'hover:bg-green-600');
            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = 'ASIGNAR';
                btn.classList.remove('bg-green-700', 'hover:bg-green-600');
                btn.classList.add('bg-red-700', 'hover:bg-red-600');
            }, 2000);
        } else {
            showToast('❌ ' + (data.message || 'Error al asignar'), 'error');
            btn.disabled = false;
            btn.innerHTML = 'ASIGNAR';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('⚠️ ' + error.message, 'warning');
        btn.disabled = false;
        btn.innerHTML = 'ASIGNAR';
    });
}
</script>
@endpush
@endsection