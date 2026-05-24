import './bootstrap';
/**
 * Prevención de navegación hacia atrás después de logout
 * Este script detecta cuando el usuario presiona "Atrás" y verifica
 * si aún está autenticado. Si no, lo redirige a la landing page.
 */

// Variable para rastrear si el usuario cerró sesión
let userLoggedOut = false;

// Escuchar cuando el usuario cierra sesión (se setea desde los layouts)
document.addEventListener('DOMContentLoaded', function() {
    // Detectar cuando se presiona el botón "Atrás"
    window.addEventListener('pageshow', function(event) {
        // Si la página se cargó desde el historial (back/forward navigation)
        if (event.persisted || performance.getEntriesByType('navigation')[0]?.type === 'back_forward') {
            
            // Verificar si el usuario aún está autenticado haciendo una petición AJAX
            fetch('/api/auth/check', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            })
            .then(response => {
                if (!response.ok) {
                    // Si no está autenticado (401/403), forzar redirección
                    console.log('Usuario no autenticado - Redirigiendo a landing...');
                    window.location.replace('/');
                }
            })
            .catch(error => {
                // Si hay error de red, asumir que no está autenticado
                console.log('Error de conexión - Redirigiendo a landing...');
                window.location.replace('/');
            });
        }
    });
    
    // Escuchar cuando el usuario cierra la pestaña o recarga
    window.addEventListener('beforeunload', function() {
        // Limpiar cualquier estado
        userLoggedOut = false;
    });
});

// Función para marcar que el usuario cerró sesión (llamar desde el formulario de logout)
function markUserLoggedOut() {
    userLoggedOut = true;
    sessionStorage.setItem('userLoggedOut', 'true');
}

// Verificar al cargar si el usuario cerró sesión previamente
if (sessionStorage.getItem('userLoggedOut') === 'true') {
    sessionStorage.removeItem('userLoggedOut');
    window.location.replace('/');
}