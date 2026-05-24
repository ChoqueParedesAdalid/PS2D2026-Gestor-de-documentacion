@php
// Mapear nombre de rol a nombre de layout correcto
$rol = auth()->user()->rol?->nombre;
$layoutMap = [
    'estudiante' => 'estudiante',
    'tutor' => 'tutor',
    'tribunal' => 'tribunal',
    'docente_cargo' => 'docente',  // ✅ Corrección clave
    'director' => 'director',
];
$layoutName = $layoutMap[$rol] ?? 'auth.app'; // Fallback por seguridad
@endphp

@extends($layoutName . '.layout')
@section('title', 'Mi Perfil')
@section('page-title', 'Mi Perfil')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Tabs de Navegación -->
    <div class="card-dark rounded-lg shadow">
        <div class="border-b border-gray-700">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="switchProfileTab('informacion')" 
                        id="tab-informacion"
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-red-500 text-red-400">
                    <i class="fas fa-user mr-2"></i>Información Personal
                </button>
                <button onclick="switchProfileTab('seguridad')" 
                        id="tab-seguridad"
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-400 hover:text-gray-300">
                    <i class="fas fa-lock mr-2"></i>Seguridad
                </button>
            </nav>
        </div>

        <!-- Contenido: Información Personal -->
        <div id="content-informacion" class="tab-content p-6">
            @if(session('success'))
                <div class="bg-green-900/70 border border-green-600 text-green-100 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nombres</label>
                        <input type="text" name="nombres" value="{{ old('nombres', $user->nombres) }}" 
                               class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        @error('nombres') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Apellidos</label>
                        <input type="text" name="apellidos" value="{{ old('apellidos', $user->apellidos) }}" 
                               class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        @error('apellidos') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Correo Institucional</label>
                        <input type="email" name="email_institucional" value="{{ old('email_institucional', $user->email_institucional) }}" 
                               class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500" readonly>
                        <p class="text-gray-500 text-xs mt-1">El correo institucional no se puede modificar.</p>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-red-700 hover:bg-red-600 text-white rounded-lg transition">
                        <i class="fas fa-save mr-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>

        <!-- Contenido: Seguridad -->
        <div id="content-seguridad" class="tab-content hidden p-6">
            @if(session('success'))
                <div class="bg-green-900/70 border border-green-600 text-green-100 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            
            <form action="{{ route('profile.password') }}" method="POST">
                @csrf @method('PUT')
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Contraseña Actual</label>
                        <input type="password" name="password_actual" 
                               class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        @error('password_actual') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nueva Contraseña</label>
                        <input type="password" name="password_nuevo" 
                               class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                        @error('password_nuevo') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Confirmar Nueva Contraseña</label>
                        <input type="password" name="password_nuevo_confirmation" 
                               class="w-full px-3 py-2 bg-black bg-opacity-50 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-red-700 hover:bg-red-600 text-white rounded-lg transition">
                        <i class="fas fa-key mr-2"></i>Actualizar Contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function switchProfileTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
    document.querySelectorAll('.tab-button').forEach(b => {
        b.classList.remove('border-red-500', 'text-red-400');
        b.classList.add('border-transparent', 'text-gray-400');
    });
    
    document.getElementById('content-' + tabName).classList.remove('hidden');
    const activeTab = document.getElementById('tab-' + tabName);
    activeTab.classList.remove('border-transparent', 'text-gray-400');
    activeTab.classList.add('border-red-500', 'text-red-400');
}
</script>
@endpush
@endsection