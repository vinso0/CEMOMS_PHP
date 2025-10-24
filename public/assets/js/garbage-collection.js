// Add this function to the existing GarbageCollectionManager class

class GarbageCollectionManager {
    /**
     * View truck details in modal with map
     */
    viewTruckDetails(truck) {
    const modal = document.getElementById('truckDetailsModal');
    const content = document.getElementById('truckDetailsContent');
    
    // Generate the details content
    content.innerHTML = `
        <div class="truck-details-grid">
            <div class="truck-details-info">
                <div class="details-section">
                    <h6><i class="fas fa-truck me-2"></i>Truck Information</h6>
                    <div class="detail-row">
                        <span class="detail-label">Plate Number:</span>
                        <span class="detail-value"><strong>${truck.plate_number}</strong></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Body Number:</span>
                        <span class="detail-value">${truck.body_number}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Truck ID:</span>
                        <span class="detail-value">#${truck.id}</span>
                    </div>
                </div>

                <div class="details-section">
                    <h6><i class="fas fa-user-tie me-2"></i>Assignment</h6>
                    <div class="detail-row">
                        <span class="detail-label">Foreman:</span>
                        <span class="detail-value">${truck.foreman_name || 'Not Assigned'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Schedule Type:</span>
                        <span class="detail-value">
                            <span class="badge bg-info">${truck.schedule || 'N/A'}</span>
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value">
                            <span class="status-badge status-${(truck.status || 'parked').toLowerCase().replace(' ', '-')}">
                                <i class="fas fa-circle me-1"></i>
                                ${truck.status || 'Parked'}
                            </span>
                        </span>
                    </div>
                </div>

                <div class="details-section">
                    <h6><i class="fas fa-route me-2"></i>Route Information</h6>
                    <div class="detail-row">
                        <span class="detail-label">Route Name:</span>
                        <span class="detail-value"><strong>${truck.route_name || 'No Route Assigned'}</strong></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Start Point:</span>
                        <span class="detail-value">${truck.start_point || 'N/A'}</span>
                    </div>
                    ${truck.mid_point ? `
                    <div class="detail-row">
                        <span class="detail-label">Mid Point:</span>
                        <span class="detail-value">${truck.mid_point}</span>
                    </div>
                    ` : ''}
                    <div class="detail-row">
                        <span class="detail-label">End Point:</span>
                        <span class="detail-value">${truck.end_point || 'N/A'}</span>
                    </div>
                </div>
            </div>
            
            <div class="truck-details-map-section">
                <div class="details-section">
                    <h6><i class="fas fa-map me-2"></i>Route Map</h6>
                    <div class="route-map-container">
                        <div id="truckDetailsMap" class="route-details-map"></div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Show the modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    // Initialize map after modal is shown
    modal.addEventListener('shown.bs.modal', () => {
        this.initTruckDetailsMap(truck);
    }, { once: true });
}

/**
 * Initialize map for truck details
 */
initTruckDetailsMap(truck) {
    const mapContainer = document.getElementById('truckDetailsMap');
    
    if (!mapContainer) return;
    
    try {
        // Clear any existing map
        mapContainer.innerHTML = '';
        
        // Initialize Leaflet Map
        const map = L.map('truckDetailsMap', {
            center: [14.5995, 120.9842], // Default Philippines coordinates
            zoom: 13,
            zoomControl: true
        });
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Add markers if coordinates are available
        const markers = [];
        let bounds = null;
        
        // Start point
        if (truck.start_lat && truck.start_lon) {
            const startMarker = L.marker([truck.start_lat, truck.start_lon], {
                icon: this.createCustomIcon('start')
            }).addTo(map);
            
            startMarker.bindPopup(`
                <div class="map-popup">
                    <strong>Start Point</strong><br>
                    ${truck.start_point || 'Unknown Location'}
                </div>
            `);
            
            markers.push(startMarker);
        }
        
        // Mid point
        if (truck.mid_lat && truck.mid_lon) {
            const midMarker = L.marker([truck.mid_lat, truck.mid_lon], {
                icon: this.createCustomIcon('mid')
            }).addTo(map);
            
            midMarker.bindPopup(`
                <div class="map-popup">
                    <strong>Mid Point</strong><br>
                    ${truck.mid_point || 'Unknown Location'}
                </div>
            `);
            
            markers.push(midMarker);
        }
        
        // End point
        if (truck.end_lat && truck.end_lon) {
            const endMarker = L.marker([truck.end_lat, truck.end_lon], {
                icon: this.createCustomIcon('end')
            }).addTo(map);
            
            endMarker.bindPopup(`
                <div class="map-popup">
                    <strong>End Point</strong><br>
                    ${truck.end_point || 'Unknown Location'}
                </div>
            `);
            
            markers.push(endMarker);
        }
        
        // Fit map to show all markers
        if (markers.length > 0) {
            const group = new L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.1));
        }
        
        // Force map resize
        setTimeout(() => {
            map.invalidateSize();
        }, 100);
        
    } catch (error) {
        console.error('Error initializing truck details map:', error);
        mapContainer.innerHTML = `
            <div class="map-error">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <p>Unable to load map</p>
            </div>
        `;
    }
}

/**
 * Create custom map icons
 */
createCustomIcon(type) {
    const icons = {
        start: { color: '#28a745', icon: 'play' },
        mid: { color: '#ffc107', icon: 'pause' },
        end: { color: '#dc3545', icon: 'stop' }
    };
    
    const config = icons[type] || icons.start;
    
    return L.divIcon({
        className: `custom-marker ${type}-marker`,
        html: `<div class="marker-pin" style="background: ${config.color}"><i class="fas fa-${config.icon}"></i></div>`,
        iconSize: [30, 40],
        iconAnchor: [15, 40],
        popupAnchor: [0, -40]
    });
}

// Static method for onclick handlers
static viewTruckDetails(truck) {
    if (window.garbageCollectionManager) {
        window.garbageCollectionManager.viewTruckDetails(truck);
    }
}
}
