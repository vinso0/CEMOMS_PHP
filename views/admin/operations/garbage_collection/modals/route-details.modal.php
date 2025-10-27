<?php
// Debugging Route Details Modal with Leaflet/OpenStreetMap
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
                    <!-- Debug Info -->
                    <div class="debug-info p-2 bg-warning text-dark border-bottom">
                        <small><strong>Debug:</strong> <span id="debug-info">Waiting for truck data...</span></small>
                    </div>
                    
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
                                <div id="route-map" style="height: 400px; background: #f0f0f0;">
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <div class="text-center">
                                            <i class="fas fa-map text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Map will load here</p>
                                        </div>
                                    </div>
                                </div>
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

.debug-info {
    font-family: monospace;
    font-size: 12px;
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
// DEBUG VERSION - Enhanced logging
let routeMap;
let routeMarkers = [];
let routePath;

/**
 * DEBUG: Populate route details modal with extensive logging
 */
function populateRouteDetailsModal(truck) {
    console.log('=== ROUTE DETAILS MODAL DEBUG ===');
    console.log('1. Function called with truck data:', truck);
    
    if (!truck) {
        console.error('ERROR: No truck data provided');
        document.getElementById('debug-info').textContent = 'ERROR: No truck data provided';
        return;
    }
    
    // Show debug info
    document.getElementById('debug-info').textContent = `Loaded truck: ${truck.plate_number || 'Unknown'} - Checking route points...`;
    
    // Update basic information
    console.log('2. Updating basic truck information...');
    const plateElement = document.getElementById('details-plate-number');
    const bodyElement = document.getElementById('details-body-number');
    const routeElement = document.getElementById('details-route-name');
    const foremanElement = document.getElementById('details-foreman');
    
    if (plateElement) plateElement.textContent = truck.plate_number || '-';
    if (bodyElement) bodyElement.textContent = truck.body_number || '-';
    if (routeElement) routeElement.textContent = truck.route_name || 'Not Assigned';
    if (foremanElement) foremanElement.textContent = truck.foreman_name || 'Not Assigned';
    
    // Debug: Show all available properties
    console.log('3. All truck properties:', Object.keys(truck));
    console.log('4. Looking for route coordinates...');
    
    // Check for different possible coordinate field names
    const possibleFields = [
        'start_lat', 'start_lng', 'start_lon', 'start_point',
        'mid_lat', 'mid_lng', 'mid_lon', 'mid_point', 
        'end_lat', 'end_lng', 'end_lon', 'end_point',
        'route_points', 'coordinates', 'waypoints'
    ];
    
    console.log('5. Checking for coordinate fields:');
    possibleFields.forEach(field => {
        if (truck.hasOwnProperty(field)) {
            console.log(`   ✓ Found: ${field} =`, truck[field]);
        }
    });
    
    // Build route points array
    const routePoints = buildRoutePointsFromTruck(truck);
    console.log('6. Built route points:', routePoints);
    
    // Update debug info
    document.getElementById('debug-info').textContent = `Found ${routePoints.length} route points`;
    
    // Load route points
    loadRoutePointsDebug(routePoints);
    
    // Initialize map
    const modal = document.getElementById('routeDetailsModal');
    if (modal) {
        modal.addEventListener('shown.bs.modal', function initMapHandler() {
            console.log('7. Modal shown, initializing map...');
            initializeMapDebug(routePoints);
            // Remove event listener after first use
            modal.removeEventListener('shown.bs.modal', initMapHandler);
        });
    }
}

/**
 * DEBUG: Build route points from truck data with extensive checking
 */
function buildRoutePointsFromTruck(truck) {
    const routePoints = [];
    console.log('Building route points from truck data...');
    
    // Method 1: Check for start/mid/end points
    if (truck.start_lat && truck.start_lng) {
        const startPoint = {
            name: truck.start_point || 'Start Point',
            address: truck.start_address || 'Start location',
            lat: parseFloat(truck.start_lat),
            lng: parseFloat(truck.start_lng)
        };
        
        if (!isNaN(startPoint.lat) && !isNaN(startPoint.lng)) {
            routePoints.push(startPoint);
            console.log('Added start point:', startPoint);
        }
    }
    
    if (truck.mid_lat && truck.mid_lng) {
        const midPoint = {
            name: truck.mid_point || 'Mid Point',
            address: truck.mid_address || 'Collection point',
            lat: parseFloat(truck.mid_lat),
            lng: parseFloat(truck.mid_lng)
        };
        
        if (!isNaN(midPoint.lat) && !isNaN(midPoint.lng)) {
            routePoints.push(midPoint);
            console.log('Added mid point:', midPoint);
        }
    }
    
    if (truck.end_lat && truck.end_lng) {
        const endPoint = {
            name: truck.end_point || 'End Point',
            address: truck.end_address || 'End location',
            lat: parseFloat(truck.end_lat),
            lng: parseFloat(truck.end_lng)
        };
        
        if (!isNaN(endPoint.lat) && !isNaN(endPoint.lng)) {
            routePoints.push(endPoint);
            console.log('Added end point:', endPoint);
        }
    }
    
    // Method 2: Check for route_points array
    if (truck.route_points && Array.isArray(truck.route_points)) {
        console.log('Found route_points array:', truck.route_points);
        truck.route_points.forEach((point, index) => {
            if (point.lat && point.lng) {
                routePoints.push({
                    name: point.name || `Point ${index + 1}`,
                    address: point.address || 'Address not available',
                    lat: parseFloat(point.lat),
                    lng: parseFloat(point.lng)
                });
            }
        });
    }
    
    // Method 3: Add some sample data if no points found (for testing)
    if (routePoints.length === 0) {
        console.warn('No route points found, adding sample data for testing...');
        routePoints.push(
            {
                name: 'Sample Start Point',
                address: 'Manila City Hall, Manila',
                lat: 14.5995,
                lng: 120.9842
            },
            {
                name: 'Sample End Point',
                address: 'Rizal Park, Manila',
                lat: 14.5832,
                lng: 120.9794
            }
        );
    }
    
    return routePoints;
}

/**
 * DEBUG: Load route points with logging
 */
function loadRoutePointsDebug(routePoints) {
    console.log('Loading route points into list...');
    const pointsList = document.getElementById('route-points-list');
    
    if (!pointsList) {
        console.error('route-points-list element not found');
        return;
    }
    
    if (!routePoints || routePoints.length === 0) {
        console.log('No route points to display');
        pointsList.innerHTML = `
            <div class="empty-points text-center p-4">
                <i class="fas fa-map-pin text-muted mb-2"></i>
                <p class="text-muted mb-0">No route points</p>
            </div>
        `;
        return;
    }
    
    console.log(`Displaying ${routePoints.length} route points`);
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
    console.log('Route points list updated successfully');
}

/**
 * DEBUG: Initialize map with extensive logging
 */
function initializeMapDebug(routePoints) {
    console.log('=== MAP INITIALIZATION DEBUG ===');
    console.log('1. Starting map initialization with points:', routePoints);
    
    const mapElement = document.getElementById('route-map');
    if (!mapElement) {
        console.error('ERROR: route-map element not found');
        return;
    }
    
    // Check if Leaflet is loaded
    if (typeof L === 'undefined') {
        console.error('ERROR: Leaflet library not loaded');
        mapElement.innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100 text-center text-danger">
                <div>
                    <i class="fas fa-exclamation-triangle mb-2"></i>
                    <p class="mb-0">Leaflet library not loaded</p>
                </div>
            </div>
        `;
        return;
    }
    
    console.log('2. Leaflet library loaded successfully');
    
    // Remove existing map if any
    if (routeMap) {
        console.log('3. Removing existing map instance');
        routeMap.remove();
        routeMap = null;
    }
    
    // Clear map container
    mapElement.innerHTML = '';
    console.log('4. Map container cleared');
    
    try {
        // Default center (Manila)
        const defaultCenter = [14.5995, 120.9842];
        const defaultZoom = 13;
        
        console.log('5. Creating map instance...');
        routeMap = L.map('route-map', {
            center: defaultCenter,
            zoom: defaultZoom,
            zoomControl: true,
            scrollWheelZoom: true
        });
        
        console.log('6. Adding tile layer...');
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(routeMap);
        
        console.log('7. Tile layer added successfully');
        
        // Add route points to map
        if (routePoints && routePoints.length > 0) {
            console.log('8. Adding route points to map...');
            addPointsToMapDebug(routePoints);
        } else {
            console.log('8. No route points to add to map');
        }
        
        // Fix map size after initialization
        setTimeout(() => {
            if (routeMap) {
                console.log('9. Invalidating map size...');
                routeMap.invalidateSize();
                console.log('10. Map initialization completed successfully!');
            }
        }, 300);
        
    } catch (error) {
        console.error('ERROR during map initialization:', error);
        mapElement.innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100 text-center text-danger">
                <div>
                    <i class="fas fa-exclamation-triangle mb-2"></i>
                    <p class="mb-0">Error initializing map</p>
                    <small>${error.message}</small>
                </div>
            </div>
        `;
    }
}

/**
 * DEBUG: Add points to map with logging
 */
function addPointsToMapDebug(routePoints) {
    console.log('Adding points to map:', routePoints);
    
    if (!routeMap || !routePoints || routePoints.length === 0) {
        console.log('Cannot add points: missing map or points');
        return;
    }
    
    // Clear existing markers
    clearMapMarkers();
    
    const pathCoordinates = [];
    const bounds = L.latLngBounds();
    
    routePoints.forEach((point, index) => {
        console.log(`Processing point ${index + 1}:`, point);
        
        if (!point.lat || !point.lng || isNaN(point.lat) || isNaN(point.lng)) {
            console.warn(`Skipping point ${index + 1}: invalid coordinates`);
            return;
        }
        
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
        
        marker.addTo(routeMap);
        routeMarkers.push({ marker, index });
        console.log(`Added marker ${index + 1} at [${point.lat}, ${point.lng}]`);
    });
    
    // Draw route path if more than one point
    if (pathCoordinates.length > 1) {
        console.log('Drawing route path...');
        routePath = L.polyline(pathCoordinates, {
            color: '#2196f3',
            weight: 4,
            opacity: 0.7,
            smoothFactor: 1
        }).addTo(routeMap);
    }
    
    // Fit map to show all points
    if (pathCoordinates.length > 0) {
        console.log('Fitting map bounds to show all points');
        routeMap.fitBounds(bounds, { padding: [20, 20] });
    }
    
    console.log(`Successfully added ${routeMarkers.length} markers to map`);
}

/**
 * Clear all markers and paths from map
 */
function clearMapMarkers() {
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
            console.log('Route details modal closed, cleaning up');
            
            if (routeMap) {
                routeMap.remove();
                routeMap = null;
            }
            
            routeMarkers = [];
            routePath = null;
            
            // Clear active states
            document.querySelectorAll('.point-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Reset debug info
            const debugInfo = document.getElementById('debug-info');
            if (debugInfo) {
                debugInfo.textContent = 'Waiting for truck data...';
            }
        });
    }
});
</script>
