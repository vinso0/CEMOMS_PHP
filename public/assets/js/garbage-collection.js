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
// FIXED: Updated populateRouteDetailsModal function that actually works
function populateRouteDetailsModal(truck) {
    if (!truck) {
        console.error('No truck data provided to populateRouteDetailsModal');
        return;
    }
    
    console.log('🚀 Loading route details for truck:', truck.plate_number);
    console.log('🔍 Full truck object:', truck);
    
    // Update basic information
    const plateElement = document.getElementById('details-plate-number');
    const bodyElement = document.getElementById('details-body-number');
    const routeElement = document.getElementById('details-route-name');
    const foremanElement = document.getElementById('details-foreman');
    
    if (plateElement) plateElement.textContent = truck.plate_number || '-';
    if (bodyElement) bodyElement.textContent = truck.body_number || '-';
    if (routeElement) routeElement.textContent = truck.route_name || 'Not Assigned';
    if (foremanElement) foremanElement.textContent = truck.foreman_name || 'Not Assigned';
    
    // Reset points list to loading state
    const pointsList = document.getElementById('route-points-list');
    if (pointsList) {
        pointsList.innerHTML = `
            <div class="loading-points text-center p-4">
                <i class="fas fa-spinner fa-spin text-muted mb-2"></i>
                <p class="text-muted mb-0">Loading route points...</p>
            </div>
        `;
    }
    
    // Initialize map and fetch points immediately
    console.log('🗺️ Starting map initialization...');
    setTimeout(() => {
        initializeRouteDetailsMap();
        
        // Extract and validate route_id
        const routeId = truck.route_id;
        console.log('=== ROUTE ID CHECK ===');
        console.log('truck.route_id:', routeId);
        console.log('typeof truck.route_id:', typeof routeId);
        console.log('Boolean check:', !!routeId);
        
        if (routeId && String(routeId).trim() !== '' && String(routeId).trim() !== '0') {
            console.log('✅ Route ID is valid, calling fetchRoutePoints...');
            fetchRoutePoints(routeId);
        } else {
            console.log('❌ Route ID is invalid, showing no points');
            showNoRoutePoints();
        }
    }, 300);
}

// FIXED: Initialize route details map
function initializeRouteDetailsMap() {
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
        console.log('🗺️ Creating map instance...');
        
        // Initialize map
        window.routeDetailsMapInstance = L.map('route-map', {
            center: [14.5995, 120.9842], // Manila
            zoom: 12,
            zoomControl: true,
            scrollWheelZoom: true
        });
        
        // Add tile layer
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(window.routeDetailsMapInstance);
        
        // Fix map size
        setTimeout(() => {
            if (window.routeDetailsMapInstance) {
                window.routeDetailsMapInstance.invalidateSize();
            }
        }, 200);
        
        console.log('✅ Route details map initialized successfully');
        
    } catch (error) {
        console.error('❌ Error initializing route details map:', error);
        mapElement.innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100 text-center text-danger">
                <div>
                    <i class="fas fa-exclamation-triangle mb-2"></i>
                    <p class="mb-0">Error loading map</p>
                </div>
            </div>
        `;
    }
}

// FIXED: Fetch route points via AJAX
function fetchRoutePoints(routeId) {
    console.log('🌐 === FETCH STARTING ===');
    console.log('📍 Route ID:', routeId);
    
    const url = `/admin/operations/garbage_collection/get_route_points?route_id=${encodeURIComponent(routeId)}`;
    console.log('🔗 Request URL:', url);
    
    fetch(url)
        .then(response => {
            console.log('📥 Response received!');
            console.log('📊 Status:', response.status, response.statusText);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.text();
        })
        .then(text => {
            console.log('📄 Raw response:', text);
            
            try {
                const data = JSON.parse(text);
                console.log('✅ JSON parsed successfully:', data);
                console.log('📍 Route points count:', data.route_points?.length || 0);
                
                if (data.route_points && data.route_points.length > 0) {
                    console.log('🎯 Displaying route points...');
                    displayRoutePoints(data.route_points);
                    console.log('🗺️ Adding points to map...');
                    addPointsToRouteDetailsMap(data.route_points);
                } else {
                    console.warn('⚠️ No route points in response');
                    showNoRoutePoints();
                }
            } catch (parseError) {
                console.error('❌ JSON Parse Error:', parseError);
                showErrorPoints();
            }
        })
        .catch(error => {
            console.error('❌ Fetch Error:', error);
            showErrorPoints();
        });
}

// FIXED: Display route points
function displayRoutePoints(routePoints) {
    const pointsList = document.getElementById('route-points-list');
    
    if (!pointsList) {
        console.error('route-points-list element not found');
        return;
    }
    
    if (!routePoints || routePoints.length === 0) {
        showNoRoutePoints();
        return;
    }
    
    let pointsHTML = '';
    routePoints.forEach((point, index) => {
        const pointType = index === 0 ? 'start' : 
                         index === routePoints.length - 1 ? 'end' : 'collection';
        
        pointsHTML += `
            <div class="point-item" onclick="highlightRoutePoint(${index})" data-point-index="${index}">
                <div class="d-flex align-items-start">
                    <span class="point-number ${pointType}">${index + 1}</span>
                    <div class="flex-grow-1">
                        <div class="point-name">${point.name}</div>
                        <div class="point-address">${point.address}</div>
                    </div>
                </div>
            </div>
        `;
    });
    
    pointsList.innerHTML = pointsHTML;
    console.log('✅ Route points displayed successfully');
}

// FIXED: Add points to map
function addPointsToRouteDetailsMap(routePoints) {
    if (!window.routeDetailsMapInstance || !routePoints || routePoints.length === 0) {
        console.log('Cannot add points to map');
        return;
    }
    
    // Clear existing markers
    if (window.routeDetailsMarkers) {
        window.routeDetailsMarkers.forEach(marker => {
            window.routeDetailsMapInstance.removeLayer(marker);
        });
    }
    window.routeDetailsMarkers = [];
    
    const pathCoordinates = [];
    const bounds = L.latLngBounds();
    
    routePoints.forEach((point, index) => {
        if (!point.lat || !point.lng) return;
        
        const coordinates = [point.lat, point.lng];
        pathCoordinates.push(coordinates);
        bounds.extend(coordinates);
        
        const pointType = index === 0 ? 'start' : 
                         index === routePoints.length - 1 ? 'end' : 'collection';
        
        const markerColor = pointType === 'start' ? '#28a745' : 
                           pointType === 'end' ? '#dc3545' : '#007bff';
        
        // Create custom marker
        const customIcon = L.divIcon({
            html: `<div style="background-color: ${markerColor}; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 12px; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">${index + 1}</div>`,
            className: 'custom-route-marker',
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });
        
        const marker = L.marker(coordinates, { icon: customIcon })
            .addTo(window.routeDetailsMapInstance)
            .bindPopup(`
                <div style="min-width: 150px;">
                    <h6>${point.name}</h6>
                    <p class="mb-0"><small>${point.address}</small></p>
                </div>
            `);
        
        window.routeDetailsMarkers.push(marker);
    });
    
    // Draw path
    if (pathCoordinates.length > 1) {
        window.routeDetailsPath = L.polyline(pathCoordinates, {
            color: '#2196f3',
            weight: 4,
            opacity: 0.7
        }).addTo(window.routeDetailsMapInstance);
    }
    
    // Fit bounds
    if (pathCoordinates.length > 0) {
        window.routeDetailsMapInstance.fitBounds(bounds, { padding: [20, 20] });
    }
    
    console.log(`✅ Added ${window.routeDetailsMarkers.length} markers to map`);
}

// FIXED: Show states
function showNoRoutePoints() {
    const pointsList = document.getElementById('route-points-list');
    if (pointsList) {
        pointsList.innerHTML = `
            <div class="empty-points text-center p-4">
                <i class="fas fa-map-pin text-muted mb-2"></i>
                <p class="text-muted mb-0">No route points assigned</p>
            </div>
        `;
    }
}

function showErrorPoints() {
    const pointsList = document.getElementById('route-points-list');
    if (pointsList) {
        pointsList.innerHTML = `
            <div class="empty-points text-center p-4">
                <i class="fas fa-exclamation-triangle text-warning mb-2"></i>
                <p class="text-muted mb-0">Error loading route points</p>
            </div>
        `;
    }
}

// FIXED: Highlight function with unique name
function highlightRoutePoint(pointIndex) {
    // Remove active class from all points
    document.querySelectorAll('.point-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Add active class
    const pointItem = document.querySelector(`[data-point-index="${pointIndex}"]`);
    if (pointItem) {
        pointItem.classList.add('active');
    }
    
    // Highlight on map
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

document.addEventListener('DOMContentLoaded', function() {
    // Handle modal focus to prevent aria-hidden warnings
    document.addEventListener('hide.bs.modal', function(event) {
        if (event.target.id === 'routeDetailsModal') {
            // Remove focus from any active button before hiding
            const activeElement = document.activeElement;
            if (activeElement && activeElement.tagName === 'BUTTON') {
                activeElement.blur();
            }
        }
    });
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
