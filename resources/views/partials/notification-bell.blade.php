<!-- ===== COMPONENTE DE NOTIFICACIONES (CAMPANITA) ===== -->
<div class="relative" x-data="notificationsComponent()">
    
    <!-- Botón de campana -->
    <button @click="toggleDropdown()" 
            class="relative text-white hover:text-red-500 focus:outline-none transition" 
            title="Notificaciones">
        <i class="fas fa-bell text-xl"></i>
        
        <!-- Badge de no leídas -->
        <span x-show="noLeidas > 0" 
              x-text="noLeidas > 9 ? '9+' : noLeidas"
              class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-semibold animate-pulse">
        </span>
    </button>

    <!-- Dropdown de notificaciones -->
    <div x-show="isOpen" 
         @click.away="isOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 md:w-96 bg-black bg-opacity-95 border border-gray-700 rounded-lg shadow-xl z-50 overflow-hidden">
        
        <!-- Header del dropdown -->
        <div class="px-4 py-3 border-b border-gray-700 flex justify-between items-center">
            <h3 class="text-white font-semibold">Notificaciones</h3>
            <button @click="marcarTodasLeidas()" 
                    class="text-xs text-red-400 hover:text-red-300 transition"
                    x-show="noLeidas > 0">
                Marcar todas como leídas
            </button>
        </div>

        <!-- Lista de notificaciones -->
        <div class="max-h-96 overflow-y-auto">
            <template x-if="loading">
                <div class="p-4 text-center text-gray-400">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Cargando...
                </div>
            </template>

            <template x-if="!loading && notificaciones.length === 0">
                <div class="p-4 text-center text-gray-400">
                    <i class="fas fa-bell-slash text-2xl mb-2 block"></i>
                    <p class="text-sm">No tienes notificaciones nuevas</p>
                </div>
            </template>

            <template x-for="notif in notificaciones" :key="notif.id">
                <a :href="notif.entidad_relacionada ? obtenerLink(notif) : '#'" 
                   @click="marcarComoLeida(notif.id)"
                   class="block px-4 py-3 border-b border-gray-800 hover:bg-white hover:bg-opacity-5 transition"
                   :class="{'bg-white bg-opacity-5': !notif.leida}">
                    
                    <div class="flex items-start space-x-3">
                        <!-- Icono según tipo -->
                        <div class="flex-shrink-0">
                            <i :class="notif.icono + ' text-lg'"></i>
                        </div>
                        
                        <!-- Contenido -->
                        <div class="flex-1 min-w-0">
                            <p class="text-white text-sm font-medium" x-text="notif.titulo"></p>
                            <p class="text-gray-400 text-xs mt-1 line-clamp-2" x-text="notif.mensaje"></p>
                            <p class="text-gray-500 text-xs mt-2" x-text="notif.fecha_creacion"></p>
                        </div>
                        
                        <!-- Indicador de no leída -->
                        <div x-show="!notif.leida" class="flex-shrink-0">
                            <span class="h-2 w-2 bg-red-500 rounded-full"></span>
                        </div>
                    </div>
                </a>
            </template>
        </div>

        <!-- Footer del dropdown -->
        <div class="px-4 py-2 border-t border-gray-700 bg-black bg-opacity-50">
            <a href="{{ route('notificaciones.index') }}" 
               class="text-center block text-red-400 hover:text-red-300 text-sm transition">
                Ver todas las notificaciones <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
function notificationsComponent() {
    return {
        isOpen: false,
        loading: true,
        noLeidas: 0,
        notificaciones: [],
        
        init() {
            this.cargarNotificaciones();
            // Auto-refresh cada 60 segundos
            setInterval(() => this.cargarNotificaciones(), 60000);
        },
        
        toggleDropdown() {
            this.isOpen = !this.isOpen;
            if (this.isOpen && this.notificaciones.length === 0) {
                this.cargarNotificaciones();
            }
        },
        
        cargarNotificaciones() {
            this.loading = true;
            
            fetch('{{ route('notificaciones.api.obtener') }}', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.notificaciones = data.notificaciones;
                    this.noLeidas = data.no_leidas;
                }
            })
            .catch(error => console.error('Error cargando notificaciones:', error))
            .finally(() => this.loading = false);
        },
        
        marcarComoLeida(id) {
            fetch(`/notificaciones/${id}/leida`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.noLeidas = data.no_leidas;
                    // Actualizar estado local
                    const notif = this.notificaciones.find(n => n.id === id);
                    if (notif) notif.leida = true;
                }
            })
            .catch(error => console.error('Error marcando como leída:', error));
        },
        
        marcarTodasLeidas() {
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
                    this.noLeidas = 0;
                    this.notificaciones.forEach(n => n.leida = true);
                }
            })
            .catch(error => console.error('Error marcando todas como leídas:', error));
        },
        
        obtenerLink(notif) {
            // Mapear entidad_relacionada a ruta
            if (notif.entidad_relacionada?.startsWith('documento:')) {
                const docId = notif.entidad_relacionada.split(':')[1];
                // Determinar ruta según rol actual
                const rutaBase = window.location.pathname.includes('/tutor') ? '/tutor' : 
                                window.location.pathname.includes('/estudiante') ? '/estudiante' : 
                                window.location.pathname.includes('/tribunal') ? '/tribunal' : '';
                return `${rutaBase}/revisar/${docId}`;
            }
            if (notif.entidad_relacionada?.startsWith('tarea:')) {
                const tareaId = notif.entidad_relacionada.split(':')[1];
                return `/estudiante/tareas`;
            }
            return '#';
        }
    }
}
</script>
@endpush