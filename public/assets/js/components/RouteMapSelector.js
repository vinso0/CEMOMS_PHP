class RouteMapSelector {
    constructor(mapId, options = {}) {
        this.mapId = mapId;
        this.options = {
            defaultLat: 14.5995,
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
        this.isDragging = false;
        this._destroyed = false;
        
        this.init();
    }
    
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

    initMap() {
        const mapContainer = document.getElementById(this.mapId);
        
        if (!mapContainer) {
            throw new Error(`Map container with id '${this.mapId}' not found`);
        }
        
        // CRITICAL: Clear existing Leaflet instances safely
        this.destroy();
        
        // Clear container completely
        if (mapContainer._leaflet_id) {
            mapContainer._leaflet = false;
            mapContainer._leaflet_id = null;
        }
        mapContainer.innerHTML = '';
        
        if (typeof L === 'undefined') {
            throw new Error('Leaflet library not loaded');
        }
        
        try {
            // Initialize fresh map instance
            this.map = L.map(this.mapId, {
                center: [this.options.defaultLat, this.options.defaultLng],
                zoom: this.options.defaultZoom,
                zoomControl: true,
                attributionControl: true,
                dragging: true,
                scrollWheelZoom: true,
                doubleClickZoom: true
            });
            
            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(this.map);
            
            // CRITICAL FIX: Add drag safety checks
            this.map.on('dragstart', () => {
                this.isDragging = true;
                // Check if container is still valid during drag
                const el = this.map.getContainer();
                if (!el || !document.body.contains(el) || el.offsetParent === null) {
                    console.warn('Map container detached during drag, stopping interaction');
                    this.map.stop();
                    this.isDragging = false;
                    return false;
                }
            });
            
            this.map.on('dragend', () => {
                this.isDragging = false;
            });
            
            // Map click handler
            this.map.on('click', (event) => {
                this.handleMapClick(event);
            });
            
            // CRITICAL FIX: Safe resize with container validation
            this.map.whenReady(() => {
                this.safeInvalidateSize();
            });
            
            console.log('✅ Map initialized successfully');
            
        } catch (error) {
            console.error('❌ Map initialization failed:', error);
            throw error;
        }
    }

    // CRITICAL FIX: Safe resize method
    safeInvalidateSize() {
        if (this._destroyed || !this.map) return;
        
        const doResize = () => {
            if (this._destroyed || !this.map) return;
            
            const el = this.map.getContainer();
            if (el && el.offsetParent !== null && document.body.contains(el)) {
                try {
                    this.map.invalidateSize();
                    console.log('✅ Map size invalidated safely');
                } catch (error) {
                    console.warn('Error during map resize:', error);
                }
            } else {
                // Container not ready, try again
                requestAnimationFrame(doResize);
            }
        };
        
        requestAnimationFrame(doResize);
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
        this.reverseGeocodeWithFallback(lat, lng, type, 3000);
    }

    reverseGeocodeWithFallback(lat, lng, pointType, timeout = 3000) {
        if (this._destroyed) return;
        
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
                if (this._destroyed) return;
                
                if (data && data.display_name) {
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
                if (this._destroyed) return;
                
                console.warn(`⚠️ Geocoding failed for ${pointType}:`, error.message);
                
                const coordStr = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                const areaHint = this.getAreaHint(lat, lng);
                
                this.setAddressInput(pointType, areaHint ? `${coordStr} (${areaHint})` : coordStr);
                input.classList.remove('loading');
                input.classList.add('fallback');
            });
    }

    getAreaHint(lat, lng) {
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

    setAddressInput(type, value) {
        if (this._destroyed) return;
        
        const inputId = type === 'start' ? 'startPoint' :
                    type === 'mid' ? 'midPoint' : 'endPoint';
        const input = document.getElementById(inputId);
        
        if (input) {
            input.value = value;
            input.classList.remove('loading', 'success', 'fallback');
            
            if (!value.includes('Looking up') && !value.match(/^\d+\.\d+, \d+\.\d+/)) {
                input.classList.add('success');
                setTimeout(() => input.classList.remove('success'), 800);
            }
        }
    }

    setCoordinates(type, lat, lng) {
        if (this._destroyed) return;
        
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
        if (!this.map) return;
        const mapContainer = this.map.getContainer();
        if (mapContainer) {
            mapContainer.style.cursor = this.currentMode === 'start' ? 'crosshair' : 
                                        this.currentMode === 'mid' ? 'copy' : 'cell';
        }
    }

    fitMapToMarkers() {
        if (!this.map) return;
        
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
                    
                    if (this.map) {
                        this.map.setView([lat, lng], 15);
                        console.log('✅ Centered map on user location');
                    }
                },
                (error) => {
                    console.log('ℹ️ Geolocation failed, using default Manila location');
                },
                {
                    enableHighAccuracy: false,
                    timeout: 5000,
                    maximumAge: 300000
                }
            );
        }
    }

    setPoint(type, lat, lng, address = null) {
        this.setMarker(type, lat, lng);
        
        if (address) {
            this.setAddressInput(type, address);
        } else {
            this.reverseGeocode(lat, lng, type);
        }
    }

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

    refreshMapSize() {
        if (this.map && !this._destroyed) {
            this.safeInvalidateSize();
        }
    }

    // CRITICAL FIX: Enhanced destroy method
    destroy() {
        console.log('🧹 Destroying RouteMapSelector...');
        this._destroyed = true;
        this.isDragging = false;
        
        if (this.map) {
            try {
                // Remove all event listeners
                this.map.off();
                
                // Remove all markers
                Object.values(this.markers).forEach(marker => {
                    if (marker) {
                        this.map.removeLayer(marker);
                    }
                });
                
                // Destroy map instance
                this.map.remove();
                console.log('✅ Map destroyed successfully');
            } catch (error) {
                console.warn('⚠️ Error during map destruction:', error);
            }
            this.map = null;
        }
        
        this.markers = { start: null, mid: null, end: null };
    }
}

window.RouteMapSelector = RouteMapSelector;
