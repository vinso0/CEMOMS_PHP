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
        
        this.path = null;
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
        
        // Create new marker with numbered circular icon
        const markerConfig = this.getMarkerConfig(type);
        
        this.markers[type] = L.marker([lat, lng], {
            icon: markerConfig.icon,
            title: markerConfig.title
        }).addTo(this.map);
        
        this.markers[type].bindPopup(markerConfig.popupContent).openPopup();
        
        // Store coordinates
        this.setCoordinates(type, lat, lng);
        
        // Update route line
        this.updateRouteLine();
        
        // Update bounds
        this.fitMapToMarkers();
    }

    getMarkerConfig(type) {
        const markerNumber = type === 'start' ? 1 : (type === 'mid' ? 2 : 3);
        const color = type === 'start' ? '#28a745' : (type === 'mid' ? '#007bff' : '#dc3545');
        const pointName = type === 'start' ? 'Start Point' : (type === 'mid' ? 'Mid Point' : 'End Point');
        
        return {
            title: pointName,
            icon: L.divIcon({
                html: `<div style="background-color:${color};width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;font-size:12px;border:2px solid #fff;box-shadow:0 2px 4px rgba(0,0,0,.3);">${markerNumber}</div>`,
                className: 'custom-route-marker',
                iconSize: [24, 24],
                iconAnchor: [12, 12],
                popupAnchor: [0, -12]
            }),
            popupContent: `
                <div style="min-width:150px;">
                    <h6>${pointName}</h6>
                    <p class="mb-0"><small>Click to set location</small></p>
                </div>
            `
        };
    }

    updateRouteLine() {
        if (!this.map || this._destroyed) return;
        
        // Clear existing path
        if (this.path) {
            try {
                this.map.removeLayer(this.path);
            } catch (error) {
                console.warn('Error removing path:', error);
            }
            this.path = null;
        }
        
        // Get coordinates of all placed markers
        const coordinates = [];
        ['start', 'mid', 'end'].forEach(type => {
            if (this.markers[type]) {
                const latlng = this.markers[type].getLatLng();
                coordinates.push([latlng.lat, latlng.lng]);
            }
        });
        
        // Draw route line if we have at least 2 points
        if (coordinates.length >= 2) {
            this.drawRoadRoute(coordinates);
        }
    }

    async drawRoadRoute(coordinates) {
        if (!this.map || this._destroyed) return;
        
        try {
            // Format coordinates for OSRM API
            const waypoints = coordinates.map(coord => `${coord[1]},${coord[0]}`).join(';');
            const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${waypoints}?overview=full&geometries=geojson`;

            const response = await fetch(osrmUrl);
            
            if (!response.ok) {
                throw new Error(`OSRM API error: ${response.status}`);
            }

            const data = await response.json();
            const route = data.routes?.[0];

            if (!route) {
                console.warn('No route found, drawing straight lines');
                this.drawStraightLines(coordinates);
                return;
            }

            // Draw the route
            const routeCoordinates = route.geometry.coordinates.map(coord => [coord[1], coord[0]]);
            
            this.path = L.polyline(routeCoordinates, {
                color: '#2196f3',
                weight: 4,
                opacity: 0.8,
                smoothFactor: 1
            }).addTo(this.map);

            console.log('✅ Road route drawn successfully');

        } catch (error) {
            console.warn('⚠️ OSRM routing failed, using straight lines:', error);
            this.drawStraightLines(coordinates);
        }
    }

    drawStraightLines(coordinates) {
        if (!this.map || this._destroyed) return;

        this.path = L.polyline(coordinates, {
            color: '#ff9800',
            weight: 3,
            opacity: 0.7,
            dashArray: '10,5'
        }).addTo(this.map);
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
        
        // Clear path first
        if (this.path && this.map) {
            try {
                this.map.removeLayer(this.path);
            } catch (error) {
                console.warn('Error removing path:', error);
            }
            this.path = null;
        }
        
        // Clear markers
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
            // Remove path
            if (this.path) {
                try {
                    this.map.removeLayer(this.path);
                } catch (error) {
                    console.warn('Error removing path:', error);
                }
                this.path = null;
            }
            
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
        
        // Clear path
        if (this.path && this.map) {
            try {
                this.map.removeLayer(this.path);
            } catch (e) {
                console.warn('Error removing path:', e);
            }
            this.path = null;
        }
        
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
