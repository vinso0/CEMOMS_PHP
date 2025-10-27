<?php
// Simplified Route Details Modal with Leaflet/OpenStreetMap
?>
<div class="modal fade" id="routeDetailsModal" tabindex="-1" aria-labelledby="routeDetailsModalLabel">
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
                                    <div class="loading-points text-center p-4">
                                        <i class="fas fa-spinner fa-spin text-muted mb-2"></i>
                                        <p class="text-muted mb-0">Loading route points...</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Map -->
                        <div class="col-md-8">
                            <div class="map-section">
                                <div class="map-header p-2 bg-dark text-white">
                                    <small>Interactive Route Map</small>
                                </div>
                                <div id="route-map" style="height: 360px; background: #f0f0f0;"></div>
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
/* Styles for route details modal */
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

.loading-points, .empty-points {
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

/**
 * Simple function to populate route details modal
 */
function populateRouteDetailsModal(truck) {
    if (!truck) {
        console.error('No truck data provided');
        return;
    }
    
    console.log('Loading route details for truck:', truck.plate_number);
    
    // Update basic information
    document.getElementById('details-plate-number').textContent = truck.plate_number || '-';
    document.getElementById('details-body-number').textContent = truck.body_number || '-';
    document.getElementById('details-route-name').textContent = truck.route_name || 'Not Assigned';
    document.getElementById('details-foreman').textContent = truck.foreman_name || 'Not Assigned';
    
    // Reset points list to loading state
    const pointsList = document.getElementById('route-points-list');
    pointsList.innerHTML = `
        <div class="loading-points text-center p-4">
            <i class="fas fa-spinner fa-spin text-muted mb-2"></i>
            <p class="text-muted mb-0">Loading route points...</p>
        </div>
    `;
    
    // Initialize map immediately and fetch points
    setTimeout(() => {
    initializeMap();

    const rid = String(truck.route_id || '').trim();
    console.log('Final route_id value:', rid);

    if (rid) {
        fetchRoutePoints(rid);
    } else {
        showNoRoutePoints();
    }
    }, 300);

}

/**
 * Initialize the map
 */
function initializeMap() {
    const mapElement = document.getElementById('route-map');
    
    if (!mapElement) {
        console.error('Map element not found');
        return;
    }
    
    // Remove existing map
    if (routeMap) {
        routeMap.remove();
        routeMap = null;
    }
    
    try {
        console.log('Initializing map...');
        
        // Create map
        routeMap = L.map('route-map', {
            center: [14.5995, 120.9842], // Manila
            zoom: 12,
            zoomControl: true,
            scrollWheelZoom: true
        });
        
        // Add tile layer
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(routeMap);
        
        // Fix map size
        setTimeout(() => {
            if (routeMap) {
                routeMap.invalidateSize();
            }
        }, 200);
        
        console.log('Map initialized successfully');
        
    } catch (error) {
        console.error('Error initializing map:', error);
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

/**
 * Fetch route points via AJAX
 */
function fetchRoutePoints(routeId) {
  console.log('Fetching route points for route ID:', routeId);

  const url = `/admin/operations/garbage_collection/get_route_points?route_id=${encodeURIComponent(routeId)}`;
  console.log('Request URL:', url);

  fetch(url, { headers: { 'Accept': 'application/json' } })
    .then(async (response) => {
      console.log('Response status:', response.status, response.statusText);
      const text = await response.text();
      console.log('Raw body length:', text.length);
      // Try to parse even if there is stray whitespace
      try {
        const data = JSON.parse(text.trim());
        console.log('Parsed JSON:', data);
        if (data && Array.isArray(data.route_points) && data.route_points.length) {
          displayRoutePoints(data.route_points);
          addPointsToMap(data.route_points);
        } else {
          console.warn('No route_points array or empty');
          showNoRoutePoints();
        }
      } catch (e) {
        console.error('JSON parse error:', e, 'Raw body snippet:', text.slice(0, 500));
        showErrorPoints();
      }
    })
    .catch((error) => {
      console.error('Fetch error:', error);
      showErrorPoints();
    });
}


/**
 * Display route points in the list
 */
function displayRoutePoints(routePoints) {
    const pointsList = document.getElementById('route-points-list');
    
    if (!pointsList) {
        console.error('route-points-list not found');
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
                        <div class="point-name">${point.name}</div>
                        <div class="point-address">${point.address}</div>
                    </div>
                </div>
            </div>
        `;
    });
    
    pointsList.innerHTML = pointsHTML;
}

/**
 * Show no route points message
 */
function showNoRoutePoints() {
    const pointsList = document.getElementById('route-points-list');
    pointsList.innerHTML = `
        <div class="empty-points text-center p-4">
            <i class="fas fa-map-pin text-muted mb-2"></i>
            <p class="text-muted mb-0">No route points assigned</p>
        </div>
    `;
}

/**
 * Show error message
 */
function showErrorPoints() {
    const pointsList = document.getElementById('route-points-list');
    pointsList.innerHTML = `
        <div class="empty-points text-center p-4">
            <i class="fas fa-exclamation-triangle text-warning mb-2"></i>
            <p class="text-muted mb-0">Error loading route points</p>
        </div>
    `;
}

/**
 * Add points to map
 */
function addPointsToMap(routePoints) {
    if (!routeMap || !routePoints || routePoints.length === 0) {
        console.log('Cannot add points to map');
        return;
    }
    
    // Clear existing markers
    clearMap();
    
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
            .addTo(routeMap)
            .bindPopup(`
                <div style="min-width: 150px;">
                    <h6>${point.name}</h6>
                    <p class="mb-0"><small>${point.address}</small></p>
                </div>
            `);
        
        routeMarkers.push({ marker, index });
    });
    
    // Draw path
    if (pathCoordinates.length > 1) {
        routePath = L.polyline(pathCoordinates, {
            color: '#2196f3',
            weight: 4,
            opacity: 0.7
        }).addTo(routeMap);
    }
    
    // Fit bounds
    if (pathCoordinates.length > 0) {
        routeMap.fitBounds(bounds, { padding: [20, 20] });
    }
    
    console.log(`Added ${routeMarkers.length} markers to map`);
}

/**
 * Clear map markers and path
 */
function clearMap() {
    if (!routeMap) return;
    
    routeMarkers.forEach(item => {
        routeMap.removeLayer(item.marker);
    });
    routeMarkers = [];
    
    if (routePath) {
        routeMap.removeLayer(routePath);
        routePath = null;
    }
}

/**
 * Highlight point
 */
function highlightPoint(pointIndex) {
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
    const markerData = routeMarkers.find(item => item.index === pointIndex);
    if (markerData && routeMap) {
        markerData.marker.openPopup();
        routeMap.panTo(markerData.marker.getLatLng());
    }
}

// Fix accessibility warning - add to garbage-collection.js
document.addEventListener('DOMContentLoaded', function() {
    // Handle focus blur to prevent aria-hidden warning
    document.addEventListener('hide.bs.modal', function(event) {
        if (event.target.id === 'routeDetailsModal') {
            if (document.activeElement && document.activeElement.blur) {
                document.activeElement.blur();
            }
        }
    });
    
    // Clean up when modal closes
    document.addEventListener('hidden.bs.modal', function(event) {
        if (event.target.id === 'routeDetailsModal') {
            console.log('Cleaning up route details modal...');
            
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
        }
    });
});
</script>
