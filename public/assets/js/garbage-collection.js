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
    
    // Replace the existing initAddTruckMap method with this:
    initAddTruckMap() {
        const mapContainer = document.getElementById('addRouteMap');
        
        if (!mapContainer) {
            console.error('Map container not found');
            return;
        }
        
        // Check if Leaflet is loaded
        if (typeof L === 'undefined') {
            console.error('Leaflet library not loaded');
            this.showMapError('Leaflet library not loaded. Please refresh the page.');
            return;
        }
        
        try {
            // Clear any existing content
            mapContainer.innerHTML = '';
            
            // Add a small delay to ensure modal DOM is ready
            setTimeout(() => {
                // Initialize RouteMapSelector
                this.routeMapSelector = new RouteMapSelector('addRouteMap', {
                    defaultLat: 14.5995, // Philippines
                    defaultLng: 120.9842,
                    defaultZoom: 13
                });
                
                // Mark map as loaded
                mapContainer.classList.add('map-loaded');
                console.log('Map initialized successfully');
            }, 200);
            
        } catch (error) {
            console.error('Error initializing map:', error);
            this.showMapError('Failed to load map. Please try again.');
        }
    }

    // Replace the showMapError method with this:
    showMapError(message = 'Unable to load map. Please check your internet connection.') {
        const mapContainer = document.getElementById('addRouteMap');
        if (mapContainer) {
            mapContainer.innerHTML = `
                <div class="map-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h6>Map Loading Error</h6>
                    <p>${message}</p>
                    <button type="button" class="btn btn-sm btn-primary" onclick="window.location.reload()">
                        <i class="fas fa-redo me-1"></i>Retry
                    </button>
                </div>
            `;
        }
    }

    // Update the resetAddTruckForm method:
    resetAddTruckForm() {
        const form = document.getElementById('addTruckForm');
        if (form) {
            form.reset();
            form.classList.remove('was-validated');
            
            // Clear validation errors
            form.querySelectorAll('.is-invalid').forEach(field => {
                field.classList.remove('is-invalid');
            });
        }
        
        // Reset map
        if (this.routeMapSelector) {
            this.routeMapSelector.clearAllMarkers();
        }
        
        // Reset map container
        const mapContainer = document.getElementById('addRouteMap');
        if (mapContainer) {
            mapContainer.classList.remove('map-loaded');
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
function populateRouteDetailsModal(truck) {
    // Populate basic information
    document.getElementById('details-plate-number').textContent = truck.plate_number || '-';
    document.getElementById('details-body-number').textContent = truck.body_number || '-';
    document.getElementById('details-route-name').textContent = truck.route_name || '-';
    document.getElementById('details-foreman').textContent = truck.foreman_name || 'Not Assigned';
    document.getElementById('details-schedule').textContent = truck.schedule || 'N/A';
    document.getElementById('details-status').textContent = truck.status || 'Parked';

    // Populate route points
    const routePointsList = document.getElementById('route-points-list');
    routePointsList.innerHTML = '';

    if (truck.start_point) {
        const routePoints = [];
        
        // Add start point
        routePoints.push({
            type: 'start',
            text: truck.start_point,
            lat: truck.start_lat,
            lng: truck.start_lon
        });

        // Add mid point if exists
        if (truck.mid_point) {
            routePoints.push({
                type: 'mid',
                text: truck.mid_point,
                lat: truck.mid_lat,
                lng: truck.mid_lon
            });
        }

        // Add end point
        if (truck.end_point) {
            routePoints.push({
                type: 'end',
                text: truck.end_point,
                lat: truck.end_lat,
                lng: truck.end_lon
            });
        }

        // Create route points display
        routePoints.forEach(point => {
            const pointElement = document.createElement('div');
            pointElement.className = `route-point ${point.type}-point`;
            pointElement.innerHTML = `
                <div class="route-point-icon">
                    <i class="fas fa-${point.type === 'start' ? 'play' : point.type === 'mid' ? 'circle' : 'stop'}"></i>
                </div>
                <div class="route-point-text">${point.text}</div>
            `;
            routePointsList.appendChild(pointElement);
        });

        // Initialize map
        const mapContainer = document.getElementById('routeDetailsMap');
        if (mapContainer) {
            // Clear any existing map
            mapContainer.innerHTML = '';

            // Initialize map with the first point
            const map = L.map('routeDetailsMap').setView(
                [routePoints[0].lat || 14.5995, routePoints[0].lng || 120.9842],
                13
            );

            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Add markers and connect them with a line
            const coordinates = routePoints.map(point => [point.lat, point.lng]);
            const polyline = L.polyline(coordinates, {color: '#007bff', weight: 3}).addTo(map);

            // Add markers
            routePoints.forEach(point => {
                const markerHtml = `
                    <div class="custom-marker ${point.type}-marker">
                        <div class="marker-pin">
                            <i class="fas fa-${point.type === 'start' ? 'play' : point.type === 'mid' ? 'circle' : 'stop'}"></i>
                        </div>
                    </div>`;

                const icon = L.divIcon({
                    className: 'custom-div-icon',
                    html: markerHtml,
                    iconSize: [30, 42],
                    iconAnchor: [15, 42]
                });

                L.marker([point.lat, point.lng], {icon: icon})
                    .addTo(map)
                    .bindPopup(point.text);
            });

            // Fit bounds to show all markers
            if (coordinates.length > 0) {
                map.fitBounds(polyline.getBounds());
            }
        }
    } else {
        routePointsList.innerHTML = `
            <div class="text-muted text-center py-3">
                No route points assigned
            </div>
        `;
        document.getElementById('routeDetailsMap').innerHTML = `
            <div class="text-muted text-center py-5">
                <i class="fas fa-map-marked-alt fa-3x mb-3"></i>
                <p>No route data available</p>
            </div>
        `;
    }
}

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
