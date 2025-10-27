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

/**
 * ENHANCED: Populate route details modal with schedule info
 */
function populateRouteDetailsModal(truckData) {
    console.log('🚛 Opening route details for truck:', truckData);
    
    // Clear previous data
    document.getElementById('route-points-list').innerHTML = `
        <div class="loading-points text-center p-4">
            <i class="fas fa-spinner fa-spin text-muted mb-2"></i>
            <p class="text-muted mb-0">Loading route points...</p>
        </div>
    `;
    
    // Clear map
    if (window.routeDetailsMapInstance) {
        window.routeDetailsMapInstance.remove();
        window.routeDetailsMapInstance = null;
    }
    
    // Populate truck and route information
    document.getElementById('details-plate-number').textContent = truckData.plate_number || '-';
    document.getElementById('details-body-number').textContent = truckData.body_number || '-';
    document.getElementById('details-route-name').textContent = truckData.route_name || '-';
    document.getElementById('details-foreman').textContent = truckData.foreman_name || '-';
    
    // ENHANCED: Populate schedule information
    document.getElementById('details-schedule-type').textContent = 
        truckData.schedule ? (truckData.schedule.charAt(0).toUpperCase() + truckData.schedule.slice(1)) : '-';
    
    // Format and display operation time
    let operationTime = truckData.operation_time || '-';
    if (operationTime !== '-' && operationTime) {
        // Convert 24-hour to 12-hour format
        const time = new Date(`2000-01-01 ${operationTime}`);
        operationTime = time.toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: '2-digit', 
            hour12: true 
        });
    }
    document.getElementById('details-operation-time').textContent = operationTime;
    
    // Handle weekly days display
    const weeklyContainer = document.getElementById('weekly-days-container');
    const weeklyDaysElement = document.getElementById('details-weekly-days');
    
    if (truckData.schedule === 'weekly' && truckData.weekly_days && truckData.weekly_days.length > 0) {
        weeklyContainer.style.display = 'block';
        
        // Create day badges
        const dayBadges = truckData.weekly_days.map(day => 
            `<span class="badge bg-primary me-1 mb-1">${day.substring(0, 3)}</span>`
        ).join('');
        
        weeklyDaysElement.innerHTML = dayBadges;
    } else {
        weeklyContainer.style.display = 'none';
    }
    
    // Initialize map
    initializeRouteDetailsMap();
    
    // Load route points
    if (truckData.route_id) {
        loadRoutePoints(truckData.route_id);
    } else {
        document.getElementById('route-points-list').innerHTML = `
            <div class="empty-points text-center p-4">
                <i class="fas fa-exclamation-circle text-warning mb-2" style="font-size: 2rem;"></i>
                <h6>No Route Assigned</h6>
                <p class="text-muted mb-0">This truck has no route assigned yet.</p>
            </div>
        `;
    }
}


/**
 * ENHANCED: Initialize route details map with tile loading fixes
 */
function initializeRouteDetailsMap() {
    const mapContainer = document.getElementById('route-map');
    
    if (!mapContainer) {
        console.error('❌ Route map container not found');
        return;
    }
    
    // Clean up existing map instance
    if (window.routeDetailsMapInstance) {
        try {
            window.routeDetailsMapInstance.remove();
        } catch (e) {
            console.warn('Map cleanup warning:', e);
        }
    }
    
    // ENHANCED: Initialize map with better tile loading options
    window.routeDetailsMapInstance = L.map('route-map', {
        center: [14.6091, 121.0223],
        zoom: 12,
        zoomControl: true,
        attributionControl: true,
        
        // TILE LOADING FIXES
        preferCanvas: false,
        worldCopyJump: false,
        maxBoundsViscosity: 1.0,
        
        // Prevent loading issues
        fadeAnimation: false,
        zoomAnimation: false,
        markerZoomAnimation: false
    });
    
    // ENHANCED: Add tile layer with better loading options
    const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
        
        // TILE LOADING OPTIMIZATIONS
        keepBuffer: 2,
        updateWhenZooming: false,
        updateWhenIdle: true,
        crossOrigin: true,
        
        // Retry failed tiles
        errorTileUrl: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='
    });
    
    tileLayer.addTo(window.routeDetailsMapInstance);
    
    // Initialize arrays
    window.routeDetailsMarkers = [];
    window.routeDetailsPath = null;
    
    // FORCE tile loading after map is ready
    window.routeDetailsMapInstance.whenReady(() => {
        console.log('✅ Route details map is ready');
        
        // Force invalidate size multiple times
        setTimeout(() => {
            window.routeDetailsMapInstance.invalidateSize(true);
        }, 100);
        
        setTimeout(() => {
            window.routeDetailsMapInstance.invalidateSize(true);
            // Force tile refresh
            tileLayer.redraw();
        }, 300);
        
        setTimeout(() => {
            window.routeDetailsMapInstance.invalidateSize(true);
        }, 500);
    });
    
    console.log('🗺️ Route details map initialized with tile loading fixes');
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

/**
 * ENHANCED: Add points to map with road-following route
 */
function addPointsToRouteDetailsMap(routePoints) {
    if (!window.routeDetailsMapInstance || !routePoints || routePoints.length === 0) {
        console.log('Cannot add points to map');
        return;
    }
    
    // Clear existing markers and paths
    if (window.routeDetailsMarkers) {
        window.routeDetailsMarkers.forEach(marker => {
            window.routeDetailsMapInstance.removeLayer(marker);
        });
    }
    window.routeDetailsMarkers = [];
    
    if (window.routeDetailsPath) {
        window.routeDetailsMapInstance.removeLayer(window.routeDetailsPath);
        window.routeDetailsPath = null;
    }
    
    const pathCoordinates = [];
    const bounds = L.latLngBounds();
    
    // Add markers first
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
    
    // Draw route using road routing
    if (pathCoordinates.length > 1) {
        console.log('🛣️ Fetching road route between points...');
        drawRoadRoute(pathCoordinates);
    }
    
    // Fit bounds
    if (pathCoordinates.length > 0) {
        window.routeDetailsMapInstance.fitBounds(bounds, { padding: [20, 20] });
    }
    
    console.log(`✅ Added ${window.routeDetailsMarkers.length} markers to map`);
}

/**
 * Load route points from server and display them
 */
function loadRoutePoints(routeId) {
    console.log('🗺️ Loading route points for route ID:', routeId);
    
    if (!routeId) {
        console.warn('⚠️ No route ID provided');
        document.getElementById('route-points-list').innerHTML = `
            <div class="empty-points text-center p-4">
                <i class="fas fa-exclamation-circle text-warning mb-2" style="font-size: 2rem;"></i>
                <h6>No Route Assigned</h6>
                <p class="text-muted mb-0">This truck has no route assigned yet.</p>
            </div>
        `;
        return;
    }
    
    // Show loading state
    document.getElementById('route-points-list').innerHTML = `
        <div class="loading-points text-center p-4">
            <i class="fas fa-spinner fa-spin text-muted mb-2"></i>
            <p class="text-muted mb-0">Loading route points...</p>
        </div>
    `;
    
    // Fetch route points from server
    fetch(`/admin/operations/garbage_collection/get_route_points?route_id=${routeId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('📍 Route points received:', data);
            
            if (data.error) {
                throw new Error(data.error);
            }
            
            if (!data.route_points || data.route_points.length === 0) {
                document.getElementById('route-points-list').innerHTML = `
                    <div class="empty-points text-center p-4">
                        <i class="fas fa-map-pin text-muted mb-2" style="font-size: 2rem;"></i>
                        <h6>No Route Points</h6>
                        <p class="text-muted mb-0">This route has no points defined.</p>
                    </div>
                `;
                return;
            }
            
            // Display route points in the list
            displayRoutePointsList(data.route_points);
            
            // Add points to map
            addPointsToRouteDetailsMap(data.route_points);
            
        })
        .catch(error => {
            console.error('❌ Error loading route points:', error);
            document.getElementById('route-points-list').innerHTML = `
                <div class="empty-points text-center p-4">
                    <i class="fas fa-exclamation-triangle text-danger mb-2" style="font-size: 2rem;"></i>
                    <h6>Error Loading Route Points</h6>
                    <p class="text-muted mb-0">${error.message}</p>
                </div>
            `;
        });
}

/**
 * Display route points in the sidebar list
 */
function displayRoutePointsList(routePoints) {
    const pointsList = document.getElementById('route-points-list');
    
    if (!routePoints || routePoints.length === 0) {
        pointsList.innerHTML = `
            <div class="empty-points text-center p-4">
                <i class="fas fa-map-pin text-muted mb-2" style="font-size: 2rem;"></i>
                <h6>No Route Points</h6>
                <p class="text-muted mb-0">This route has no points defined.</p>
            </div>
        `;
        return;
    }
    
    let pointsHtml = '';
    
    routePoints.forEach((point, index) => {
        const pointClass = point.name.toLowerCase().includes('start') ? 'start' : 
                          point.name.toLowerCase().includes('end') ? 'end' : 'mid';
        
        pointsHtml += `
            <div class="point-item" data-point-index="${index}" onclick="highlightRoutePoint(${index})">
                <div class="point-number ${pointClass}">${index + 1}</div>
                <div class="point-info">
                    <div class="point-name">${point.name}</div>
                    <div class="point-address">${point.address}</div>
                </div>
            </div>
        `;
    });
    
    pointsList.innerHTML = pointsHtml;
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

/**
 * NEW: Draw route following actual roads using OpenStreetMap routing
 */
function drawRoadRoute(coordinates) {
    if (coordinates.length < 2) return;
    
    // Show loading indicator
    const mapElement = document.getElementById('route-map');
    const loadingDiv = document.createElement('div');
    loadingDiv.id = 'route-loading';
    loadingDiv.innerHTML = `
        <div style="position: absolute; top: 10px; right: 10px; background: rgba(255,255,255,0.9); padding: 8px 12px; border-radius: 4px; font-size: 12px; z-index: 1000; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
            <i class="fas fa-spinner fa-spin me-1"></i>Calculating route...
        </div>
    `;
    mapElement.appendChild(loadingDiv);
    
    // Build waypoints string for OSRM (Open Source Routing Machine)
    const waypoints = coordinates.map(coord => `${coord[1]},${coord[0]}`).join(';');
    const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${waypoints}?overview=full&geometries=geojson`;
    
    console.log('🔗 OSRM URL:', osrmUrl);
    
    fetch(osrmUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Routing service error: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('🛣️ Route data received:', data);
            
            // Remove loading indicator
            const loading = document.getElementById('route-loading');
            if (loading) loading.remove();
            
            if (data.routes && data.routes.length > 0) {
                const route = data.routes[0];
                const routeCoordinates = route.geometry.coordinates.map(coord => [coord[1], coord[0]]);
                
                // Draw the road-following route
                window.routeDetailsPath = L.polyline(routeCoordinates, {
                    color: '#2196f3',
                    weight: 4,
                    opacity: 0.8,
                    smoothFactor: 1
                }).addTo(window.routeDetailsMapInstance);
                
                // Add route info popup
                const distance = (route.distance / 1000).toFixed(1); // Convert to km
                const duration = Math.round(route.duration / 60); // Convert to minutes
                
                window.routeDetailsPath.bindPopup(`
                    <div style="text-align: center;">
                        <h6>📍 Route Information</h6>
                        <p style="margin: 5px 0;"><strong>Distance:</strong> ${distance} km</p>
                        <p style="margin: 5px 0;"><strong>Duration:</strong> ${duration} min</p>
                        <small style="color: #666;">Following actual roads</small>
                    </div>
                `);
                
                console.log(`✅ Road route drawn: ${distance}km, ${duration}min`);
            } else {
                console.warn('No route found, falling back to straight line');
                drawStraightLineRoute(coordinates);
            }
        })
        .catch(error => {
            console.error('❌ Routing error:', error);
            
            // Remove loading indicator
            const loading = document.getElementById('route-loading');
            if (loading) loading.remove();
            
            // Fallback to straight line
            console.log('📍 Using straight line fallback');
            drawStraightLineRoute(coordinates);
        });
}

/**
 * NEW: Fallback function for straight line route
 */
function drawStraightLineRoute(coordinates) {
    if (!window.routeDetailsMapInstance) return;
    
    window.routeDetailsPath = L.polyline(coordinates, {
        color: '#ff9800', // Orange color to indicate straight line
        weight: 3,
        opacity: 0.7,
        dashArray: '10, 5' // Dashed line to show it's not following roads
    }).addTo(window.routeDetailsMapInstance);
    
    window.routeDetailsPath.bindPopup(`
        <div style="text-align: center;">
            <h6>📍 Direct Route</h6>
            <p style="margin: 5px 0; color: #ff9800;"><strong>⚠️ Straight line connection</strong></p>
            <small style="color: #666;">Road routing unavailable</small>
        </div>
    `);
}

/**
 * ENHANCED: Highlight point with route segment highlighting
 */
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
        window.routeDetailsMapInstance.panTo(marker.getLatLng(), { animate: true });
        
        // Add subtle zoom animation
        setTimeout(() => {
            const currentZoom = window.routeDetailsMapInstance.getZoom();
            window.routeDetailsMapInstance.setZoom(Math.max(currentZoom, 15), { animate: true });
        }, 500);
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

/**
 * Toggle weekly days section visibility
 */
function toggleWeeklyDays() {
    const scheduleType = document.getElementById('scheduleType').value;
    const weeklySection = document.getElementById('weeklyDaysSection');
    
    if (scheduleType === 'weekly') {
        weeklySection.style.display = 'block';
        // Make at least one day required for weekly schedules
        const dayCheckboxes = document.querySelectorAll('input[name="schedule_days[]"]');
        dayCheckboxes.forEach(checkbox => checkbox.required = true);
    } else {
        weeklySection.style.display = 'none';
        // Remove day requirements for daily schedules
        const dayCheckboxes = document.querySelectorAll('input[name="schedule_days[]"]');
        dayCheckboxes.forEach(checkbox => {
            checkbox.required = false;
            checkbox.checked = false;
        });
        clearWeeklyDaysValidation();
    }
}

/**
 * Quick select functions for days
 */
function selectWeekdays() {
    // Clear all first
    clearDays();
    // Select Monday to Friday
    ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'].forEach(day => {
        document.getElementById(day).checked = true;
    });
    clearWeeklyDaysValidation();
}

function selectAllDays() {
    const dayCheckboxes = document.querySelectorAll('input[name="schedule_days[]"]');
    dayCheckboxes.forEach(checkbox => checkbox.checked = true);
    clearWeeklyDaysValidation();
}

function clearDays() {
    const dayCheckboxes = document.querySelectorAll('input[name="schedule_days[]"]');
    dayCheckboxes.forEach(checkbox => checkbox.checked = false);
}

function clearWeeklyDaysValidation() {
    const errorDiv = document.getElementById('weeklyDaysError');
    const dayCheckboxes = document.querySelectorAll('input[name="schedule_days[]"]');
    
    if (errorDiv) {
        errorDiv.textContent = '';
    }
    
    dayCheckboxes.forEach(checkbox => {
        checkbox.classList.remove('is-invalid');
    });
}

/**
 * Validate weekly days selection
 */
function validateWeeklyDays() {
    const scheduleType = document.getElementById('scheduleType').value;
    
    if (scheduleType === 'weekly') {
        const selectedDays = document.querySelectorAll('input[name="schedule_days[]"]:checked');
        const errorDiv = document.getElementById('weeklyDaysError');
        const dayCheckboxes = document.querySelectorAll('input[name="schedule_days[]"]');
        
        if (selectedDays.length === 0) {
            errorDiv.textContent = 'Please select at least one day for weekly schedule.';
            dayCheckboxes.forEach(checkbox => checkbox.classList.add('is-invalid'));
            return false;
        } else {
            clearWeeklyDaysValidation();
            return true;
        }
    }
    return true;
}

// Enhanced form validation
document.addEventListener('DOMContentLoaded', function() {
    const addTruckForm = document.getElementById('addTruckForm');
    if (addTruckForm) {
        addTruckForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validate required fields
            const requiredFields = addTruckForm.querySelectorAll('[required]:not([name="schedule_days[]"])');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    const feedback = field.parentElement.querySelector('.invalid-feedback');
                    if (feedback) feedback.textContent = 'This field is required.';
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            // Validate route points
            const startLat = document.getElementById('startLat').value;
            const startLon = document.getElementById('startLon').value;
            const endLat = document.getElementById('endLat').value;
            const endLon = document.getElementById('endLon').value;
            
            if (!startLat || !startLon) {
                const startField = document.getElementById('startPoint');
                startField.classList.add('is-invalid');
                const feedback = startField.parentElement.querySelector('.invalid-feedback');
                if (feedback) feedback.textContent = 'Please select a start point on the map.';
                isValid = false;
            }
            
            if (!endLat || !endLon) {
                const endField = document.getElementById('endPoint');
                endField.classList.add('is-invalid');
                const feedback = endField.parentElement.querySelector('.invalid-feedback');
                if (feedback) feedback.textContent = 'Please select an end point on the map.';
                isValid = false;
            }
            
            // Validate weekly days
            if (!validateWeeklyDays()) {
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            addTruckForm.classList.add('was-validated');
        });
    }
});
