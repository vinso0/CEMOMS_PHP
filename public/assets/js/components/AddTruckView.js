class AddTruckView {
  constructor(modalId = 'addTruckModal', mapId = 'addRouteMap') {
    this.modal = document.getElementById(modalId);
    this.mapId = mapId;
    this.selector = null;
    this.isDragging = false;
    this.wire();
  }

  wire() {
    if (!this.modal) return;

    // CRITICAL FIX: Prevent modal hide during drag
    this.modal.addEventListener('hide.bs.modal', (e) => {
      if (this.isDragging) {
        console.log('🛑 Preventing modal hide during drag');
        e.preventDefault();
        // Stop interaction and retry hide
        if (this.selector?.map) {
          this.selector.map.stop();
        }
        this.isDragging = false;
        setTimeout(() => {
          const modalInstance = bootstrap.Modal.getInstance(this.modal);
          if (modalInstance) modalInstance.hide();
        }, 50);
        return;
      }
      this.preCleanup();
    });

    this.modal.addEventListener('shown.bs.modal', () => this.initMap());
    this.modal.addEventListener('hidden.bs.modal', () => this.resetForm());

    // Form validation
    const form = document.getElementById('addTruckForm');
    if (form) {
      form.addEventListener('submit', (e) => {
        if (!this.validate(form)) {
          e.preventDefault();
          e.stopPropagation();
        }
        form.classList.add('was-validated');
      });
    }

    // Quick select day helpers
    window.selectWeekdays = () => this.selectWeekdays();
    window.selectAllDays = () => this.selectAllDays();
    window.clearDays = () => this.clearDays();
    window.toggleWeeklyDays = () => this.toggleWeeklyDays();
  }

  preCleanup() {
    console.log('🧹 Pre-cleanup starting...');
    if (this.selector) {
      try {
        this.selector.destroy();
        console.log('✅ Map destroyed in pre-cleanup');
      } catch (error) {
        console.warn('⚠️ Error during pre-cleanup:', error);
      }
      this.selector = null;
    }
    this.isDragging = false;
  }

  initMap() {
    const container = document.getElementById(this.mapId);
    if (!container) {
        console.error(`Map container ${this.mapId} not found`);
        return;
    }

    console.log('🗺️ Initializing map...');

    // Ensure clean slate
    this.preCleanup();

    // Clear container
    container.innerHTML = '';
    if (container._leaflet_id) {
      container._leaflet_id = null;
      container._leaflet = false;
    }

    // Check if RouteMapSelector is available
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

        // CRITICAL FIX: Track dragging state
        if (this.selector.map) {
          this.selector.map.on('dragstart', () => {
            this.isDragging = true;
          });
          this.selector.map.on('dragend', () => {
            this.isDragging = false;
          });
        }

        setTimeout(() => this.selector?.refreshMapSize(), 200);
        container.classList.add('map-loaded');
        console.log('✅ Map initialized successfully');
    } catch (error) {
        console.error('❌ Failed to initialize RouteMapSelector:', error);
    }
  }

  resetForm() {
    console.log('📋 Resetting form...');
    const form = document.getElementById('addTruckForm');
    if (form) {
      form.reset();
      form.classList.remove('was-validated');
      form.querySelectorAll('.is-invalid').forEach(f => f.classList.remove('is-invalid'));
    }
    
    // Final cleanup
    this.preCleanup();
    
    const container = document.getElementById(this.mapId);
    if (container) {
      container.classList.remove('map-loaded');
    }
  }

  // ... rest of your validation methods (keep unchanged)
  validate(form) {
    let ok = true;

    form.querySelectorAll('[required]').forEach(field => {
      if (!field.value.trim()) {
        this.setErr(field, 'This field is required');
        ok = false;
      } else {
        this.clearErr(field);
      }
    });

    const startLat = document.getElementById('startLat')?.value;
    const startLon = document.getElementById('startLon')?.value;
    const endLat = document.getElementById('endLat')?.value;
    const endLon = document.getElementById('endLon')?.value;

    if (!startLat || !startLon) {
      const f = document.getElementById('startPoint');
      if (f) { this.setErr(f, 'Please select a start point on the map'); ok = false; }
    }
    if (!endLat || !endLon) {
      const f = document.getElementById('endPoint');
      if (f) { this.setErr(f, 'Please select an end point on the map'); ok = false; }
    }

    if (!this.validateWeeklyDays()) ok = false;

    return ok;
  }

  setErr(field, msg) {
    field.classList.add('is-invalid');
    const fb = field.parentElement.querySelector('.invalid-feedback');
    if (fb) fb.textContent = msg;
  }
  
  clearErr(field) {
    field.classList.remove('is-invalid');
    const fb = field.parentElement.querySelector('.invalid-feedback');
    if (fb) fb.textContent = '';
  }

  toggleWeeklyDays() {
    const type = document.getElementById('scheduleType')?.value;
    const section = document.getElementById('weeklyDaysSection');
    if (!section) return;
    const checks = document.querySelectorAll('input[name="schedule_days[]"]');
    if (type === 'Weekly') {
      section.style.display = 'block';
      checks.forEach(c => c.required = true);
    } else {
      section.style.display = 'none';
      checks.forEach(c => { c.required = false; c.checked = false; });
      this.clearWeeklyDaysValidation();
    }
  }
  
  selectWeekdays() { 
    this.clearDays(); 
    ['monday','tuesday','wednesday','thursday','friday'].forEach(d => {
      const checkbox = document.getElementById(d);
      if (checkbox) checkbox.checked = true;
    }); 
    this.clearWeeklyDaysValidation(); 
  }
  
  selectAllDays() { 
    document.querySelectorAll('input[name="schedule_days[]"]').forEach(c => c.checked = true); 
    this.clearWeeklyDaysValidation(); 
  }
  
  clearDays() { 
    document.querySelectorAll('input[name="schedule_days[]"]').forEach(c => c.checked = false); 
  }
  
  clearWeeklyDaysValidation() {
    const err = document.getElementById('weeklyDaysError'); 
    if (err) err.textContent = '';
    document.querySelectorAll('input[name="schedule_days[]"]').forEach(c => c.classList.remove('is-invalid'));
  }
  
  validateWeeklyDays() {
    const type = document.getElementById('scheduleType')?.value;
    if (type === 'Weekly') {
      const selected = document.querySelectorAll('input[name="schedule_days[]"]:checked');
      const err = document.getElementById('weeklyDaysError');
      if (selected.length === 0) {
        if (err) err.textContent = 'Please select at least one day for Weekly schedule.';
        document.querySelectorAll('input[name="schedule_days[]"]').forEach(c => c.classList.add('is-invalid'));
        return false;
      }
      this.clearWeeklyDaysValidation();
    }
    return true;
  }
}

window.AddTruckView = AddTruckView;
