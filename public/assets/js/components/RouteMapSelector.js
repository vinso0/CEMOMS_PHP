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
        this._destroyed = false;
        this._isInitializing = false;
        
        this.init();
    }
    
    init() {
        if (this._isInitializing) {
            console.warn('⚠️ RouteMapSelector already initializing');
            return;
        }
        
        this._isInitializing = true;
        
        try {
            this.initMap();
            this.initEventListeners();
            console.log('✅ RouteMapSelector initialized');
        } catch (error) {
            console.error('❌ RouteMapSelector init failed:', error);
            this._destroyed = true;
            throw error;
        } finally {
            this._isInitializing = false;
        }
    }

    initMap() {
        const mapContainer = document.getElementById(this.mapId);
        
        if (!mapContainer) {
            throw new Error(`Map container '${this.mapId}' not found`);
        }
        
        if (typeof L === 'undefined') {
            throw new Error('Leaflet library not loaded');
        }
        
        // CRITICAL FIX: Everything happens inside setTimeout in proper sequence
        setTimeout(() => {
            if (this._destroyed) return;
            
            try {
                // Step 1: Destroy existing map instance first
                if (this.map) {
                    try {
                        this.map.eachLayer(layer => {
                            this.map.removeLayer(layer);
                        });
                        this.map.off();
                        this.map.remove();
                    } catch (error) {
                        console.warn('⚠️ Error destroying old map:', error);
                    }
                    this.map = null;
                }
                
                // Step 2: Clean Leaflet DOM references
                if (mapContainer._leaflet_id && window.L && L.DomUtil) {
                    L.DomUtil.removeClass(mapContainer, 'leaflet-container leaflet-touch leaflet-fade-anim leaflet-grab leaflet-touch-drag leaflet-touch-zoom');
                    mapContainer._leaflet_id = undefined;
                    mapContainer._leaflet = undefined;
                }
                
                // Step 3: Clear HTML
                mapContainer.innerHTML = '';
                
                // Step 4: Create new map
                this.map = L.map(this.mapId, {
                    center: [this.options.defaultLat, this.options.defaultLng],
                    zoom: this.options.defaultZoom,
                    zoomControl: true,
                    attributionControl: true,
                    dragging: true,
                    touchZoom: true,
                    scrollWheelZoom: true,
                    doubleClickZoom: true,
                    boxZoom: true,
                    keyboard: true,
                    tap: true,
                    preferCanvas: false,
                    worldCopyJump: false,
                    fadeAnimation: false,
                    zoomAnimation: false,
                    markerZoomAnimation: false
                });
                
                // Step 5: Add tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                    maxZoom: 19,
                    keepBuffer: 2,
                    updateWhenZooming: false,
                    updateWhenIdle: true
                }).addTo(this.map);
                
                // Step 6: Add click handler
                this.map.on('click', (event) => {
                    if (!this._destroyed) {
                        this.handleMapClick(event);
                    }
                });
                
                // Step 7: Handle map ready
                this.map.whenReady(() => {
                    console.log('✅ Map ready, invalidating size');
                    setTimeout(() => {
                        if (!this._destroyed && this.map) {
                            this.map.invalidateSize(true);
                        }
                    }, 100);
                });
                
                console.log('✅ Map initialized successfully');
                
            } catch (error) {
                console.error('❌ Map init failed:', error);
                this._destroyed = true;
                throw error;
            }
        }, 50);
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
        if (this._destroyed) return;
        
        const lat = event.latlng.lat;
        const lng = event.latlng.lng;
        
        this.setMarker(this.currentMode, lat, lng);
        this.reverseGeocode(lat, lng, this.currentMode);
        this.advanceMode();
    }

    setMarker(type, lat, lng) {
        if (this._destroyed || !this.map) return;
        
        // Remove existing marker
        if (this.markers[type]) {
            try {
                this.map.removeLayer(this.markers[type]);
            } catch (error) {
                console.warn('Error removing marker:', error);
            }
        }
        
        // Create new marker
        const markerConfig = this.getMarkerConfig(type);
        
        this.markers[type] = L.marker([lat, lng], {
            icon: markerConfig.icon,
            title: markerConfig.title
        }).addTo(this.map);
        
        this.markers[type].bindPopup(markerConfig.title).openPopup();
        
        // Store coordinates
        this.setCoordinates(type, lat, lng);
        
        // Update bounds
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
        
        if (!input) return;
        
        this.setAddressInput(pointType, '🔍 Looking up address...');
        input.classList.add('loading');
        
        const timeoutPromise = new Promise((_, reject) => {
            setTimeout(() => reject(new Error('Timeout')), timeout);
        });
        
        const fetchPromise = fetch(`/api/geocode_proxy?lat=${lat}&lng=${lng}`)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                return response.json();
            });
        
        Promise.race([fetchPromise, timeoutPromise])
            .then(data => {
                if (this._destroyed) return;
                
                if (data && data.display_name) {
                    const parts = data.display_name.split(',').map(s => s.trim());
                    const friendlyAddress = parts.slice(0, 3).join(', ');
                    
                    this.setAddressInput(pointType, friendlyAddress);
                    input.classList.remove('loading');
                    input.classList.add('success');
                } else {
                    throw new Error('No address found');
                }
            })
            .catch(error => {
                if (this._destroyed) return;
                
                const coordStr = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                const areaHint = this.getAreaHint(lat, lng);
                
                this.setAddressInput(pointType, areaHint ? `${coordStr} (${areaHint})` : coordStr);
                input.classList.remove('loading');
                input.classList.add('fallback');
            });
    }

    getAreaHint(lat, lng) {
        if (lat >= 14.4 && lat <= 14.8 && lng >= 120.9 && lng <= 121.1) {
            if (lat >= 14.55 && lat <= 14.65) return "Manila City";
            else if (lat >= 14.5 && lat < 14.55) return "Makati/Pasay";
            else if (lat > 14.65) return "Quezon City";
            else return "Metro Manila";
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
        if (!this.map || this._destroyed) return;
        
        try {
            const mapContainer = this.map.getContainer();
            if (mapContainer) {
                mapContainer.style.cursor = this.currentMode === 'start' ? 'crosshair' : 
                                            this.currentMode === 'mid' ? 'copy' : 'cell';
            }
        } catch (error) {
            console.warn('Could not update cursor:', error);
        }
    }

    fitMapToMarkers() {
        if (!this.map || this._destroyed) return;
        
        const validMarkers = Object.values(this.markers).filter(m => m);
        
        if (validMarkers.length > 0) {
            try {
                const group = new L.featureGroup(validMarkers);
                this.map.fitBounds(group.getBounds().pad(0.1));
            } catch (error) {
                console.warn('Error fitting bounds:', error);
            }
        }
    }

    clearAllMarkers() {
        if (this._destroyed) return;
        
        Object.keys(this.markers).forEach(type => {
            if (this.markers[type] && this.map) {
                try {
                    this.map.removeLayer(this.markers[type]);
                } catch (error) {
                    console.warn('Error removing marker:', error);
                }
                this.markers[type] = null;
            }
            
            this.setAddressInput(type, '');
            this.setCoordinates(type, '', '');
        });
        
        this.currentMode = 'start';
        const startRadio = document.getElementById('startMode');
        if (startRadio) startRadio.checked = true;
        
        this.updateModeIndicator();
    }

    getCurrentLocation() {
        if (navigator.geolocation && !this._destroyed) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    if (this._destroyed || !this.map) return;
                    
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    this.map.setView([lat, lng], 15);
                },
                () => {},
                {
                    enableHighAccuracy: false,
                    timeout: 5000,
                    maximumAge: 300000
                }
            );
        }
    }

    setPoint(type, lat, lng, address = null) {
        if (this._destroyed) return;
        
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
        if (!this._destroyed && this.map) {
            setTimeout(() => {
                if (!this._destroyed && this.map) {
                    try {
                        this.map.invalidateSize(true);
                    } catch (error) {
                        console.warn('Error invalidating size:', error);
                    }
                }
            }, 100);
        }
    }

    destroyMapInstance() {
        if (!this.map) return;
        
        try {
            // Remove all layers
            this.map.eachLayer(layer => {
                try {
                    this.map.removeLayer(layer);
                } catch (error) {
                    console.warn('Error removing layer:', error);
                }
            });
            
            // Remove event listeners
            this.map.off();
            
            // Remove map instance
            this.map.remove();
            console.log('🗑️ Map instance removed');
        } catch (error) {
            console.warn('⚠️ Error destroying map:', error);
        }
        
        this.map = null;
    }

    destroy() {
        if (this._destroyed) return;
        
        console.log('🧹 Destroying RouteMapSelector');
        this._destroyed = true;
        
        // Clear markers
        Object.values(this.markers).forEach(marker => {
            if (marker && this.map) {
                try {
                    this.map.removeLayer(marker);
                } catch (e) {
                    console.warn('Error removing marker:', e);
                }
            }
        });
        this.markers = { start: null, mid: null, end: null };
        
        // Destroy map
        this.destroyMapInstance();
    }
}

window.RouteMapSelector = RouteMapSelector;
