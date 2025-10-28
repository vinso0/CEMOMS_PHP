import { RouteMapSelector } from './RouteMapSelector.js';

export class AddTruckView {
  constructor(modalId = 'addTruckModal', mapId = 'addRouteMap') {
    this.modal = document.getElementById(modalId);
    this.mapId = mapId;
    this.selector = null;
    this.wire();
  }

  wire() {
    if (!this.modal) return;

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

  initMap() {
    const container = document.getElementById(this.mapId);
    if (!container) return;

    // Recreate instance each time to avoid stale state
    this.selector = new RouteMapSelector(this.mapId, {
      defaultLat: 14.5995,
      defaultLng: 120.9842,
      defaultZoom: 13
    });

    setTimeout(() => this.selector?.refreshMapSize(), 150);
    container.classList.add('map-loaded');
  }

  resetForm() {
    const form = document.getElementById('addTruckForm');
    if (form) {
      form.reset();
      form.classList.remove('was-validated');
      form.querySelectorAll('.is-invalid').forEach(f => f.classList.remove('is-invalid'));
    }
    this.selector?.clearAllMarkers();
    const container = document.getElementById(this.mapId);
    if (container) container.classList.remove('map-loaded');
  }

  validate(form) {
    let ok = true;

    // required fields
    form.querySelectorAll('[required]').forEach(field => {
      if (!field.value.trim()) {
        this.setErr(field, 'This field is required');
        ok = false;
      } else {
        this.clearErr(field);
      }
    });

    // route points
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

  // weekly days helpers
  toggleWeeklyDays() {
    const type = document.getElementById('scheduleType')?.value;
    const section = document.getElementById('weeklyDaysSection');
    if (!section) return;
    const checks = document.querySelectorAll('input[name="schedule_days[]"]');
    if (type === 'weekly') {
      section.style.display = 'block';
      checks.forEach(c => c.required = true);
    } else {
      section.style.display = 'none';
      checks.forEach(c => { c.required = false; c.checked = false; });
      this.clearWeeklyDaysValidation();
    }
  }
  selectWeekdays() { this.clearDays(); ['monday','tuesday','wednesday','thursday','friday'].forEach(d => document.getElementById(d).checked = true); this.clearWeeklyDaysValidation(); }
  selectAllDays() { document.querySelectorAll('input[name="schedule_days[]"]').forEach(c => c.checked = true); this.clearWeeklyDaysValidation(); }
  clearDays() { document.querySelectorAll('input[name="schedule_days[]"]').forEach(c => c.checked = false); }
  clearWeeklyDaysValidation() {
    const err = document.getElementById('weeklyDaysError'); if (err) err.textContent = '';
    document.querySelectorAll('input[name="schedule_days[]"]').forEach(c => c.classList.remove('is-invalid'));
  }
  validateWeeklyDays() {
    const type = document.getElementById('scheduleType')?.value;
    if (type === 'weekly') {
      const selected = document.querySelectorAll('input[name="schedule_days[]"]:checked');
      const err = document.getElementById('weeklyDaysError');
      if (selected.length === 0) {
        if (err) err.textContent = 'Please select at least one day for weekly schedule.';
        document.querySelectorAll('input[name="schedule_days[]"]').forEach(c => c.classList.add('is-invalid'));
        return false;
      }
      this.clearWeeklyDaysValidation();
    }
    return true;
  }
}
