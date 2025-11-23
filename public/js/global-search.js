/**
 * Búsqueda Global - Personalización del navbar search de AdminLTE
 */
document.addEventListener('DOMContentLoaded', function() {
    // Buscar el formulario de búsqueda del navbar
    const navbarSearchForm = document.querySelector('form.navbar-search-form');
    const navbarSearchInput = document.querySelector('input.navbar-search-input');
    
    if (navbarSearchForm && navbarSearchInput) {
        // Cambiar la acción del formulario
        navbarSearchForm.action = '/admin/search';
        navbarSearchForm.method = 'GET';
        
        // Cambiar el nombre del input
        if (navbarSearchInput.name !== 'q') {
            navbarSearchInput.name = 'q';
        }
        
        // Agregar placeholder
        navbarSearchInput.placeholder = 'Buscar keywords, sitios, URLs, tareas...';
        
        // Autocompletado para el navbar search
        let autocompleteTimeout;
        let autocompleteContainer = null;
        
        navbarSearchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(autocompleteTimeout);
            
            // Eliminar contenedor anterior si existe
            if (autocompleteContainer) {
                autocompleteContainer.remove();
                autocompleteContainer = null;
            }
            
            if (query.length < 2) {
                return;
            }
            
            autocompleteTimeout = setTimeout(function() {
                fetch('/admin/search/autocomplete?q=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            return;
                        }
                        
                        // Crear contenedor de autocompletado
                        autocompleteContainer = document.createElement('div');
                        autocompleteContainer.className = 'navbar-search-autocomplete position-absolute w-100 mt-1';
                        autocompleteContainer.style.cssText = 'z-index: 1000; top: 100%; left: 0;';
                        
                        const listGroup = document.createElement('div');
                        listGroup.className = 'list-group shadow-lg';
                        
                        data.forEach(function(item) {
                            const link = document.createElement('a');
                            link.href = item.url;
                            link.className = 'list-group-item list-group-item-action';
                            link.innerHTML = '<i class="' + item.icon + ' mr-2"></i><strong>' + item.text + '</strong><small class="text-muted ml-2">(' + item.type + ')</small>';
                            listGroup.appendChild(link);
                        });
                        
                        autocompleteContainer.appendChild(listGroup);
                        
                        // Insertar después del input
                        const inputGroup = navbarSearchInput.closest('.input-group');
                        if (inputGroup) {
                            inputGroup.style.position = 'relative';
                            inputGroup.appendChild(autocompleteContainer);
                        }
                    })
                    .catch(error => {
                        console.error('Error en autocompletado:', error);
                    });
            }, 300);
        });
        
        // Ocultar autocompletado al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (autocompleteContainer && !autocompleteContainer.contains(e.target) && e.target !== navbarSearchInput) {
                autocompleteContainer.remove();
                autocompleteContainer = null;
            }
        });
        
        // Ocultar autocompletado al presionar Escape
        navbarSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && autocompleteContainer) {
                autocompleteContainer.remove();
                autocompleteContainer = null;
            }
        });
    }
});

