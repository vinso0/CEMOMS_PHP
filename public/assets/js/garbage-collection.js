// Garbage Collection Management
class GarbageCollectionManager {
    constructor() {
        this.routeMapSelector = null;
        this.init();
    }
    
    init() {
        this.initModals();
        this.initFilters();
        this.initFormValidation();
    }
    
    initModals() {
        // Initialize Add Truck Modal
        const addTruckModal = document.getElementById('addTruckModal');
        if (addTruckModal) {
            addTruckModal.addEventListener('shown.bs.modal', () => {
                this.initAddTruckMap();
            });
            
            addTruckModal.addEventListener('hidden.bs.modal', () => {
                this.resetAddTruckForm();
            });
        }
    }
    
    initAddTruckMap() {
        if (typeof L !== 'undefined') {
            this.routeMapSelector = new RouteMapSelector('addRouteMap', {
                defaultLat: 14.5995, // Philippines
                defaultLng: 120.9842,
                defaultZoom: 13
            });
        } else {
            console.error('Leaflet library not loaded');
            this.showMapError();
        }
    }

    
    initFilters() {
        const filterButtons = document.querySelectorAll('[onclick*="applyFilters"], [onclick*="resetFilters"]');
        filterButtons.forEach(button => {
            const action = button.getAttribute('onclick');
            button.removeAttribute('onclick');
            
            if (action.includes('applyFilters')) {
                button.addEventListener('click', () => this.applyFilters());
            } else if (action.includes('resetFilters')) {
                button.addEventListener('click', () => this.resetFilters());
            }
        });
    }
    
    initFormValidation() {
        const addTruckForm = document.getElementById('addTruckForm');
        if (addTruckForm) {
            addTruckForm.addEventListener('submit', (e) => {
                if (!this.validateTruckForm(addTruckForm)) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                
                addTruckForm.classList.add('was-validated');
            });
        }
    }
    
    validateTruckForm(form) {
        let isValid = true;
        
        // Validate required fields
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                this.setFieldError(field, 'This field is required');
                isValid = false;
            } else {
                this.clearFieldError(field);
            }
        });
        
        // Validate route points
        const startLat = document.getElementById('startLat').value;
        const startLon = document.getElementById('startLon').value;
        const endLat = document.getElementById('endLat').value;
        const endLon = document.getElementById('endLon').value;
        
        if (!startLat || !startLon) {
            this.setFieldError(document.getElementById('startPoint'), 'Please select a start point on the map');
            isValid = false;
        }
        
        if (!endLat || !endLon) {
            this.setFieldError(document.getElementById('endPoint'), 'Please select an end point on the map');
            isValid = false;
        }
        
        return isValid;
    }
    
    setFieldError(field, message) {
        field.classList.add('is-invalid');
        const feedback = field.parentElement.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.textContent = message;
        }
    }
    
    clearFieldError(field) {
        field.classList.remove('is-invalid');
        const feedback = field.parentElement.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.textContent = '';
        }
    }
    
    resetAddTruckForm() {
        const form = document.getElementById('addTruckForm');
        if (form) {
            form.reset();
            form.classList.remove('was-validated');
            
            // Clear validation errors
            form.querySelectorAll('.is-invalid').forEach(field => {
                field.classList.remove('is-invalid');
            });
            
            // Clear route map
            if (this.routeMapSelector) {
                this.routeMapSelector.clearAllMarkers();
            }
        }
    }
    
    applyFilters() {
        const filters = {
            truck: document.getElementById('filter-truck')?.value || '',
            foreman: document.getElementById('filter-foreman')?.value || '',
            status: document.getElementById('filter-status')?.value || '',
            search: document.getElementById('filter-search')?.value || ''
        };
        
        const rows = document.querySelectorAll('#trucks-tbody tr[data-truck-id]');
        
        rows.forEach(row => {
            let showRow = true;
            
            // Filter by truck
            if (filters.truck && row.dataset.truckId !== filters.truck) {
                showRow = false;
            }
            
            // Filter by foreman
            if (filters.foreman && row.dataset.foremanId !== filters.foreman) {
                showRow = false;
            }
            
            // Filter by status
            if (filters.status && row.dataset.status !== filters.status) {
                showRow = false;
            }
            
            // Filter by search
            if (filters.search) {
                const searchText = row.textContent.toLowerCase();
                if (!searchText.includes(filters.search.toLowerCase())) {
                    showRow = false;
                }
            }
            
            row.style.display = showRow ? '' : 'none';
        });
        
        this.updateFilterResults();
    }
    
    resetFilters() {
        document.getElementById('filter-truck').value = '';
        document.getElementById('filter-foreman').value = '';
        document.getElementById('filter-status').value = '';
        document.getElementById('filter-search').value = '';
        
        // Show all rows
        document.querySelectorAll('#trucks-tbody tr').forEach(row => {
            row.style.display = '';
        });
        
        this.updateFilterResults();
    }
    
    updateFilterResults() {
        const visibleRows = document.querySelectorAll('#trucks-tbody tr[data-truck-id][style=""], #trucks-tbody tr[data-truck-id]:not([style])');
        console.log(`Showing ${visibleRows.length} trucks`);
    }
    
    showMapError() {
        const mapContainer = document.getElementById('addRouteMap');
        if (mapContainer) {
            mapContainer.innerHTML = `
                <div class="map-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Unable to load map. Please check your internet connection.</p>
                    <button type="button" class="btn btn-sm btn-primary" onclick="location.reload()">
                        Reload Page
                    </button>
                </div>
            `;
        }
    }
}

// Utility functions for modal population (existing functionality)
function populateEditModal(truck) {
    // Implementation for edit modal
    console.log('Edit truck:', truck);
}

function populateDispatchModal(truck) {
    // Implementation for dispatch modal
    console.log('Dispatch truck:', truck);
}

function populateDeleteModal(truck) {
    // Implementation for delete modal
    console.log('Delete truck:', truck);
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    new GarbageCollectionManager();
});

// Legacy support for existing onclick handlers
function applyFilters() {
    if (window.garbageCollectionManager) {
        window.garbageCollectionManager.applyFilters();
    }
}

function resetFilters() {
    if (window.garbageCollectionManager) {
        window.garbageCollectionManager.resetFilters();
    }
}
