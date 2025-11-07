class EditSweeperView {
  constructor(modalId = 'editSweeperModal', mapId = 'editSweeperRouteMap') {
    this.modal = document.getElementById(modalId);
    this.mapId = mapId;
    this.selector = null;
    this.isDragging = false;
    this.isHiding = false;
    this.wire();
  }

  wire() {
    if (!this.modal) return;

    // CRITICAL FIX: Prevent modal close during drag + blur focused elements
    this.modal.addEventListener('hide.bs.modal', (e) => {
      if (this.isHiding) {
        e.preventDefault();
        return;
      }

      // CRITICAL FIX: Blur focused elements
      const focusedElement = document.activeElement;
      if (focusedElement && this.modal.contains(focusedElement)) {
        focusedElement.blur();
      }

      if (this.isDragging || this.selector?.isDragging) {
        console.log('🛑 Edit: Preventing modal hide during drag');
        e.preventDefault();

        if (this.selector?.map) {
          this.selector.map.stop();
          if (this.selector.map.dragging) {
            this.selector.map.dragging.disable();
            setTimeout(() => this.selector?.map?.dragging?.enable(), 100);
          }
        }

        this.isDragging = false;
        if (this.selector) this.selector.isDragging = false;

        setTimeout(() => {
          const modalInstance = bootstrap.Modal.getInstance(this.modal);
          if (modalInstance) modalInstance.hide();
        }, 100);
        return;
      }

      this.isHiding = true;
      this.preCleanup();
    });

    this.modal.addEventListener('shown.bs.modal', () => {
      this.isHiding = false;
      this.initMap();
    });

    this.modal.addEventListener('hidden.bs.modal', () => {
      this.isHiding = false;
      this.reset();
    });

    // Edit button delegation
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('.edit-sweeper-btn');
      if (!btn) return;
      const payload = {
        sweeper_id: btn.dataset.sweeperId || '',
        equipment_number: btn.dataset.equipmentNumber || '',
        route_name: btn.dataset.routeName || '',
        route_id: btn.dataset.routeId || '',
        foreman_id: btn.dataset.foremanId || '',
        foreman_name: btn.dataset.foremanName || '',
        schedule: btn.dataset.schedule || '',
        schedule_id: btn.dataset.scheduleId || '',
        operation_time: btn.dataset.operationTime || '',
        weekly_days: JSON.parse(btn.dataset.weeklyDays || '[]')
      };
      setTimeout(() => this.populate(payload), 200);
    });

    window.populateEditModal = (d) => this.populate(d);
    window.clearEditRoute = () => this.clearRoute();
  }

  preCleanup() {
    console.log('🧹 Edit: Pre-cleanup starting...');

    if (this.selector) {
      try {
        this.selector.destroy();
        console.log('✅ Edit: Map destroyed');
      } catch (error) {
        console.warn('⚠️ Edit: Cleanup error:', error);
      }
      this.selector = null;
    }

    this.isDragging = false;
  }

  initMap() {
    console.log('🗺️ Edit: Initializing map...');

    this.preCleanup();

    const container = document.getElementById(this.mapId);
    if (!container) {
      console.error(`Map container ${this.mapId} not found`);
      return;
    }

    container.innerHTML = '';
    if (container._leaflet_id) {
      delete container._leaflet_id;
      container._leaflet = false;
    }

    if (container.offsetWidth === 0 || container.offsetHeight === 0) {
      console.log('Waiting for container dimensions...');
      setTimeout(() => this.initMap(), 100);
      return;
    }

    if (!window.RouteMapSelector) {
      console.error('RouteMapSelector class not available');
      return;
    }

    try {
      this.selector = new window.RouteMapSelector(this.mapId, {
        defaultLat: 14.5995,
        defaultLng: 120.9842,
        defaultZoom: 13
      });

      if (this.selector.map) {
        this.selector.map.on('dragstart', () => {
          this.isDragging = true;
          this.selector.isDragging = true;
        });

        this.selector.map.on('dragend', () => {
          setTimeout(() => {
            this.isDragging = false;
            this.selector.isDragging = false;
          }, 50);
        });
      }

      setTimeout(() => {
        if (this.selector && !this.selector._destroyed) {
          this.selector.refreshMapSize();
        }
      }, 300);

      container.classList.add('map-loaded');
      console.log('✅ Edit: Map initialized');
    } catch (error) {
      console.error('❌ Edit: Map init failed:', error);
    }
  }

  reset() {
    console.log('📋 Edit: Resetting...');

    const form = document.getElementById('editSweeperForm');
    if (form) {
      form.reset();
      form.classList.remove('was-validated');
    }

    this.preCleanup();

    const container = document.getElementById(this.mapId);
    if (container) {
      container.classList.remove('map-loaded');
    }
  }

  // Keep all existing methods (populate, loadRoutePoints, etc.) unchanged
  populate(data) {
    if (!data?.sweeper_id) {
      console.warn('Missing sweeper_id in EditSweeperView.populate:', data);
      return;
    }

    const setVal = (id, v) => { const el = document.getElementById(id); if (el) el.value = v || ''; };
    setVal('editSweeperId', data.sweeper_id);
    setVal('editScheduleId', data.schedule_id);
    setVal('editRouteId', data.route_id);
    setVal('editEquipmentNumber', data.equipment_number);
    setVal('editRouteName', data.route_name);

    const foreman = document.getElementById('editAssignedForeman');
    if (foreman) foreman.value = data.foreman_id || '';

    const sched = document.getElementById('editScheduleType');
    if (sched) {
      sched.value = data.schedule || '';
    }

    setVal('editOperationTime', data.operation_time);


    setTimeout(() => {
      if (data.route_id) this.loadRoutePoints(data.route_id);
      else this.clearRouteInputs();
    }, 500);
  }

  async loadRoutePoints(routeId) {
    try {
      const res = await fetch(`/admin/operations/street_sweeping/get_route_points?route_id=${routeId}`);
      if (!res.ok) throw new Error(`${res.status} ${res.statusText}`);
      const data = await res.json();
      if (!data.route_points?.length) {
        this.clearRouteInputs();
        return;
      }
      this.populateRoutePoints(data.route_points);
    } catch (err) {
      console.error('Edit loadRoutePoints error:', err);
      this.clearRouteInputs();
    }
  }

  populateRoutePoints(points) {
    if (!this.selector) return;

    this.selector.clearAllMarkers();
    this.clearRouteInputs();

    points.forEach(p => {
      if (!p.lat || !p.lng) return;

      let type = 'mid';
      const n = (p.name || '').toLowerCase();
      if (n.includes('start')) type = 'start';
      else if (n.includes('end')) type = 'end';

      this.selector.setPoint(type, p.lat, p.lng, p.address);

      const id = type.charAt(0).toUpperCase() + type.slice(1);
      const set = (suffix, v) => {
        const el = document.getElementById(`edit${id}${suffix}`);
        if (el) el.value = v || '';
      };

      set('Point', p.address || '');
      set('Lat', p.lat);
      set('Lon', p.lng);
    });
  }

  clearRouteInputs() {
    ['Start','Mid','End'].forEach(k => {
      ['Point','Lat','Lon'].forEach(s => {
        const el = document.getElementById(`edit${k}${s}`);
        if (el) el.value = '';
      });
    });
  }


  clearRoute() {
    if (this.selector) {
      this.selector.clearAllMarkers();
    }
    this.clearRouteInputs();
  }

  cleanup() {
  if (this.map) {
    this.map.remove();
    this.map = null;
    // Also clear global reference if used
    window._detailsMapInstance = null;
  }
  // (Optionally) Clear container innerHTML if you dynamically re-render
  const container = document.getElementById(this.mapId);
  if (container) {
    container.innerHTML = '';
    container._leaflet_id = undefined;
  }
}
}

window.EditSweeperView = EditSweeperView;