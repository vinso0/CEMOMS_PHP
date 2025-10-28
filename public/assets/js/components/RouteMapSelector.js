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
            // Clear any existing map instance
            if (this.map) {
                this.map.remove();
            }
            
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
            
            // Try to get user's current location (with better error handling)
            this.getCurrentLocation();
            
            console.log('Leaflet map initialized successfully');
            
        } catch (error) {
            console.error('Error creating Leaflet map:', error);
            throw new Error(`Failed to create map instance: ${error.message}`);
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
        // Direct call to the enhanced version
        this.reverseGeocodeWithFallback(lat, lng, type, 3000);
    }

    reverseGeocodeWithFallback(lat, lng, pointType, timeout = 3000) {
        const inputId = pointType === 'start' ? 'startPoint' :
                    pointType === 'mid' ? 'midPoint' : 'endPoint';
        const input = document.getElementById(inputId);
        
        if (!input) {
            console.error(`Input element not found: ${inputId}`);
            return;
        }
        
        // Show loading state immediately
        this.setAddressInput(pointType, '🔍 Looking up address...');
        input.classList.add('loading');
        
        // Create timeout promise
        const timeoutPromise = new Promise((_, reject) => {
            setTimeout(() => reject(new Error('Timeout')), timeout);
        });
        
        // Create fetch promise
        const fetchPromise = fetch(`/api/geocode_proxy?lat=${lat}&lng=${lng}`)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                return response.json();
            });
        
        // Race between fetch and timeout
        Promise.race([fetchPromise, timeoutPromise])
            .then(data => {
                if (data && data.display_name) {
                    // Clean up the address
                    const parts = data.display_name.split(',').map(s => s.trim());
                    const friendlyAddress = parts.slice(0, 3).join(', ');
                    
                    this.setAddressInput(pointType, friendlyAddress);
                    input.classList.remove('loading');
                    input.classList.add('success');
                    
                    console.log(`✅ Address resolved for ${pointType}: ${friendlyAddress}`);
                } else {
                    throw new Error('No address found');
                }
            })
            .catch(error => {
                console.warn(`⚠️ Geocoding failed for ${pointType}:`, error.message);
                
                // Fallback to coordinates with area hint
                const coordStr = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                const areaHint = this.getAreaHint(lat, lng);
                
                this.setAddressInput(pointType, areaHint ? `${coordStr} (${areaHint})` : coordStr);
                input.classList.remove('loading');
                input.classList.add('fallback');
                
                // Optional: Try again after 3 seconds quietly
                setTimeout(() => {
                    if (input.classList.contains('fallback')) {
                        this.reverseGeocodeQuietRetry(lat, lng, pointType);
                    }
                }, 2000);
            });
    }

    /**
     * Get area hint based on coordinates
     */
    getAreaHint(lat, lng) {
        // Philippines Metro Manila rough boundaries
        if (lat >= 14.4 && lat <= 14.8 && lng >= 120.9 && lng <= 121.1) {
            if (lat >= 14.55 && lat <= 14.65) {
                return "Manila City";
            } else if (lat >= 14.5 && lat < 14.55) {
                return "Makati/Pasay";
            } else if (lat > 14.65) {
                return "Quezon City";
            } else {
                return "Metro Manila";
            }
        }
        return "Philippines";
    }

    /**
     * Quiet retry for geocoding
     */
    reverseGeocodeQuietRetry(lat, lng, pointType) {
    fetch(`/api/geocode_proxy?lat=${lat}&lng=${lng}`)
        .then(r => r.json())
        .then(data => {
        const input = document.getElementById(
            pointType === 'start' ? 'startPoint' :
            pointType === 'mid' ? 'midPoint' : 'endPoint'
        );
        if (data && data.display_name && input && input.classList.contains('fallback')) {
            const parts = data.display_name.split(',').map(s => s.trim());
            const friendlyAddress = parts.slice(0, 3).join(', ');
            this.setAddressInput(pointType, friendlyAddress);
            input.classList.remove('fallback');
            input.classList.add('success');
            console.log(`✅ Retry via proxy successful: ${friendlyAddress}`);
        }
        })
        .catch(() => { /* silent */ });
    }



    setAddressInput(type, value) {
        const inputId = type === 'start' ? 'startPoint' :
                    type === 'mid' ? 'midPoint' : 'endPoint';
        const input = document.getElementById(inputId);
        
        if (input) {
            input.value = value;
            // Remove any existing state classes
            input.classList.remove('loading', 'success', 'fallback');
            
            // Trigger the success animation if it's a good address
            if (!value.includes('Looking up') && !value.match(/^\d+\.\d+, \d+\.\d+/)) {
                input.classList.add('success');
                // Remove success class after animation
                setTimeout(() => input.classList.remove('success'), 800);
            }
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
                    
                    // Only center if we're still near the default location
                    this.map.setView([lat, lng], 15);
                    console.log('✅ Centered map on user location');
                },
                (error) => {
                    console.log('ℹ️ Geolocation failed, using default Manila location');
                    // Don't change map view, just log the info
                },
                {
                    enableHighAccuracy: false,
                    timeout: 5000,
                    maximumAge: 300000
                }
            );
        } else {
            console.log('ℹ️ Geolocation not supported, using default location');
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

    // Add this public method
    refreshMapSize() {
        if (this.map) {
            setTimeout(() => {
                this.map.invalidateSize();
            }, 100);
        }
    }

    // Also add a destroy method for cleanup
    destroy() {
        if (this.map) {
            this.map.remove();
            this.map = null;
        }
        this.markers = { start: null, mid: null, end: null };
    }

}
