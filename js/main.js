// public/js/main.js

document.addEventListener('DOMContentLoaded', function() {
    // Referencia al elemento principal del wrapper (que tiene la clase 'toggled')
    const wrapper = document.getElementById('wrapper');

    // Botón para desktop (minimizar/expandir la sidebar)
    const sidebarToggleDesktop = document.getElementById('sidebar-toggle-desktop');
    if (sidebarToggleDesktop) {
        sidebarToggleDesktop.addEventListener('click', function() {
            if (wrapper) { // Verificar si wrapper existe antes de usarlo
                wrapper.classList.toggle('toggled-desktop');
            }
        });
    }

    // Botón para mobile (mostrar/ocultar completamente la sidebar)
    const sidebarToggleMobile = document.getElementById('sidebar-toggle-mobile');
    if (sidebarToggleMobile) {
        sidebarToggleMobile.addEventListener('click', function() {
            if (wrapper) {
                wrapper.classList.toggle('toggled-mobile');
            }
        });
    }

    // Botón de cierre para móvil dentro de la sidebar
    const sidebarCloseMobile = document.getElementById('sidebar-close-mobile');
    if (sidebarCloseMobile) {
        sidebarCloseMobile.addEventListener('click', function() {
            if (wrapper) {
                wrapper.classList.remove('toggled-mobile'); // Elimina la clase para ocultar
            }
        });
    }

    // Puedes añadir aquí cualquier otra lógica JS global o común a todas las páginas.
});
