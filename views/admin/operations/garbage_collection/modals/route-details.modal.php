<?php
// Fixed Route Details Modal with Leaflet/OpenStreetMap
?>
<div class="modal fade" id="routeDetailsModal" tabindex="-1" aria-labelledby="routeDetailsModalLabel" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="routeDetailsModalLabel">
                    <i class="fas fa-route me-2"></i>Route Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="route-details-container">
                    <!-- Truck Information & Route Assignment -->
                    <div class="info-section p-3 bg-light border-bottom">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary mb-2">
                                    <i class="fas fa-truck me-1"></i>Truck Information
                                </h6>
                                <div class="info-item">
                                    <strong>Plate Number:</strong> 
                                    <span id="details-plate-number">-</span>
                                </div>
                                <div class="info-item">
                                    <strong>Body Number:</strong> 
                                    <span id="details-body-number">-</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success mb-2">
                                    <i class="fas fa-route me-1"></i>Route Assignment
                                </h6>
                                <div class="info-item">
                                    <strong>Route:</strong> 
                                    <span id="details-route-name">-</span>
                                </div>
                                <div class="info-item">
                                    <strong>Foreman:</strong> 
                                    <span id="details-foreman">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Route Points & Map -->
                    <div class="row g-0">
                        <!-- Route Points List -->
                        <div class="col-md-4 border-end">
                            <div class="points-section">
                                <div class="points-header p-2 bg-secondary text-white">
                                    <h6 class="mb-0">Route Points</h6>
                                </div>
                                <div class="points-list" id="route-points-list">
                                    <div class="empty-points text-center p-4">
                                        <i class="fas fa-map-pin text-muted mb-2"></i>
                                        <p class="text-muted mb-0">No route points</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Map -->
                        <div class="col-md-8">
                            <div class="map-section">
                                <div id="route-map" style="height: 400px; background: #f0f0f0;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
      crossorigin="" />

<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
        crossorigin=""></script>

<style>
/* Simple styles for route details modal */
.route-details-container {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.info-section .info-item {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.points-section {
    height: 400px;
    display: flex;
    flex-direction: column;
}

.points-list {
    flex: 1;
    overflow-y: auto;
    background: #f8f9fa;
}

.point-item {
    padding: 0.75rem;
    border-bottom: 1px solid #dee2e6;
    background: white;
    margin: 0.25rem;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.point-item:hover {
    background: #e3f2fd;
}

.point-item.active {
    background: #bbdefb;
    border-left: 4px solid #2196f3;
}

.point-number {
    display: inline-block;
    width: 20px;
    height: 20px;
    background: #007bff;
    color: white;
    text-align: center;
    border-radius: 50%;
    font-size: 0.75rem;
    font-weight: bold;
    line-height: 20px;
    margin-right: 0.5rem;
}

.point-number.start { background: #28a745; }
.point-number.end { background: #dc3545; }

.point-name {
    font-weight: 600;
    color: #212529;
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
}

.point-address {
    color: #6c757d;
    font-size: 0.75rem;
    line-height: 1.2;
}

.map-section {
    position: relative;
}

#route-map {
    width: 100%;
}

.empty-points {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .modal-dialog.modal-lg {
        margin: 0.5rem;
        max-width: none;
    }
    
    .row.g-0 > .col-md-4,
    .row.g-0 > .col-md-8 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .points-section {
        height: 200px;
    }
    
    #route-map {
        height: 300px !important;
    }
}
</style>

<script>
let routeMap;
let routeMarkers = [];
let routePath;

// Fix accessibility issue with Bootstrap modals
document.addEventListener('DOMContentLoaded', function() {
    // Add event listener to fix aria-hidden focus issue
    document.querySelectorAll('.modal').forEach(function(modal) {
        modal.addEventListener('hide.bs.modal', function() {
            if (document.activeElement) {
                document.activeElement.blur();
            }
        });
    });
});

/**
 * Populate route details modal with truck data
 * This function should be called from your main JavaScript file
 */
function populateRouteDetailsModal(truck) {
    if (!truck) {
        console.error('No truck data provided');
        return;
    }
    
    console.log('Populating modal with truck data:', truck);
    
    // Check if elements exist before setting content
    const plateElement = document.getElementById('details-plate-number');
    const bodyElement = document.getElementById('details-body-number');
    const routeElement = document.getElementById('details-route-name');
    const foremanElement = document.getElementById('details-foreman');
    
    if (!plateElement || !bodyElement || !routeElement || !foremanElement) {
        console.error('One or more required elements not found in DOM');
        return;
    }
    
    // Update truck information safely
    plateElement.textContent = truck.plate_number || '-';
    bodyElement.textContent = truck.body_number || '-';
    routeElement.textContent = truck.route_name || 'Not Assigned';
    foremanElement.textContent = truck.foreman_name || 'Not Assigned';
    
    // Load route points
    loadRoutePoints(truck.route_points || []);
    
    // Initialize map after modal is fully shown
    const modal = document.getElementById('routeDetailsModal');
    modal.addEventListener('shown.bs.modal', function initMapHandler() {
        initializeMap(truck.route_points || []);
        // Remove event listener after first use
        modal.removeEventListener('shown.bs.modal', initMapHandler);
    });
}

/**
 * Load route points into the list
 */
function loadRoutePoints(routePoints) {
    const pointsList = document.getElementById('route-points-list');
    
    if (!pointsList) {
        console.error('Route points list element not found');
        return;
    }
    
    if (!routePoints || routePoints.length === 0) {
        pointsList.innerHTML = `
            <div class="empty-points text-center p-4">
                <i class="fas fa-map-pin text-muted mb-2"></i>
                <p class="text-muted mb-0">No route points</p>
            </div>
        `;
        return;
    }
    
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
    
    pointsList.innerHTML = pointsHTML;
}

/**
 * Initialize Leaflet map
 */
function initializeMap(routePoints) {
    console.log('Initializing map with points:', routePoints);
    
    // Remove existing map if any
    if (routeMap) {
        routeMap.remove();
        routeMap = null;
    }
    
    const mapElement = document.getElementById('route-map');
    if (!mapElement) {
        console.error('Map element not found');
        return;
    }
    
    // Default center (Manila)
    const defaultCenter = [14.5995, 120.9842];
    const defaultZoom = 13;
    
    try {
        // Initialize map
        routeMap = L.map('route-map', {
            center: defaultCenter,
            zoom: defaultZoom,
            zoomControl: true,
            scrollWheelZoom: true
        });
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(routeMap);
        
        // Add route points to map
        if (routePoints && routePoints.length > 0) {
            addPointsToMap(routePoints);
        }
        
        // Fix map size after initialization
        setTimeout(() => {
            if (routeMap) {
                routeMap.invalidateSize();
            }
        }, 300);
        
        console.log('Map initialized successfully');
    } catch (error) {
        console.error('Error initializing map:', error);
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

/**
 * Add points and route path to map
 */
function addPointsToMap(routePoints) {
    if (!routeMap || !routePoints || routePoints.length === 0) return;
    
    // Clear existing markers and path
    clearMap();
    
    const pathCoordinates = [];
    const bounds = L.latLngBounds();
    
    routePoints.forEach((point, index) => {
        // Skip points without coordinates
        if (!point.lat || !point.lng) return;
        
        const lat = parseFloat(point.lat);
        const lng = parseFloat(point.lng);
        
        if (isNaN(lat) || isNaN(lng)) return;
        
        const coordinates = [lat, lng];
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
        
        marker.addTo(routeMap);
        routeMarkers.push({ marker, index });
    });
    
    // Draw route path if more than one point
    if (pathCoordinates.length > 1) {
        routePath = L.polyline(pathCoordinates, {
            color: '#2196f3',
            weight: 4,
            opacity: 0.7,
            smoothFactor: 1
        }).addTo(routeMap);
    }
    
    // Fit map to show all points
    if (pathCoordinates.length > 0) {
        routeMap.fitBounds(bounds, { padding: [20, 20] });
    }
    
    console.log(`Added ${routeMarkers.length} markers to map`);
}

/**
 * Clear all markers and paths from map
 */
function clearMap() {
    if (!routeMap) return;
    
    routeMarkers.forEach(item => {
        if (item.marker) {
            routeMap.removeLayer(item.marker);
        }
    });
    routeMarkers = [];
    
    if (routePath) {
        routeMap.removeLayer(routePath);
        routePath = null;
    }
}

/**
 * Highlight specific point
 */
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
    const markerData = routeMarkers.find(item => item.index === pointIndex);
    if (markerData && routeMap) {
        // Open popup and pan to marker
        markerData.marker.openPopup();
        routeMap.panTo(markerData.marker.getLatLng());
    }
}

// Clean up when modal is closed
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('routeDetailsModal');
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            console.log('Modal closed, cleaning up');
            
            if (routeMap) {
                clearMap();
                routeMap.remove();
                routeMap = null;
            }
            
            // Clear active states
            document.querySelectorAll('.point-item').forEach(item => {
                item.classList.remove('active');
            });
        });
    }
});
</script>
