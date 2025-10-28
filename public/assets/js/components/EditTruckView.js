import { RouteMapSelector } from './RouteMapSelector.js';

export class EditTruckView {
  constructor(modalId = 'editTruckModal', mapId = 'editRouteMap') {
    this.modal = document.getElementById(modalId);
    this.mapId = mapId;
    this.selector = null;
    this.wire();
  }

  wire() {
    if (!this.modal) return;

    this.modal.addEventListener('shown.bs.modal', () => this.initMap());
    this.modal.addEventListener('hidden.bs.modal', () => this.reset());

    // Delegate clicks on edit buttons
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('.edit-truck-btn');
      if (!btn) return;
      const payload = {
        truck_id: btn.dataset.truckId || '',
        plate_number: btn.dataset.plateNumber || '',
        body_number: btn.dataset.bodyNumber || '',
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

    // expose for inline calls if needed
    window.populateEditModal = (data) => this.populate(data);

    // weekly helpers
    window.toggleEditWeeklyDays = () => this.toggleWeeklyDays();
    window.selectEditWeekdays = () => this.selectWeekdays();
    window.selectEditAllDays = () => this.selectAllDays();
    window.clearEditDays = () => this.clearDays();
    window.clearEditRoute = () => this.clearRoute();
  }

  initMap() {
    if (!this.selector) {
      this.selector = new RouteMapSelector(this.mapId, {
        defaultLat: 14.5995, defaultLng: 120.9842, defaultZoom: 13
      });
    }
    setTimeout(() => this.selector?.refreshMapSize(), 150);
    const container = document.getElementById(this.mapId);
    if (container) container.classList.add('map-loaded');
  }

  reset() {
    const form = document.getElementById('editTruckForm');
    if (form) { form.reset(); form.classList.remove('was-validated'); }
    this.selector?.clearAllMarkers();
  }

  populate(data) {
    const setVal = (id, v) => { const el = document.getElementById(id); if (el) el.value = v || ''; };
    setVal('editTruckId', data.truck_id);
    setVal('editScheduleId', data.schedule_id);
    setVal('editRouteId', data.route_id);
    setVal('editPlateNumber', data.plate_number);
    setVal('editBodyNumber', data.body_number);
    setVal('editRouteName', data.route_name);
    const foreman = document.getElementById('editAssignedForeman');
    if (foreman) foreman.value = data.foreman_id || '';
    const sched = document.getElementById('editScheduleType');
    if (sched) { sched.value = data.schedule || ''; this.toggleWeeklyDays(); }
    setVal('editOperationTime', data.operation_time);

    if (data.schedule === 'weekly' && data.weekly_days) {
      this.clearDays();
      (Array.isArray(data.weekly_days) ? data.weekly_days : String(data.weekly_days).split(','))
        .map(d => d.trim())
        .forEach(day => { const cb = document.getElementById(`edit${day}`); if (cb) cb.checked = true; });
    }

    // Load points after map ready
    setTimeout(() => {
      if (data.route_id) this.loadRoutePoints(data.route_id);
      else this.clearRouteInputs();
    }, 500);
  }

  async loadRoutePoints(routeId) {
    try {
      const res = await fetch(`/admin/operations/garbage_collection/get_route_points?route_id=${routeId}`);
      if (!res.ok) throw new Error(`${res.status} ${res.statusText}`);
      const data = await res.json();
      if (!data.route_points?.length) { this.clearRouteInputs(); return; }
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

      this.selector.setPoint(type, p.lat, p.lng, p.address); // avoid reverse geocode
      const id = type.charAt(0).toUpperCase() + type.slice(1);
      const set = (suffix, v) => { const el = document.getElementById(`edit${id}${suffix}`); if (el) el.value = v || ''; };
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

  // weekly helpers
  toggleWeeklyDays() {
    const type = document.getElementById('editScheduleType')?.value;
    const section = document.getElementById('editWeeklyDaysSection');
    if (!section) return;
    if (type === 'weekly') section.style.display = 'block';
    else { section.style.display = 'none'; this.clearDays(); }
  }
  selectWeekdays() { this.clearDays(); ['editMonday','editTuesday','editWednesday','editThursday','editFriday'].forEach(id => document.getElementById(id).checked = true); }
  selectAllDays() { document.querySelectorAll('#editTruckModal input[name="schedule_days[]"]').forEach(cb => cb.checked = true); }
  clearDays() { document.querySelectorAll('#editTruckModal input[name="schedule_days[]"]').forEach(cb => cb.checked = false); }

  clearRoute() { this.selector?.clearAllMarkers(); this.clearRouteInputs(); }
}
