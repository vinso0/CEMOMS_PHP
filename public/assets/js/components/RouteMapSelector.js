class RouteMapSelector {
    constructor(mapId, options = {}) {
        this.mapId = mapId;
        this.options = {
            defaultLat: 14.5995, // Philippines default
            defaultLng: 120.9842,
            defaultZoom: 13,
            ...options
        };
        
        this.map = null;
        this.markers = {
            start: null,
            mid: null,
            end: null
        };
        
        this.currentMode = 'start';
        
        this.init();
    }
    
    // Replace the init method with this:
init() {
    try {
        this.initMap();
        this.initEventListeners();
        console.log('RouteMapSelector initialized successfully');
    } catch (error) {
        console.error('RouteMapSelector initialization failed:', error);
        throw error;
    }
}

// Replace the initMap method with this:
    initMap() {
        const mapContainer = document.getElementById(this.mapId);
        
        if (!mapContainer) {
            throw new Error(`Map container with id '${this.mapId}' not found`);
        }
        
        if (typeof L === 'undefined') {
            throw new Error('Leaflet library not loaded');
        }
        
        try {
            // Initialize Leaflet Map
            this.map = L.map(this.mapId, {
                center: [this.options.defaultLat, this.options.defaultLng],
                zoom: this.options.defaultZoom,
                zoomControl: true,
                attributionControl: true
            });
            
            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(this.map);
            
            // Add map click listener
            this.map.on('click', (event) => {
                this.handleMapClick(event);
            });
            
            // Force map to resize properly after modal is shown
            setTimeout(() => {
                this.map.invalidateSize();
            }, 100);
            
            // Try to get user's current location
            this.getCurrentLocation();
            
            console.log('Leaflet map initialized successfully');
            
        } catch (error) {
            console.error('Error creating Leaflet map:', error);
            throw new Error('Failed to create map instance');
        }
    }

    
    initEventListeners() {
        // Point mode selector
        document.querySelectorAll('input[name="pointMode"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                this.currentMode = e.target.value;
                this.updateModeIndicator();
            });
        });
        
        // Clear route button
        const clearBtn = document.getElementById('clearRouteBtn');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                this.clearAllMarkers();
            });
        }
    }
    
    handleMapClick(event) {
        const lat = event.latlng.lat;
        const lng = event.latlng.lng;
        
        this.setMarker(this.currentMode, lat, lng);
        this.reverseGeocode(lat, lng, this.currentMode);
        
        // Auto-advance to next mode
        this.advanceMode();
    }
    
    setMarker(type, lat, lng) {
        // Remove existing marker
        if (this.markers[type]) {
            this.map.removeLayer(this.markers[type]);
        }
        
        // Create new marker
        const markerConfig = this.getMarkerConfig(type);
        
        this.markers[type] = L.marker([lat, lng], {
            icon: markerConfig.icon,
            title: markerConfig.title
        }).addTo(this.map);
        
        // Add popup with info
        this.markers[type].bindPopup(markerConfig.title).openPopup();
        
        // Store coordinates
        this.setCoordinates(type, lat, lng);
        
        // Update map bounds to include all markers
        this.fitMapToMarkers();
    }
    
    getMarkerConfig(type) {
        const configs = {
            start: {
                title: 'Start Point',
                icon: L.divIcon({
                    className: 'custom-marker start-marker',
                    html: '<div class="marker-pin"><i class="fas fa-play"></i></div>',
                    iconSize: [30, 40],
                    iconAnchor: [15, 40],
                    popupAnchor: [0, -40]
                })
            },
            mid: {
                title: 'Mid Point',
                icon: L.divIcon({
                    className: 'custom-marker mid-marker',
                    html: '<div class="marker-pin"><i class="fas fa-pause"></i></div>',
                    iconSize: [30, 40],
                    iconAnchor: [15, 40],
                    popupAnchor: [0, -40]
                })
            },
            end: {
                title: 'End Point',
                icon: L.divIcon({
                    className: 'custom-marker end-marker',
                    html: '<div class="marker-pin"><i class="fas fa-stop"></i></div>',
                    iconSize: [30, 40],
                    iconAnchor: [15, 40],
                    popupAnchor: [0, -40]
                })
            }
        };
        
        return configs[type];
    }
    
    reverseGeocode(lat, lng, type) {
        // Using Nominatim (OpenStreetMap) geocoding service
        const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data && data.display_name) {
                    this.setAddressInput(type, data.display_name);
                } else {
                    this.setAddressInput(type, `${lat.toFixed(6)}, ${lng.toFixed(6)}`);
                }
            })
            .catch(error => {
                console.error('Geocoding failed:', error);
                this.setAddressInput(type, `${lat.toFixed(6)}, ${lng.toFixed(6)}`);
            });
    }
    
    setAddressInput(type, address) {
        const inputId = `${type}Point`;
        const input = document.getElementById(inputId);
        
        if (input) {
            input.value = address;
            input.classList.add('updated');
            
            // Remove updated class after animation
            setTimeout(() => {
                input.classList.remove('updated');
            }, 1000);
        }
    }
    
    setCoordinates(type, lat, lng) {
        const latInput = document.getElementById(`${type}Lat`);
        const lngInput = document.getElementById(`${type}Lon`);
        
        if (latInput) latInput.value = lat;
        if (lngInput) lngInput.value = lng;
    }
    
    advanceMode() {
        const modes = ['start', 'mid', 'end'];
        const currentIndex = modes.indexOf(this.currentMode);
        
        if (currentIndex < modes.length - 1) {
            const nextMode = modes[currentIndex + 1];
            const nextRadio = document.getElementById(`${nextMode}Mode`);
            
            if (nextRadio) {
                nextRadio.checked = true;
                this.currentMode = nextMode;
                this.updateModeIndicator();
            }
        }
    }
    
    updateModeIndicator() {
        // Update map cursor style
        const mapContainer = this.map.getContainer();
        mapContainer.style.cursor = this.currentMode === 'start' ? 'crosshair' : 
                                   this.currentMode === 'mid' ? 'copy' : 'cell';
    }
    
    fitMapToMarkers() {
        const markerPositions = [];
        
        Object.values(this.markers).forEach(marker => {
            if (marker) {
                markerPositions.push(marker.getLatLng());
            }
        });
        
        if (markerPositions.length > 0) {
            const group = new L.featureGroup(Object.values(this.markers).filter(m => m));
            this.map.fitBounds(group.getBounds().pad(0.1));
        }
    }
    
    clearAllMarkers() {
        Object.keys(this.markers).forEach(type => {
            if (this.markers[type]) {
                this.map.removeLayer(this.markers[type]);
                this.markers[type] = null;
            }
            
            // Clear form inputs
            this.setAddressInput(type, '');
            this.setCoordinates(type, '', '');
        });
        
        // Reset to start mode
        this.currentMode = 'start';
        const startRadio = document.getElementById('startMode');
        if (startRadio) {
            startRadio.checked = true;
        }
        
        this.updateModeIndicator();
    }
    
    getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    this.map.setView([lat, lng], 15);
                },
                () => {
                    console.log('Geolocation failed, using default location');
                }
            );
        }
    }
    
    // Public method to set a specific point programmatically
    setPoint(type, lat, lng, address = null) {
        this.setMarker(type, lat, lng);
        
        if (address) {
            this.setAddressInput(type, address);
        } else {
            this.reverseGeocode(lat, lng, type);
        }
    }
    
    // Public method to get all route points
    getRoutePoints() {
        const points = {};
        
        Object.keys(this.markers).forEach(type => {
            if (this.markers[type]) {
                const latlng = this.markers[type].getLatLng();
                points[type] = { lat: latlng.lat, lng: latlng.lng };
            } else {
                points[type] = null;
            }
        });
        
        return points;
    }
}
