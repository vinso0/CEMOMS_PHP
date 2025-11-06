class RouteDetailsView {
  constructor(modalId = 'routeDetailsModal', mapId = 'route-map') {
    this.modal = document.getElementById(modalId);
    this.mapId = mapId;
    this.map = null;
    this.markers = [];
    this.path = null;
    this.isInitializing = false;
    this.wire();
  }

  wire() {
    if (!this.modal) return;

    // Remove any existing event listeners to prevent duplicates
    this.modal.removeEventListener('shown.bs.modal', this.handleModalShown);
    this.modal.removeEventListener('hidden.bs.modal', this.handleModalHidden);

    // Bind event handlers
    this.handleModalShown = () => {
      console.log('🔍 Route details modal shown');
      setTimeout(() => this.initMap(), 200);
    };

    this.handleModalHidden = () => {
      console.log('🔍 Route details modal hidden');
      this.cleanup();
    };

    this.modal.addEventListener('shown.bs.modal', this.handleModalShown);
    this.modal.addEventListener('hidden.bs.modal', this.handleModalHidden);

    // Event delegation for route details buttons
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('.route-details-btn');
      if (btn) {
        console.log('🔍 Route details button clicked');
        try {
          const truckData = JSON.parse(btn.dataset.truckData);
          console.log('🔍 Truck data parsed:', truckData);
          // Set data immediately when button is clicked
          this.setTruckData(truckData);
        } catch (error) {
          console.error('❌ Failed to parse truck data:', error);
        }
      }
    });
  }

  setTruckData(truckData) {
    this.currentTruckData = truckData;
    this.populateModalData(truckData);
  }

  populateModalData(truckData) {
    console.log('🔍 Populating modal with:', truckData);
    
    // Helper function to set text content
    const setText = (id, value) => {
      const el = document.getElementById(id);
      if (el) el.textContent = value || '-';
    };

    // Populate truck information
    setText('details-plate-number', truckData.plate_number);
    setText('details-body-number', truckData.body_number);
    setText('details-route-name', truckData.route_name);
    setText('details-foreman', truckData.foreman_name);

    // Format schedule type
    const scheduleType = truckData.schedule ? 
      truckData.schedule.charAt(0).toUpperCase() + truckData.schedule.slice(1) : '-';
    setText('details-schedule-type', scheduleType);

    // Format operation time
    let operationTime = truckData.operation_time || '-';
    if (operationTime && operationTime !== '-') {
      try {
        const date = new Date(`2000-01-01 ${operationTime}`);
        operationTime = date.toLocaleTimeString('en-US', { 
          hour: 'numeric', 
          minute: '2-digit', 
          hour12: true 
        });
      } catch (error) {
        console.warn('Failed to format operation time:', error);
      }
    }
    setText('details-operation-time', operationTime);

    // Handle Weekly days
    const weeklyContainer = document.getElementById('Weekly-days-container');
    const weeklyDaysEl = document.getElementById('details-Weekly-days');
    
    if (truckData.schedule === 'Weekly' && truckData.weekly_days?.length) {
      if (weeklyContainer) weeklyContainer.style.display = 'block';
      if (weeklyDaysEl) {
        weeklyDaysEl.innerHTML = truckData.weekly_days
          .map(day => `<span class="badge bg-primary me-1 mb-1">${day.substring(0, 3)}</span>`)
          .join('');
      }
    } else {
      if (weeklyContainer) weeklyContainer.style.display = 'none';
    }
  }

  initMap() {
    if (this.isInitializing) {
      console.log('⚠️ Map initialization already in progress');
      return;
    }

    this.isInitializing = true;
    console.log('🗺️ Initializing route details map');

    const container = document.getElementById(this.mapId);
    if (!container) {
      console.error('❌ Map container not found:', this.mapId);
      this.isInitializing = false;
      return;
    }

    // SAFER cleanup of existing map
    this.destroyMap();

    // SAFER Leaflet internal references cleanup
    try {
      if (container._leaflet_id && L.Util) {
        // Use Leaflet's utility to properly clean up
        L.DomUtil.removeClass(container, 'leaflet-container leaflet-touch leaflet-fade-anim leaflet-grab leaflet-touch-drag leaflet-touch-zoom');
        container._leaflet_id = undefined;
        container._leaflet = undefined;
      }
    } catch (error) {
      console.warn('Could not clean Leaflet references:', error);
    }

    // Clear container HTML
    container.innerHTML = '';
    
    // Add a small delay to ensure cleanup is complete
    setTimeout(() => {
      try {
        // Create new map instance
        this.map = L.map(this.mapId, {
          center: [14.6091, 121.0223],
          zoom: 12,
          zoomControl: true,
          attributionControl: true,
          preferCanvas: false,
          worldCopyJump: false,
          maxBoundsViscosity: 1.0,
          fadeAnimation: false,
          zoomAnimation: false,
          markerZoomAnimation: false
        });

        // Add tile layer
        const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
          maxZoom: 19,
          keepBuffer: 2,
          updateWhenZooming: false,
          updateWhenIdle: true,
          crossOrigin: true,
          errorTileUrl: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='
        }).addTo(this.map);

        // Reset tracking arrays
        this.markers = [];
        this.path = null;

        // Handle map ready event
        this.map.whenReady(() => {
          console.log('✅ Map ready, invalidating size');
          setTimeout(() => {
            if (this.map) {
              this.map.invalidateSize(true);
              tileLayer.redraw();
            }
          }, 100);

          // Load route data if available
          if (this.currentTruckData?.route_id) {
            this.loadRoutePoints(this.currentTruckData.route_id);
          } else {
            this.showNoRouteMessage();
          }
        });

        console.log('✅ Route details map initialized successfully');

      } catch (error) {
        console.error('❌ Failed to initialize route details map:', error);
      } finally {
        this.isInitializing = false;
      }
    }, 50); // Small delay to ensure DOM is ready
  }


  destroyMap() {
    if (this.map) {
      try {
        // Remove all layers
        this.clearMapLayers();
        
        // Remove map instance
        this.map.remove();
        console.log('🗑️ Map instance removed');
      } catch (error) {
        console.warn('⚠️ Error removing map:', error);
      }
      this.map = null;
    }
  }

  clearMapLayers() {
    // Clear markers
    this.markers.forEach(marker => {
      try {
        if (this.map && this.map.hasLayer(marker)) {
          this.map.removeLayer(marker);
        }
      } catch (error) {
        console.warn('Error removing marker:', error);
      }
    });
    this.markers = [];

    // Clear path
    if (this.path) {
      try {
        if (this.map && this.map.hasLayer(this.path)) {
          this.map.removeLayer(this.path);
        }
      } catch (error) {
        console.warn('Error removing path:', error);
      }
      this.path = null;
    }
  }

  showNoRouteMessage() {
    const list = document.getElementById('route-points-list');
    if (list) {
      list.innerHTML = `
        <div class="empty-points text-center p-4">
          <i class="fas fa-exclamation-circle text-warning mb-2" style="font-size: 2rem;"></i>
          <h6>No Route Assigned</h6>
          <p class="text-muted mb-0">This truck has no route assigned yet.</p>
        </div>`;
    }
  }

  async loadRoutePoints(routeId) {
    const list = document.getElementById('route-points-list');
    
    // Show loading state
    if (list) {
      list.innerHTML = `
        <div class="loading-points text-center p-4">
          <i class="fas fa-spinner fa-spin text-muted mb-2"></i>
          <p class="text-muted mb-0">Loading route points...</p>
        </div>`;
    }

    try {
      const response = await fetch(`/admin/operations/garbage_collection/get_route_points?route_id=${routeId}`);
      
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      }

      const data = await response.json();
      
      if (!data.route_points || data.route_points.length === 0) {
        this.showEmptyRoutePoints();
        return;
      }

      this.renderPointsList(data.route_points);
      this.drawOnMap(data.route_points);

    } catch (error) {
      console.error('❌ Failed to load route points:', error);
      this.showRoutePointsError(error.message);
    }
  }

  showEmptyRoutePoints() {
    const list = document.getElementById('route-points-list');
    if (list) {
      list.innerHTML = `
        <div class="empty-points text-center p-4">
          <i class="fas fa-map-pin text-muted mb-2" style="font-size: 2rem;"></i>
          <h6>No Route Points</h6>
          <p class="text-muted mb-0">This route has no points defined.</p>
        </div>`;
    }
  }

  showRoutePointsError(message) {
    const list = document.getElementById('route-points-list');
    if (list) {
      list.innerHTML = `
        <div class="empty-points text-center p-4">
          <i class="fas fa-exclamation-triangle text-danger mb-2" style="font-size: 2rem;"></i>
          <h6>Error Loading Route Points</h6>
          <p class="text-muted mb-0">${message}</p>
        </div>`;
    }
  }

  renderPointsList(points) {
    const list = document.getElementById('route-points-list');
    if (!list) return;

    const pointsHtml = points.map((point, index) => {
      const className = (point.name || '').toLowerCase().includes('start') ? 'start' :
                       (point.name || '').toLowerCase().includes('end') ? 'end' : 'mid';
      
      return `
        <div class="point-item" data-point-index="${index}" onclick="window.__highlightRoutePoint?.(${index})">
          <div class="point-number ${className}">${index + 1}</div>
          <div class="point-info">
            <div class="point-name">${point.name || 'Unnamed Point'}</div>
            <div class="point-address">${point.address || 'No address'}</div>
          </div>
        </div>`;
    }).join('');

    list.innerHTML = pointsHtml;
    
    // Set global highlight function
    window.__highlightRoutePoint = (index) => this.highlight(index);
  }

  drawOnMap(points) {
    if (!this.map || !points.length) return;

    console.log('🎨 Drawing route points on map:', points.length);

    // Clear existing layers
    this.clearMapLayers();

    const coordinates = [];
    const bounds = L.latLngBounds();

    points.forEach((point, index) => {
      const latLng = [parseFloat(point.lat), parseFloat(point.lng)];
      coordinates.push(latLng);
      bounds.extend(latLng);

      // Determine point type and color
      const pointType = index === 0 ? 'start' : 
                       (index === points.length - 1 ? 'end' : 'collection');
      const color = pointType === 'start' ? '#28a745' : 
                   (pointType === 'end' ? '#dc3545' : '#007bff');

      // Create custom marker
      const markerIcon = L.divIcon({
        html: `<div style="background-color:${color};width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;font-size:12px;border:2px solid #fff;box-shadow:0 2px 4px rgba(0,0,0,.3);">${index + 1}</div>`,
        className: 'custom-route-marker',
        iconSize: [24, 24],
        iconAnchor: [12, 12]
      });

      // Create and add marker
      const marker = L.marker(latLng, { icon: markerIcon })
        .addTo(this.map)
        .bindPopup(`
          <div style="min-width:150px;">
            <h6>${point.name || 'Point ' + (index + 1)}</h6>
            <p class="mb-0"><small>${point.address || 'No address available'}</small></p>
          </div>
        `);

      this.markers.push(marker);
    });

    // Draw route if we have multiple points
    if (coordinates.length > 1) {
      this.drawRoadRoute(coordinates);
    }

    // Fit map to show all points
    if (bounds.isValid()) {
      this.map.fitBounds(bounds, { padding: [20, 20] });
    }
  }

  async drawRoadRoute(coordinates) {
    const container = document.getElementById(this.mapId);
    
    // Show loading indicator
    const loadingIndicator = document.createElement('div');
    loadingIndicator.id = 'route-loading-indicator';
    loadingIndicator.innerHTML = `
      <div style="position:absolute;top:10px;right:10px;background:rgba(255,255,255,.9);padding:8px 12px;border-radius:4px;font-size:12px;z-index:1000;box-shadow:0 2px 4px rgba(0,0,0,.2);">
        <i class="fas fa-spinner fa-spin me-1"></i>Calculating route...
      </div>`;
    container.appendChild(loadingIndicator);

    try {
      // Format coordinates for OSRM API
      const waypoints = coordinates.map(coord => `${coord[1]},${coord[0]}`).join(';');
      const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${waypoints}?overview=full&geometries=geojson`;

      const response = await fetch(osrmUrl);
      
      // Remove loading indicator
      loadingIndicator.remove();

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

      // Add route information popup
      const distance = (route.distance / 1000).toFixed(1);
      const duration = Math.round(route.duration / 60);
      
      this.path.bindPopup(`
        <div style="text-align:center;">
          <h6>📍 Route Information</h6>
          <p style="margin:5px 0;"><strong>Distance:</strong> ${distance} km</p>
          <p style="margin:5px 0;"><strong>Duration:</strong> ${duration} min</p>
          <small style="color:#666;">Following actual roads</small>
        </div>
      `);

      console.log('✅ Road route drawn successfully');

    } catch (error) {
      console.warn('⚠️ OSRM routing failed, using straight lines:', error);
      loadingIndicator.remove();
      this.drawStraightLines(coordinates);
    }
  }

  drawStraightLines(coordinates) {
    if (!this.map) return;

    this.path = L.polyline(coordinates, {
      color: '#ff9800',
      weight: 3,
      opacity: 0.7,
      dashArray: '10,5'
    }).addTo(this.map);

    this.path.bindPopup(`
      <div style="text-align:center;">
        <h6>📍 Direct Route</h6>
        <p style="margin:5px 0;color:#ff9800;">
          <strong>⚠️ Straight line connection</strong>
        </p>
        <small style="color:#666;">Road routing unavailable</small>
      </div>
    `);
  }

  highlight(index) {
    // Remove active class from all items
    document.querySelectorAll('.point-item').forEach(item => {
      item.classList.remove('active');
    });

    // Add active class to selected item
    const selectedItem = document.querySelector(`.point-item[data-point-index="${index}"]`);
    if (selectedItem) {
      selectedItem.classList.add('active');
    }

    // Highlight marker on map
    const marker = this.markers[index];
    if (marker && this.map) {
      marker.openPopup();
      this.map.panTo(marker.getLatLng(), { animate: true });
      
      // Zoom in after panning
      setTimeout(() => {
        if (this.map) {
          const currentZoom = this.map.getZoom();
          this.map.setZoom(Math.max(currentZoom, 15), { animate: true });
        }
      }, 500);
    }
  }

  cleanup() {
    console.log('🧹 Cleaning up route details view');

    // Blur focused elements
    const focusedElement = document.activeElement;
    if (focusedElement && focusedElement.closest('#routeDetailsModal')) {
      focusedElement.blur();
    }

    // Clear route points list active states
    document.querySelectorAll('.point-item').forEach(item => {
      item.classList.remove('active');
    });

    // Destroy map
    this.destroyMap();

    // Clear truck data
    this.currentTruckData = null;

    // Remove global highlight function
    if (window.__highlightRoutePoint) {
      delete window.__highlightRoutePoint;
    }
  }

  // Public method to refresh map size
  refreshMap() {
    if (this.map) {
      setTimeout(() => {
        this.map.invalidateSize(true);
      }, 100);
    }
  }
}

// Export to global scope
window.RouteDetailsView = RouteDetailsView;
