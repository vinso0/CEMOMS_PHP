// Interactive Route Map Component for Garbage Collection
// This component integrates Leaflet.js for visual route point selection

class RouteMapSelector {
    constructor(containerId, options = {}) {
        this.containerId = containerId;
        this.map = null;
        this.markers = {
            start: null,
            mid: null,
            end: null
        };
        this.polyline = null;
        this.options = {
            center: [14.6396, 120.9822], // Caloocan City coordinates
            zoom: 14,
            ...options
        };
        
        // Input field references
        this.inputs = {
            startPoint: null,
            startLat: null,
            startLon: null,
            midPoint: null,
            midLat: null,
            midLon: null,
            endPoint: null,
            endLat: null,
            endLon: null
        };
        
        // Marker icons
        this.icons = this.createMarkerIcons();
        
        // Add reference to input groups
        this.inputGroups = {
            start: {
                point: document.getElementById(`${options.modalId}-start-point`),
                lat: document.getElementById(`${options.modalId}-start-lat`),
                lon: document.getElementById(`${options.modalId}-start-lon`),
                clear: document.querySelector(`#${options.modalId}TruckModal .clear-point[data-point-type="start"]`)
            },
            mid: {
                point: document.getElementById(`${options.modalId}-mid-point`),
                lat: document.getElementById(`${options.modalId}-mid-lat`),
                lon: document.getElementById(`${options.modalId}-mid-lon`),
                clear: document.querySelector(`#${options.modalId}TruckModal .clear-point[data-point-type="mid"]`)
            },
            end: {
                point: document.getElementById(`${options.modalId}-end-point`),
                lat: document.getElementById(`${options.modalId}-end-lat`),
                lon: document.getElementById(`${options.modalId}-end-lon`),
                clear: document.querySelector(`#${options.modalId}TruckModal .clear-point[data-point-type="end"]`)
            }
        };

        this.init();
    }
    
    createMarkerIcons() {
        return {
            start: L.icon({
                iconUrl: 'data:image/svg+xml;base64,' + btoa(`
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32">
                        <path fill="#4CAF50" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                        <circle cx="12" cy="9" r="3" fill="white"/>
                        <text x="12" y="11" font-size="8" fill="#4CAF50" text-anchor="middle" font-weight="bold">S</text>
                    </svg>
                `),
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            }),
            mid: L.icon({
                iconUrl: 'data:image/svg+xml;base64,' + btoa(`
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32">
                        <path fill="#FF9800" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                        <circle cx="12" cy="9" r="3" fill="white"/>
                        <text x="12" y="11" font-size="8" fill="#FF9800" text-anchor="middle" font-weight="bold">M</text>
                    </svg>
                `),
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            }),
            end: L.icon({
                iconUrl: 'data:image/svg+xml;base64,' + btoa(`
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32">
                        <path fill="#f44336" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                        <circle cx="12" cy="9" r="3" fill="white"/>
                        <text x="12" y="11" font-size="8" fill="#f44336" text-anchor="middle" font-weight="bold">E</text>
                    </svg>
                `),
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            })
        };
    }
    
    init() {
        this.createMap();
        this.setupEventListeners();
    }
    
    createMap() {
        const container = document.getElementById(this.containerId);
        if (!container) {
            console.error(`Map container with id "${this.containerId}" not found`);
            return;
        }
        
        // Initialize map
        this.map = L.map(this.containerId).setView(this.options.center, this.options.zoom);
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(this.map);
        
        // Add map click handler
        this.map.on('click', (e) => this.handleMapClick(e));
        
    }
    
    setupEventListeners() {
        // Link input fields
        const modalId = this.options.modalId || '';
        const prefix = modalId ? `${modalId}-` : '';
        
        this.inputs = {
            startPoint: document.getElementById(`${prefix}start-point`),
            startLat: document.getElementById(`${prefix}start-lat`),
            startLon: document.getElementById(`${prefix}start-lon`),
            midPoint: document.getElementById(`${prefix}mid-point`),
            midLat: document.getElementById(`${prefix}mid-lat`),
            midLon: document.getElementById(`${prefix}mid-lon`),
            endPoint: document.getElementById(`${prefix}end-point`),
            endLat: document.getElementById(`${prefix}end-lat`),
            endLon: document.getElementById(`${prefix}end-lon`)
        };
    }
    
    setupClearButtons() {
        Object.keys(this.inputGroups).forEach(type => {
            const group = this.inputGroups[type];
            if (group.clear) {
                group.clear.addEventListener('click', () => {
                    this.clearPoint(type);
                });
            }
        });
    }

    handleMapClick(e) {
        const { lat, lng } = e.latlng;
        
        // Determine which point to set based on what's missing
        let pointType = null;
        if (!this.markers.start) pointType = 'start';
        else if (!this.markers.end) pointType = 'end';
        else if (!this.markers.mid) pointType = 'mid';
        
        if (pointType) {
            this.addMarker(pointType, lat, lng);
        } else {
            this.showNotification('All points are set. Clear a point first to place a new one.', 'info');
        }
    }
    
    async addMarker(type, lat, lng) {
        // Remove existing marker if any
        if (this.markers[type]) {
            this.map.removeLayer(this.markers[type]);
        }

        const marker = L.marker([lat, lng], {
            icon: this.icons[type],
            draggable: true,
            title: `${type.charAt(0).toUpperCase() + type.slice(1)} Point`
        }).addTo(this.map);

        // Update marker and inputs when dragged
        marker.on('dragend', (e) => {
            const pos = e.target.getLatLng();
            this.updateMarkerPosition(type, pos.lat, pos.lng);
        });

        this.markers[type] = marker;

        // Get and set address
        try {
            const address = await this.reverseGeocode(lat, lng);
            this.updateInputs(type, lat, lng, address);
            
            // Update marker popup
            marker.bindPopup(`
                <strong>${type.charAt(0).toUpperCase() + type.slice(1)} Point</strong><br>
                ${address}
            `).openPopup();
        } catch (error) {
            console.error('Error getting address:', error);
            this.updateInputs(type, lat, lng, `Location: ${lat.toFixed(6)}, ${lng.toFixed(6)}`);
        }

        this.updatePolyline();
    }
    
    clearPoint(type) {
        // Remove marker
        if (this.markers[type]) {
            this.map.removeLayer(this.markers[type]);
            this.markers[type] = null;
        }

        // Clear input fields
        const group = this.inputGroups[type];
        if (group.point) group.point.value = '';
        if (group.lat) group.lat.value = '';
        if (group.lon) group.lon.value = '';

        // Update polyline
        this.updatePolyline();
    }

    async reverseGeocode(lat, lng) {
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`, {
                headers: {
                    'User-Agent': 'CEMOMS/1.0'
                }
            });
            
            if (!response.ok) throw new Error('Geocoding failed');
            
            const data = await response.json();
            return this.formatAddress(data);
        } catch (error) {
            console.error('Reverse geocoding error:', error);
            return `Location: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        }
    }
    
    formatAddress(data) {
        if (!data || !data.address) return 'Unknown location';
        
        const addr = data.address;
        const parts = [];
        
        if (addr.house_number) parts.push(addr.house_number);
        if (addr.road) parts.push(addr.road);
        else if (addr.street) parts.push(addr.street);
        
        if (addr.suburb) parts.push(addr.suburb);
        else if (addr.neighbourhood) parts.push(addr.neighbourhood);
        
        if (addr.village) parts.push('Brgy. ' + addr.village);
        else if (addr.quarter) parts.push('Brgy. ' + addr.quarter);
        
        if (addr.city) parts.push(addr.city);
        else if (addr.municipality) parts.push(addr.municipality);
        
        return parts.join(', ') || data.display_name;
    }
    
    updateInputs(type, lat, lng, address) {
        const group = this.inputGroups[type];
        if (group) {
            if (group.point) group.point.value = address;
            if (group.lat) group.lat.value = lat.toFixed(6);
            if (group.lon) group.lon.value = lng.toFixed(6);
        }
    }
    
    updateMarkerPosition(type, lat, lng) {
        this.updateInputs(type, lat, lng);
        this.reverseGeocode(type, lat, lng);
        this.updatePolyline();
    }
    
    updatePolyline() {
        // Remove existing polyline
        if (this.polyline) {
            this.map.removeLayer(this.polyline);
        }
        
        // Create array of marker positions
        const positions = [];
        if (this.markers.start) positions.push(this.markers.start.getLatLng());
        if (this.markers.mid) positions.push(this.markers.mid.getLatLng());
        if (this.markers.end) positions.push(this.markers.end.getLatLng());
        
        // Draw polyline if we have at least 2 points
        if (positions.length >= 2) {
            this.polyline = L.polyline(positions, {
                color: '#2196F3',
                weight: 4,
                opacity: 0.7,
                dashArray: '10, 10'
            }).addTo(this.map);
            
            // Fit map to show all markers
            this.map.fitBounds(this.polyline.getBounds(), { padding: [50, 50] });
        }
    }
    
    clearAllMarkers() {
        // Remove all markers
        Object.keys(this.markers).forEach(type => {
            if (this.markers[type]) {
                this.map.removeLayer(this.markers[type]);
                this.markers[type] = null;
            }
        });
        
        // Remove polyline
        if (this.polyline) {
            this.map.removeLayer(this.polyline);
            this.polyline = null;
        }
        
        // Clear all input fields
        Object.keys(this.inputs).forEach(key => {
            if (this.inputs[key]) {
                this.inputs[key].value = '';
            }
        });
        
        this.showNotification('Route cleared. Click on map to set new points.', 'info');
    }
    
    loadExistingRoute(startLat, startLon, midLat, midLon, endLat, endLon) {
        // Clear existing markers first
        this.clearAllMarkers();
        
        // Add markers for existing coordinates
        if (startLat && startLon) {
            this.addMarker('start', parseFloat(startLat), parseFloat(startLon));
        }
        
        if (midLat && midLon) {
            this.addMarker('mid', parseFloat(midLat), parseFloat(midLon));
        }
        
        if (endLat && endLon) {
            this.addMarker('end', parseFloat(endLat), parseFloat(endLon));
        }
        
        // Update polyline
        setTimeout(() => this.updatePolyline(), 500);
    }
    
    showNotification(message, type = 'info') {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `map-toast map-toast-${type}`;
        toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            ${message}
        `;
        
        document.body.appendChild(toast);
        
        // Trigger animation
        setTimeout(() => toast.classList.add('show'), 10);
        
        // Remove after 3 seconds
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    invalidateSize() {
        if (this.map) {
            this.map.invalidateSize();
        }
    }

    destroy() {
        if (this.map) {
            this.map.remove();
            this.map = null;
        }
    }
}

// Toast notification styles (add to page)
const toastStyles = `
<style>
.map-toast {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: white;
    padding: 12px 16px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 10000;
    transform: translateY(100px);
    opacity: 0;
    transition: all 0.3s ease;
}

.map-toast.show {
    transform: translateY(0);
    opacity: 1;
}

.map-toast-success { border-left: 4px solid #4CAF50; }
.map-toast-error { border-left: 4px solid #f44336; }
.map-toast-info { border-left: 4px solid #2196F3; }

.map-toast i {
    font-size: 1.2rem;
}

.map-toast-success i { color: #4CAF50; }
.map-toast-error i { color: #f44336; }
.map-toast-info i { color: #2196F3; }

@media (max-width: 768px) {
    .map-toast {
        bottom: 10px;
        right: 10px;
        left: 10px;
        font-size: 0.9rem;
    }
}
</style>
`;

// Add styles to page
if (!document.getElementById('map-toast-styles')) {
    const styleEl = document.createElement('div');
    styleEl.id = 'map-toast-styles';
    styleEl.innerHTML = toastStyles;
    document.head.appendChild(styleEl);
}

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = RouteMapSelector;
}