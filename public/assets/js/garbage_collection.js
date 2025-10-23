// public/assets/js/garbage-collection.js

// Global variables
let debounceTimer;
let currentFocus = -1;
let addRouteMap = null;
let editRouteMap = null;

// Autocomplete setup
function setupAutocomplete(inputId, suggestionsId, latId, lonId) {
    const input = document.getElementById(inputId);
    const suggestionsContainer = document.getElementById(suggestionsId);
    const latInput = document.getElementById(latId);
    const lonInput = document.getElementById(lonId);

    if (!input || !suggestionsContainer) return;

    input.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();

        if (query.length < 2) {
            suggestionsContainer.innerHTML = '';
            suggestionsContainer.style.display = 'none';
            return;
        }

        suggestionsContainer.innerHTML = '<div class="autocomplete-suggestion loading">Searching...</div>';
        suggestionsContainer.style.display = 'block';

        debounceTimer = setTimeout(() => {
            fetch(`/api/geocode?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    suggestionsContainer.innerHTML = '';
                    if (data.length === 0) {
                        suggestionsContainer.innerHTML = '<div class="autocomplete-suggestion no-results">No results found</div>';
                        return;
                    }

                    data.forEach(place => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-suggestion';
                        div.innerHTML = `<i class="fas fa-map-marker-alt"></i><span class="suggestion-text">${place.display_name}</span>`;
                        div.addEventListener('click', () => {
                            input.value = place.display_name;
                            if (latInput) latInput.value = place.lat;
                            if (lonInput) lonInput.value = place.lon;
                            suggestionsContainer.style.display = 'none';
                        });
                        suggestionsContainer.appendChild(div);
                    });
                })
                .catch(error => {
                    console.error('Error fetching suggestions:', error);
                    suggestionsContainer.innerHTML = '<div class="autocomplete-suggestion error">Error fetching results</div>';
                });
        }, 400);
    });

    setupKeyboardNavigation(input, suggestionsContainer);
    setupOutsideClick(input, suggestionsContainer);
}

// Keyboard navigation for autocomplete
function setupKeyboardNavigation(input, suggestionsContainer) {
    input.addEventListener('keydown', function(e) {
        const suggestions = suggestionsContainer.getElementsByClassName('autocomplete-suggestion');
        
        if (e.keyCode === 40) { // Down arrow
            e.preventDefault();
            currentFocus++;
            addActive(suggestions);
        } else if (e.keyCode === 38) { // Up arrow
            e.preventDefault();
            currentFocus--;
            addActive(suggestions);
        } else if (e.keyCode === 13) { // Enter
            e.preventDefault();
            if (currentFocus > -1 && suggestions[currentFocus]) {
                suggestions[currentFocus].click();
            }
        } else if (e.keyCode === 27) { // Escape
            suggestionsContainer.innerHTML = '';
            suggestionsContainer.style.display = 'none';
        }
    });
}

// Filter functionality
function applyFilters() {
    const truck = document.getElementById('filter-truck').value;
    const foreman = document.getElementById('filter-foreman').value;
    const status = document.getElementById('filter-status').value;
    const search = document.getElementById('filter-search').value.toLowerCase();
    
    const rows = document.querySelectorAll('#trucks-tbody tr');
    
    rows.forEach(row => {
        const truckId = row.dataset.truckId;
        const foremanId = row.dataset.foremanId;
        const rowStatus = row.dataset.status;
        const rowText = row.textContent.toLowerCase();
        
        let showRow = true;
        
        if (truck && truckId !== truck) showRow = false;
        if (foreman && foremanId !== foreman) showRow = false;
        if (status && rowStatus !== status) showRow = false;
        if (search && !rowText.includes(search)) showRow = false;
        
        row.style.display = showRow ? '' : 'none';
    });
}

function resetFilters() {
    document.getElementById('filter-truck').value = '';
    document.getElementById('filter-foreman').value = '';
    document.getElementById('filter-status').value = '';
    document.getElementById('filter-search').value = '';
    
    const rows = document.querySelectorAll('#trucks-tbody tr');
    rows.forEach(row => row.style.display = '');
}

// Modal population functions
function populateEditModal(truck) {
    document.getElementById('edit-truck-id').value = truck.id;
    document.getElementById('edit-plate-number').value = truck.plate_number;
    document.getElementById('edit-body-number').value = truck.body_number;
    document.getElementById('edit-assigned-foreman').value = truck.foreman_id || '';
    document.getElementById('edit-schedule-type').value = truck.schedule || 'daily';
    document.getElementById('edit-route-id').value = truck.route_id || '';
    document.getElementById('edit-route-name').value = truck.route_name || '';
    document.getElementById('edit-start-point').value = truck.start_point || '';
    document.getElementById('edit-mid-point').value = truck.mid_point || '';
    document.getElementById('edit-end-point').value = truck.end_point || '';

    // Populate coordinates
    document.getElementById('edit-start-lat').value = truck.start_lat || '';
    document.getElementById('edit-start-lon').value = truck.start_lon || '';
    document.getElementById('edit-mid-lat').value = truck.mid_lat || '';
    document.getElementById('edit-mid-lon').value = truck.mid_lon || '';
    document.getElementById('edit-end-lat').value = truck.end_lat || '';
    document.getElementById('edit-end-lon').value = truck.end_lon || '';

    if (editRouteMap) {
        editRouteMap.loadExistingRoute(
            truck.start_lat, truck.start_lon,
            truck.mid_lat, truck.mid_lon,
            truck.end_lat, truck.end_lon
        );
    }
}

function populateDeleteModal(truck) {
    document.getElementById('delete-truck-id').value = truck.id;
    document.getElementById('delete-truck-plate').textContent = truck.plate_number + ' (' + truck.body_number + ')';
}

function populateDispatchModal(truck) {
    document.getElementById('dispatch-truck-id').value = truck.id;
    document.getElementById('dispatch-truck-plate').value = truck.plate_number + ' (' + truck.body_number + ')';
    document.getElementById('dispatch-date').value = new Date().toISOString().split('T')[0];
}

// Initialize everything when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize autocomplete
    setupAutocomplete('start-point', 'start-suggestions', 'start-lat', 'start-lon');
    setupAutocomplete('mid-point', 'mid-suggestions', 'mid-lat', 'mid-lon');
    setupAutocomplete('end-point', 'end-suggestions', 'end-lat', 'end-lon');
    
    setupAutocomplete('edit-start-point', 'edit-start-suggestions', 'edit-start-lat', 'edit-start-lon');
    setupAutocomplete('edit-mid-point', 'edit-mid-suggestions', 'edit-mid-lat', 'edit-mid-lon');
    setupAutocomplete('edit-end-point', 'edit-end-suggestions', 'edit-end-lat', 'edit-end-lon');
    
    // Initialize search
    const searchInput = document.getElementById('filter-search');
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }

    // Initialize modals
    initializeModals();
});

// Modal initialization
function initializeModals() {
    const addModal = document.getElementById('addTruckModal');
    const editModal = document.getElementById('editTruckModal');

    // Add modal initialization
    if (addModal) {
        addModal.addEventListener('shown.bs.modal', function() {
            setTimeout(() => {
                if (!addRouteMap) {
                    addRouteMap = new RouteMapSelector('add-route-map', {
                        modalId: 'add'
                    });
                    addRouteMap.invalidateSize();
                }
            }, 200);
        });

        addModal.addEventListener('hide.bs.modal', function() {
            if (addRouteMap) {
                addRouteMap.destroy();
                addRouteMap = null;
            }
        });
    }

    // Edit modal initialization
    if (editModal) {
        editModal.addEventListener('shown.bs.modal', function() {
            setTimeout(() => {
                if (!editRouteMap) {
                    editRouteMap = new RouteMapSelector('edit-route-map', {
                        modalId: 'edit'
                    });
                    editRouteMap.invalidateSize();
                }
            }, 200);
        });

        editModal.addEventListener('hide.bs.modal', function() {
            if (editRouteMap) {
                editRouteMap.destroy();
                editRouteMap = null;
            }
        });
    }
}

const style = document.createElement('style');
style.textContent = `
.autocomplete-suggestions {
    position: absolute;
    z-index: 1000;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    max-height: 250px;
    overflow-y: auto;
    width: calc(100% - 30px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    border-radius: 0 0 8px 8px;
    display: none;
}

.autocomplete-suggestion {
    padding: 12px 15px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    transition: background-color 0.2s;
}

.autocomplete-suggestion i {
    color: #4CAF50;
    margin-top: 2px;
    flex-shrink: 0;
}

.autocomplete-suggestion .suggestion-text {
    flex: 1;
    font-size: 0.9rem;
    line-height: 1.4;
}

.autocomplete-suggestion:hover,
.autocomplete-suggestion.active {
    background-color: #f8f9fa;
}

.autocomplete-suggestion:last-child {
    border-bottom: none;
}

.autocomplete-suggestion.loading,
.autocomplete-suggestion.no-results,
.autocomplete-suggestion.error {
    justify-content: center;
    color: #666;
    font-style: italic;
    cursor: default;
}

.autocomplete-suggestion.loading {
    color: #2196F3;
}

.autocomplete-suggestion.error {
    color: #f44336;
}

/* Scrollbar styling for suggestions */
.autocomplete-suggestions::-webkit-scrollbar {
    width: 8px;
}

.autocomplete-suggestions::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.autocomplete-suggestions::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.autocomplete-suggestions::-webkit-scrollbar-thumb:hover {
    background: #555;
}
`;
document.head.appendChild(style);

function applyFilters() {
    const truck = document.getElementById('filter-truck').value;
    const foreman = document.getElementById('filter-foreman').value;
    const status = document.getElementById('filter-status').value;
    const search = document.getElementById('filter-search').value.toLowerCase();
    
    const rows = document.querySelectorAll('#trucks-tbody tr');
    
    rows.forEach(row => {
        const truckId = row.dataset.truckId;
        const foremanId = row.dataset.foremanId;
        const rowStatus = row.dataset.status;
        const rowText = row.textContent.toLowerCase();
        
        let showRow = true;
        
        if (truck && truckId !== truck) showRow = false;
        if (foreman && foremanId !== foreman) showRow = false;
        if (status && rowStatus !== status) showRow = false;
        if (search && !rowText.includes(search)) showRow = false;
        
        row.style.display = showRow ? '' : 'none';
    });
}

function resetFilters() {
    document.getElementById('filter-truck').value = '';
    document.getElementById('filter-foreman').value = '';
    document.getElementById('filter-status').value = '';
    document.getElementById('filter-search').value = '';
    
    const rows = document.querySelectorAll('#trucks-tbody tr');
    rows.forEach(row => {
        row.style.display = '';
    });
}

function populateEditModal(truck) {
    document.getElementById('edit-truck-id').value = truck.id;
    document.getElementById('edit-plate-number').value = truck.plate_number;
    document.getElementById('edit-body-number').value = truck.body_number;
    document.getElementById('edit-assigned-foreman').value = truck.foreman_id || '';
    document.getElementById('edit-schedule-type').value = truck.schedule || 'daily';
    document.getElementById('edit-route-id').value = truck.route_id || '';
    document.getElementById('edit-route-name').value = truck.route_name || '';
    document.getElementById('edit-start-point').value = truck.start_point || '';
    document.getElementById('edit-mid-point').value = truck.mid_point || '';
    document.getElementById('edit-end-point').value = truck.end_point || '';

    // Populate coordinates
    document.getElementById('edit-start-lat').value = truck.start_lat || '';
    document.getElementById('edit-start-lon').value = truck.start_lon || '';
    document.getElementById('edit-mid-lat').value = truck.mid_lat || '';
    document.getElementById('edit-mid-lon').value = truck.mid_lon || '';
    document.getElementById('edit-end-lat').value = truck.end_lat || '';
    document.getElementById('edit-end-lon').value = truck.end_lon || '';

    // Ensure map is initialized before loading route
    if (editRouteMap) {
        editRouteMap.loadExistingRoute(
            truck.start_lat, truck.start_lon,
            truck.mid_lat, truck.mid_lon,
            truck.end_lat, truck.end_lon
        );
    }
}

function populateDeleteModal(truck) {
    document.getElementById('delete-truck-id').value = truck.id;
    document.getElementById('delete-truck-plate').textContent = truck.plate_number + ' (' + truck.body_number + ')';
}

function populateDispatchModal(truck) {
    document.getElementById('dispatch-truck-id').value = truck.id;
    document.getElementById('dispatch-truck-plate').value = truck.plate_number + ' (' + truck.body_number + ')';
    document.getElementById('dispatch-date').value = new Date().toISOString().split('T')[0];
}