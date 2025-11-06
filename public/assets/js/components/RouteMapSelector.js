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
        this.isInitialized = false;
        
        this.init();
    }
    
    init() {
        try {
            // Wait for DOM to be ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.initMap());
            } else {
                this.initMap();
            }
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
            console.warn(`Map container '${this.mapId}' not found, will retry when modal opens`);
            return;
        }
        
        // CRITICAL FIX: Ensure container is visible and has dimensions
        if (mapContainer.offsetWidth === 0 || mapContainer.offsetHeight === 0) {
            console.warn('Map container has no dimensions, deferring initialization');
            return;
        }
        
        // CRITICAL: Clean up any existing Leaflet instances
        this.cleanupExistingMap(mapContainer);
        
        if (typeof L === 'undefined') {
            throw new Error('Leaflet library not loaded');
        }
        
        try {
            // Initialize fresh map instance with error handling
            this.map = L.map(this.mapId, {
                center: [this.options.defaultLat, this.options.defaultLng],
                zoom: this.options.defaultZoom,
                zoomControl: true,
                attributionControl: true,
                // CRITICAL: Ensure dragging is enabled
                dragging: true,
                touchZoom: true,
                doubleClickZoom: true,
                scrollWheelZoom: true,
                boxZoom: true,
                keyboard: true
            });
            
            // Add tile layer with error handling
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19,
                errorTileUrl: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='
            }).addTo(this.map);
            
            // Add map click handler
            this.map.on('click', (event) => {
                this.handleMapClick(event);
            });
            
            // CRITICAL: Proper size invalidation
            this.invalidateMapSize();
            
            this.isInitialized = true;
            console.log('✅ Map initialized successfully with dragging enabled');
            
        } catch (error) {
            console.error('❌ Map initialization failed:', error);
            this.isInitialized = false;
            throw error;
        }
    }

    // CRITICAL: New method to clean up existing maps
    cleanupExistingMap(container) {
        // Remove existing Leaflet instance
        if (container._leaflet_id) {
            try {
                // Get the existing map instance
                const existingMap = container._leaflet;
                if (existingMap && existingMap.remove) {
                    existingMap.remove();
                }
            } catch (e) {
                console.warn('Error cleaning up existing map:', e);
            }
            
            // Clear Leaflet properties
            container._leaflet_id = null;
            container._leaflet = null;
        }
        
        // Clear any existing map instance
        if (this.map) {
            try {
                this.map.remove();
            } catch (e) {
                console.warn('Error removing existing map instance:', e);
            }
            this.map = null;
        }
        
        // Clear container completely
        container.innerHTML = '';
    }

    // Method for proper size invalidation
    invalidateMapSize() {
        if (!this.map) return;
        
        // Multiple attempts to ensure proper sizing
        setTimeout(() => {
            if (this.map) {
                this.map.invalidateSize(true);
            }
        }, 50);
        
        setTimeout(() => {
            if (this.map) {
                this.map.invalidateSize(true);
            }
        }, 200);
        
        setTimeout(() => {
            if (this.map) {
                this.map.invalidateSize(true);
            }
        }, 500);
    }

    // modal-specific initialization
    initializeForModal() {
        const mapContainer = document.getElementById(this.mapId);
        
        if (!mapContainer) {
            console.error(`Map container '${this.mapId}' not found`);
            return false;
        }
        
        // Wait for container to have proper dimensions
        let attempts = 0;
        const maxAttempts = 10;
        
        const checkAndInit = () => {
            attempts++;
            
            if (mapContainer.offsetWidth > 0 && mapContainer.offsetHeight > 0) {
                if (!this.isInitialized || !this.map) {
                    this.initMap();
                } else {
                    this.invalidateMapSize();
                }
                return true;
            } else if (attempts < maxAttempts) {
                setTimeout(checkAndInit, 100);
                return false;
            } else {
                console.error('Failed to initialize map: container has no dimensions after multiple attempts');
                return false;
            }
        };
        
        return checkAndInit();
    }

    initEventListeners() {
        // Point mode selector - handle both naming conventions
        const selectors = ['input[name="pointMode"]', 'input[name="editPointMode"]'];
        
        selectors.forEach(selector => {
            document.querySelectorAll(selector).forEach(radio => {
                radio.addEventListener('change', (e) => {
                    this.currentMode = e.target.value;
                    this.updateModeIndicator();
                });
            });
        });
        
        // Clear route button - handle both add and edit modals
        const clearBtns = ['clearRouteBtn', 'clearEditRouteBtn'];
        clearBtns.forEach(btnId => {
            const clearBtn = document.getElementById(btnId);
            if (clearBtn) {
                clearBtn.addEventListener('click', () => {
                    this.clearAllMarkers();
                });
            }
        });
    }

    handleMapClick(event) {
        if (!this.map || !this.isInitialized) {
            console.warn('Map not properly initialized for click handling');
            return;
        }
        
        const lat = event.latlng.lat;
        const lng = event.latlng.lng;
        
        this.setMarker(this.currentMode, lat, lng);
        this.reverseGeocode(lat, lng, this.currentMode);
        
        // Auto-advance to next mode
        this.advanceMode();
    }

    setMarker(type, lat, lng) {
        if (!this.map || !this.isInitialized) {
            console.warn('Map not initialized, cannot set marker');
            return;
        }
        
        // Remove existing marker
        if (this.markers[type]) {
            this.map.removeLayer(this.markers[type]);
        }
        
        // Create new marker
        const markerConfig = this.getMarkerConfig(type);
        
        this.markers[type] = L.marker([lat, lng], {
            icon: markerConfig.icon,
            title: markerConfig.title,
            draggable: false // Prevent marker dragging to avoid conflicts
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
        // Handle both add and edit modal input naming
        const inputMappings = {
            start: ['startPoint', 'editStartPoint'],
            mid: ['midPoint', 'editMidPoint'],
            end: ['endPoint', 'editEndPoint']
        };
        
        const possibleIds = inputMappings[pointType] || [];
        let input = null;
        
        // Find the correct input element
        for (const id of possibleIds) {
            const element = document.getElementById(id);
            if (element) {
                input = element;
                break;
            }
        }
        
        if (!input) {
            console.error(`Input element not found for point type: ${pointType}`);
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

    reverseGeocodeQuietRetry(lat, lng, pointType) {
        fetch(`/api/geocode_proxy?lat=${lat}&lng=${lng}`)
            .then(r => r.json())
            .then(data => {
                // Handle both add and edit modal inputs
                const inputMappings = {
                    start: ['startPoint', 'editStartPoint'],
                    mid: ['midPoint', 'editMidPoint'],
                    end: ['endPoint', 'editEndPoint']
                };
                
                const possibleIds = inputMappings[pointType] || [];
                let input = null;
                
                for (const id of possibleIds) {
                    const element = document.getElementById(id);
                    if (element) {
                        input = element;
                        break;
                    }
                }
                
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
        // Handle both add and edit modal inputs
        const inputMappings = {
            start: ['startPoint', 'editStartPoint'],
            mid: ['midPoint', 'editMidPoint'], 
            end: ['endPoint', 'editEndPoint']
        };
        
        const possibleIds = inputMappings[type] || [];
        let input = null;
        
        for (const id of possibleIds) {
            const element = document.getElementById(id);
            if (element) {
                input = element;
                break;
            }
        }
        
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
        // Handle both add and edit modal coordinate inputs
        const latMappings = {
            start: ['startLat', 'editStartLat'],
            mid: ['midLat', 'editMidLat'],
            end: ['endLat', 'editEndLat']
        };
        
        const lngMappings = {
            start: ['startLon', 'editStartLon'],
            mid: ['midLon', 'editMidLon'],
            end: ['endLon', 'editEndLon']
        };
        
        // Set latitude
        const latIds = latMappings[type] || [];
        latIds.forEach(id => {
            const input = document.getElementById(id);
            if (input) input.value = lat;
        });
        
        // Set longitude  
        const lngIds = lngMappings[type] || [];
        lngIds.forEach(id => {
            const input = document.getElementById(id);
            if (input) input.value = lng;
        });
    }

    advanceMode() {
        const modes = ['start', 'mid', 'end'];
        const currentIndex = modes.indexOf(this.currentMode);
        
        if (currentIndex < modes.length - 1) {
            const nextMode = modes[currentIndex + 1];
            
            // Handle both add and edit modals
            const nextRadioIds = [`${nextMode}Mode`, `edit${nextMode.charAt(0).toUpperCase() + nextMode.slice(1)}Mode`];
            
            for (const radioId of nextRadioIds) {
                const nextRadio = document.getElementById(radioId);
                if (nextRadio) {
                    nextRadio.checked = true;
                    this.currentMode = nextMode;
                    this.updateModeIndicator();
                    break;
                }
            }
        }
    }

    updateModeIndicator() {
        if (!this.map || !this.isInitialized) return;
        
        // Update map cursor style
        const mapContainer = this.map.getContainer();
        mapContainer.style.cursor = this.currentMode === 'start' ? 'crosshair' : 
                                    this.currentMode === 'mid' ? 'copy' : 'cell';
    }

    fitMapToMarkers() {
        if (!this.map || !this.isInitialized) return;
        
        const markerPositions = [];
        
        Object.values(this.markers).forEach(marker => {
            if (marker) {
                markerPositions.push(marker.getLatLng());
            }
        });
        
        if (markerPositions.length > 0) {
            try {
                const group = new L.featureGroup(Object.values(this.markers).filter(m => m));
                this.map.fitBounds(group.getBounds().pad(0.1));
            } catch (error) {
                console.warn('Error fitting map to markers:', error);
            }
        }
    }

    clearAllMarkers() {
        Object.keys(this.markers).forEach(type => {
            if (this.markers[type] && this.map) {
                this.map.removeLayer(this.markers[type]);
                this.markers[type] = null;
            }
            
            // Clear form inputs for both add and edit modals
            this.setAddressInput(type, '');
            this.setCoordinates(type, '', '');
        });
        
        // Reset to start mode
        this.currentMode = 'start';
        
        // Handle both add and edit modals
        const startRadioIds = ['startMode', 'editStartMode'];
        startRadioIds.forEach(radioId => {
            const startRadio = document.getElementById(radioId);
            if (startRadio) {
                startRadio.checked = true;
            }
        });
        
        this.updateModeIndicator();
    }

    getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    if (this.map && this.isInitialized) {
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
        } else {
            console.log('ℹ️ Geolocation not supported, using default location');
        }
    }

    // Public method to set a specific point programmatically
    setPoint(type, lat, lng, address = null) {
        if (!this.isInitialized) {
            console.warn('Map not initialized, cannot set point');
            return;
        }
        
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

    // Refresh method
    refreshMapSize() {
        if (this.map && this.isInitialized) {
            this.invalidateMapSize();
        } else if (!this.isInitialized) {
            // Try to initialize if not already done
            this.initializeForModal();
        }
    }

    // Destroy method
    destroy() {
        if (this.map) {
            try {
                this.map.remove();
            } catch (error) {
                console.warn('Error destroying map:', error);
            }
            this.map = null;
        }
        this.markers = { start: null, mid: null, end: null };
        this.isInitialized = false;
    }
}

window.RouteMapSelector = RouteMapSelector;
