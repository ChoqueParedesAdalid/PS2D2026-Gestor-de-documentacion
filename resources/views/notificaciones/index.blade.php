@extends('tutor.layout') {{-- O el layout correspondiente al rol --}}

@section('title', 'Notificaciones')
@section('page-title', 'NOTIFICACIONES')

@section('content')
<div class="card-dark rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-white">Todas tus notificaciones</h3>
        <button onclick="marcarTodasLeidas()" class="text-red-400 hover:text-red-300 text-sm">
            Marcar todas como leídas
        </button>
    </div>
    
    <div class="divide-y divide-gray-700">
        @forelse($notificaciones as $notif)
        <div class="px-6 py-4 hover:bg-white hover:bg-opacity-5 transition {{ !$notif->leida ? 'bg-white bg-opacity-5' : '' }}">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <i class="{{ $notif->icono }} text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start">
                        <p class="text-white font-medium">{{ $notif->titulo }}</p>
                        @if(!$notif->leida)
                            <span class="h-2 w-2 bg-red-500 rounded-full"></span>
                        @endif
                    </div>
                    <p class="text-gray-400 text-sm mt-1">{{ $notif->mensaje }}</p>
                    <p class="text-gray-500 text-xs mt-2">
                        {{ $notif->fecha_creacion->format('d/m/Y H:i') }}
                        @if($notif->fecha_lectura)
                            • Leída: {{ $notif->fecha_lectura->diffForHumans() }}
                        @endif
                    </p>
                    
                    @if($notif->entidad_relacionada)
                        <a href="{{ $this->obtenerLinkDeEntidad($notif->entidad_relacionada) }}" 
                           class="text-red-400 hover:text-red-300 text-sm mt-2 inline-block">
                            Ver detalle <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="px-6 py-12 text-center text-gray-400">
            <i class="fas fa-bell-slash text-4xl mb-3"></i>
            <p class="text-lg">No tienes notificaciones</p>
        </div>
        @endforelse
    </div>
    
    <!-- Paginación -->
    <div class="px-6 py-4 border-t border-gray-700">
        {{ $notificaciones->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
function marcarTodasLeidas() {
    fetch('{{ route('notificaciones.marcarTodasLeidas') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endpush