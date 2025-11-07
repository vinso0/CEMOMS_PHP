class AddSweeperView {
  constructor(modalId = 'addSweeperModal', mapId = 'addSweeperRouteMap') {
    this.modal = document.getElementById(modalId);
    this.mapId = mapId;
    this.selector = null;
    this.isHiding = false;
    this.wire();
  }

  wire() {
    if (!this.modal) return;

    // CRITICAL FIX: Blur focused elements and proper cleanup
    this.modal.addEventListener('hide.bs.modal', (e) => {
      if (this.isHiding) {
        e.preventDefault();
        return;
      }

      // CRITICAL FIX: Blur any focused elements to prevent aria-hidden warning
      const focusedElement = document.activeElement;
      if (focusedElement && this.modal.contains(focusedElement)) {
        focusedElement.blur();
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
      this.resetForm();
    });

    const form = document.getElementById('addSweeperForm');
    if (form) {
      form.addEventListener('submit', (e) => {
        if (!this.validate(form)) {
          e.preventDefault();
          e.stopPropagation();
        }
        form.classList.add('was-validated');
      });
    }

  }

  preCleanup() {
    console.log('🧹 Add: Pre-cleanup starting...');

    if (this.selector) {
      try {
        this.selector.destroy();
        console.log('✅ Add: Map destroyed');
      } catch (error) {
        console.warn('⚠️ Add: Cleanup error:', error);
      }
      this.selector = null;
    }
  }

  initMap() {
    console.log('🗺️ Add: Initializing map...');

    const container = document.getElementById(this.mapId);
    if (!container) {
      console.error(`Map container ${this.mapId} not found`);
      return;
    }

    // CRITICAL FIX: Ensure cleanup before init
    this.preCleanup();

    // CRITICAL FIX: Clear container completely
    container.innerHTML = '';
    if (container._leaflet_id) {
      delete container._leaflet_id;
      container._leaflet = false;
    }

    // CRITICAL FIX: Check dimensions
    if (container.offsetWidth === 0 || container.offsetHeight === 0) {
      console.log('⏳ Waiting for container dimensions...');
      setTimeout(() => this.initMap(), 100);
      return;
    }

    if (!window.RouteMapSelector) {
      console.error('RouteMapSelector class not available');
      return;
    }

    // CRITICAL FIX: Add delay before initialization
    setTimeout(() => {
      try {
        this.selector = new window.RouteMapSelector(this.mapId, {
          defaultLat: 14.5995,
          defaultLng: 120.9842,
          defaultZoom: 13
        });

        // CRITICAL FIX: Additional delay for size refresh
        setTimeout(() => {
          if (this.selector && !this.selector._destroyed) {
            this.selector.refreshMapSize();
          }
        }, 300);

        console.log('✅ Add: Map initialized');
      } catch (error) {
        console.error('❌ Add: Map init failed:', error);
      }
    }, 100);
  }

  resetForm() {
    const form = document.getElementById('addSweeperForm');
    if (form) {
      form.reset();
      form.classList.remove('was-validated');
    }

    this.preCleanup();
  }

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

window.AddSweeperView = AddSweeperView;