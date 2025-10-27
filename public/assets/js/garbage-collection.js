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
        const startLat = document.getElementById('startLat')?.value;
        const startLon = document.getElementById('startLon')?.value;
        const endLat = document.getElementById('endLat')?.value;
        const endLon = document.getElementById('endLon')?.value;
        
        if (!startLat || !startLon) {
            const startField = document.getElementById('startPoint');
            if (startField) {
                this.setFieldError(startField, 'Please select a start point on the map');
                isValid = false;
            }
        }
        
        if (!endLat || !endLon) {
            const endField = document.getElementById('endPoint');
            if (endField) {
                this.setFieldError(endField, 'Please select an end point on the map');
                isValid = false;
            }
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
        const filterTruck = document.getElementById('filter-truck');
        const filterForeman = document.getElementById('filter-foreman');
        const filterStatus = document.getElementById('filter-status');
        const filterSearch = document.getElementById('filter-search');
        
        if (filterTruck) filterTruck.value = '';
        if (filterForeman) filterForeman.value = '';
        if (filterStatus) filterStatus.value = '';
        if (filterSearch) filterSearch.value = '';
        
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
}

// FIXED: Updated populateRouteDetailsModal function with proper null checks
function populateRouteDetailsModal(truck) {
    if (!truck) {
        console.error('No truck data provided to populateRouteDetailsModal');
        return;
    }
    
    console.log('Populating route details modal with:', truck);
    
    // Safely populate basic information with null checks
    const plateElement = document.getElementById('details-plate-number');
    const bodyElement = document.getElementById('details-body-number');
    const routeElement = document.getElementById('details-route-name');
    const foremanElement = document.getElementById('details-foreman');
    
    if (plateElement) plateElement.textContent = truck.plate_number || '-';
    if (bodyElement) bodyElement.textContent = truck.body_number || '-';
    if (routeElement) routeElement.textContent = truck.route_name || 'Not Assigned';
    if (foremanElement) foremanElement.textContent = truck.foreman_name || 'Not Assigned';
    
    // Handle route points
    const routePointsList = document.getElementById('route-points-list');
    if (!routePointsList) {
        console.error('route-points-list element not found');
        return;
    }
    
    // Build route points array from truck data
    const routePoints = [];
    
    // Add start point if exists
    if (truck.start_point && truck.start_lat && truck.start_lon) {
        routePoints.push({
            name: truck.start_point,
            address: truck.start_address || 'Start location',
            lat: parseFloat(truck.start_lat),
            lng: parseFloat(truck.start_lon)
        });
    }
    
    // Add mid point if exists
    if (truck.mid_point && truck.mid_lat && truck.mid_lon) {
        routePoints.push({
            name: truck.mid_point,
            address: truck.mid_address || 'Collection point',
            lat: parseFloat(truck.mid_lat),
            lng: parseFloat(truck.mid_lon)
        });
    }
    
    // Add end point if exists
    if (truck.end_point && truck.end_lat && truck.end_lon) {
        routePoints.push({
            name: truck.end_point,
            address: truck.end_address || 'End location',
            lat: parseFloat(truck.end_lat),
            lng: parseFloat(truck.end_lon)
        });
    }
    
    // Display route points
    if (routePoints.length === 0) {
        routePointsList.innerHTML = `
            <div class="empty-points text-center p-4">
                <i class="fas fa-map-pin text-muted mb-2"></i>
                <p class="text-muted mb-0">No route points</p>
            </div>
        `;
    } else {
        let pointsHTML = '';
        routePoints.forEach((point, index) => {
            const pointType = index === 0 ? 'start' : 
                             index === routePoints.length - 1 ? 'end' : 'collection';
            
            pointsHTML += `
                <div class="point-item" onclick="highlightPoint(${index})" data-point-index="${index}">
                    <div class="d-flex align-items-start">
                        <span class="point-number ${pointType}">${index + 1}</span>
                        <div class="flex-grow-1">
                            <div class="point-name">${point.name || `Point ${index + 1}`}</div>
                            <div class="point-address">${point.address || 'Address not available'}</div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        routePointsList.innerHTML = pointsHTML;
    }
    
    // Initialize map after modal is shown
    const modal = document.getElementById('routeDetailsModal');
    if (modal) {
        // Remove any existing listeners
        modal.removeEventListener('shown.bs.modal', initMapForModal);
        
        // Add new listener
        modal.addEventListener('shown.bs.modal', initMapForModal);
        
        function initMapForModal() {
            console.log('Modal shown, initializing map...');
            initializeRouteDetailsMap(routePoints);
        }
    }
}

// FIXED: Separate function to initialize route details map
function initializeRouteDetailsMap(routePoints) {
    const mapElement = document.getElementById('route-map');
    if (!mapElement) {
        console.error('route-map element not found');
        return;
    }
    
    // Remove existing map if any
    if (window.routeDetailsMapInstance) {
        window.routeDetailsMapInstance.remove();
        window.routeDetailsMapInstance = null;
    }
    
    // Clear map container
    mapElement.innerHTML = '';
    
    try {
        // Default center (Manila)
        const defaultCenter = [14.5995, 120.9842];
        const defaultZoom = 13;
        
        // Initialize map
        window.routeDetailsMapInstance = L.map('route-map', {
            center: defaultCenter,
            zoom: defaultZoom,
            zoomControl: true,
            scrollWheelZoom: true
        });
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(window.routeDetailsMapInstance);
        
        // Add route points to map
        if (routePoints && routePoints.length > 0) {
            addPointsToRouteMap(routePoints);
        }
        
        // Fix map size after initialization
        setTimeout(() => {
            if (window.routeDetailsMapInstance) {
                window.routeDetailsMapInstance.invalidateSize();
            }
        }, 300);
        
        console.log('Route details map initialized successfully');
        
    } catch (error) {
        console.error('Error initializing route details map:', error);
        mapElement.innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100 text-center">
                <div>
                    <i class="fas fa-exclamation-triangle text-warning mb-2"></i>
                    <p class="mb-0">Error loading map</p>
                </div>
            </div>
        `;
    }
}

// FIXED: Function to add points to route map
function addPointsToRouteMap(routePoints) {
    if (!window.routeDetailsMapInstance || !routePoints || routePoints.length === 0) return;
    
    const pathCoordinates = [];
    const bounds = L.latLngBounds();
    
    // Clear existing markers
    if (window.routeDetailsMarkers) {
        window.routeDetailsMarkers.forEach(marker => {
            window.routeDetailsMapInstance.removeLayer(marker);
        });
    }
    window.routeDetailsMarkers = [];
    
    routePoints.forEach((point, index) => {
        if (!point.lat || !point.lng || isNaN(point.lat) || isNaN(point.lng)) return;
        
        const coordinates = [point.lat, point.lng];
        pathCoordinates.push(coordinates);
        bounds.extend(coordinates);
        
        // Determine point type and color
        const pointType = index === 0 ? 'start' : 
                         index === routePoints.length - 1 ? 'end' : 'collection';
        
        const markerColor = pointType === 'start' ? 'green' : 
                           pointType === 'end' ? 'red' : 'blue';
        
        // Create custom icon
        const customIcon = L.divIcon({
            html: `<div style="background-color: ${markerColor}; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 12px; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">${index + 1}</div>`,
            className: 'custom-marker',
            iconSize: [24, 24],
            iconAnchor: [12, 12],
            popupAnchor: [0, -12]
        });
        
        // Create marker
        const marker = L.marker(coordinates, {
            icon: customIcon,
            title: point.name || `Point ${index + 1}`
        }).bindPopup(`
            <div style="min-width: 150px;">
                <h6 style="margin-bottom: 8px;">${point.name || `Point ${index + 1}`}</h6>
                <p style="margin-bottom: 4px; font-size: 12px;"><strong>Address:</strong><br>${point.address || 'No address'}</p>
                <p style="margin: 0; font-size: 12px;"><strong>Type:</strong> ${pointType.charAt(0).toUpperCase() + pointType.slice(1)} Point</p>
            </div>
        `);
        
        marker.addTo(window.routeDetailsMapInstance);
        window.routeDetailsMarkers.push(marker);
    });
    
    // Draw route path if more than one point
    if (pathCoordinates.length > 1) {
        if (window.routeDetailsPath) {
            window.routeDetailsMapInstance.removeLayer(window.routeDetailsPath);
        }
        
        window.routeDetailsPath = L.polyline(pathCoordinates, {
            color: '#2196f3',
            weight: 4,
            opacity: 0.7,
            smoothFactor: 1
        }).addTo(window.routeDetailsMapInstance);
    }
    
    // Fit map to show all points
    if (pathCoordinates.length > 0) {
        window.routeDetailsMapInstance.fitBounds(bounds, { padding: [20, 20] });
    }
}

// FIXED: Function to highlight specific point
function highlightPoint(pointIndex) {
    console.log('Highlighting point:', pointIndex);
    
    // Remove active class from all points
    document.querySelectorAll('.point-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Add active class to selected point
    const pointItem = document.querySelector(`[data-point-index="${pointIndex}"]`);
    if (pointItem) {
        pointItem.classList.add('active');
    }
    
    // Highlight marker on map
    if (window.routeDetailsMarkers && window.routeDetailsMarkers[pointIndex] && window.routeDetailsMapInstance) {
        const marker = window.routeDetailsMarkers[pointIndex];
        marker.openPopup();
        window.routeDetailsMapInstance.panTo(marker.getLatLng());
    }
}

// Clean up when modal is closed
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('routeDetailsModal');
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            console.log('Route details modal closed, cleaning up');
            
            if (window.routeDetailsMapInstance) {
                window.routeDetailsMapInstance.remove();
                window.routeDetailsMapInstance = null;
            }
            
            if (window.routeDetailsMarkers) {
                window.routeDetailsMarkers = [];
            }
            
            if (window.routeDetailsPath) {
                window.routeDetailsPath = null;
            }
            
            // Clear active states
            document.querySelectorAll('.point-item').forEach(item => {
                item.classList.remove('active');
            });
        });
    }
});

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
    window.garbageCollectionManager = new GarbageCollectionManager();
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
