
class RouteDetailsView {
  constructor(modalId = 'routeDetailsModal', mapId = 'route-map') {
    this.modal = document.getElementById(modalId);
    this.mapId = mapId;
    this.map = null;
    this.markers = [];
    this.path = null;
    this.wire();
  }

  wire() {
    if (!this.modal) return;

    this.modal.addEventListener('shown.bs.modal', () => {
      setTimeout(() => this.initMap(), 100);
    });
    this.modal.addEventListener('hidden.bs.modal', () => this.cleanup());

    // Event delegation for buttons
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.route-details-btn');
        if (btn) {
            console.log('🔍 Route details button clicked'); // DEBUG
            const truckData = JSON.parse(btn.dataset.truckData);
            console.log('🔍 Truck data parsed:', truckData); // DEBUG
            setTimeout(() => {
                console.log('🔍 Calling open() method'); // DEBUG
                this.open(truckData);
            }, 200);
        }
    });
}


  open(truckData) {
    console.log('🔍 RouteDetailsView.open called with:', truckData);
    // populate header
    const txt = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v || '-'; };
    txt('details-plate-number', truckData.plate_number);
    txt('details-body-number', truckData.body_number);
    txt('details-route-name', truckData.route_name);
    txt('details-foreman', truckData.foreman_name);

    const sched = truckData.schedule ? truckData.schedule.charAt(0).toUpperCase() + truckData.schedule.slice(1) : '-';
    txt('details-schedule-type', sched);

    let op = truckData.operation_time || '-';
    if (op && op !== '-') {
      const dt = new Date(`2000-01-01 ${op}`);
      op = dt.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
    }
    txt('details-operation-time', op);

    const weeklyContainer = document.getElementById('weekly-days-container');
    const weeklyDaysEl = document.getElementById('details-weekly-days');
    if (truckData.schedule === 'weekly' && truckData.weekly_days?.length) {
      if (weeklyContainer) weeklyContainer.style.display = 'block';
      if (weeklyDaysEl) {
        weeklyDaysEl.innerHTML = truckData.weekly_days.map(d => `<span class="badge bg-primary me-1 mb-1">${d.substring(0,3)}</span>`).join('');
      }
    } else if (weeklyContainer) weeklyContainer.style.display = 'none';

    this.initMap();

    if (truckData.route_id) {
      this.loadRoutePoints(truckData.route_id);
    } else {
      const list = document.getElementById('route-points-list');
      if (list) {
        list.innerHTML = `<div class="empty-points text-center p-4">
          <i class="fas fa-exclamation-circle text-warning mb-2" style="font-size: 2rem;"></i>
          <h6>No Route Assigned</h6>
          <p class="text-muted mb-0">This truck has no route assigned yet.</p>
        </div>`;
      }
    }
  }

  initMap() {
    const container = document.getElementById(this.mapId);
    if (!container) return;

    if (this.map) { try { this.map.remove(); } catch {} this.map = null; }
    this.map = L.map(this.mapId, {
      center: [14.6091, 121.0223], zoom: 12, zoomControl: true, attributionControl: true,
      preferCanvas: false, worldCopyJump: false, maxBoundsViscosity: 1.0,
      fadeAnimation: false, zoomAnimation: false, markerZoomAnimation: false
    });
    const layer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors', maxZoom: 19, keepBuffer: 2, updateWhenZooming: false, updateWhenIdle: true, crossOrigin: true,
      errorTileUrl: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='
    }).addTo(this.map);

    this.markers = []; this.path = null;

    this.map.whenReady(() => {
      setTimeout(() => this.map.invalidateSize(true), 100);
      setTimeout(() => { this.map.invalidateSize(true); layer.redraw(); }, 300);
      setTimeout(() => this.map.invalidateSize(true), 500);
    });
  }

  async loadRoutePoints(routeId) {
    const list = document.getElementById('route-points-list');
    if (list) list.innerHTML = `<div class="loading-points text-center p-4">
      <i class="fas fa-spinner fa-spin text-muted mb-2"></i>
      <p class="text-muted mb-0">Loading route points...</p>
    </div>`;

    try {
      const r = await fetch(`/admin/operations/garbage_collection/get_route_points?route_id=${routeId}`);
      if (!r.ok) throw new Error(`${r.status} ${r.statusText}`);
      const data = await r.json();
      if (!data.route_points?.length) {
        if (list) list.innerHTML = `<div class="empty-points text-center p-4">
          <i class="fas fa-map-pin text-muted mb-2" style="font-size: 2rem;"></i>
          <h6>No Route Points</h6><p class="text-muted mb-0">This route has no points defined.</p></div>`;
        return;
      }
      this.renderPointsList(data.route_points);
      this.drawOnMap(data.route_points);
    } catch (e) {
      console.error('loadRoutePoints error:', e);
      if (list) list.innerHTML = `<div class="empty-points text-center p-4">
        <i class="fas fa-exclamation-triangle text-danger mb-2" style="font-size: 2rem;"></i>
        <h6>Error Loading Route Points</h6><p class="text-muted mb-0">${e.message}</p></div>`;
    }
  }

  renderPointsList(points) {
    const list = document.getElementById('route-points-list');
    if (!list) return;
    list.innerHTML = points.map((p, i) => {
      const cls = (p.name || '').toLowerCase().includes('start') ? 'start' :
                  (p.name || '').toLowerCase().includes('end') ? 'end' : 'mid';
      return `<div class="point-item" data-point-index="${i}" onclick="window.__highlightRoutePoint?.(${i})">
        <div class="point-number ${cls}">${i+1}</div>
        <div class="point-info"><div class="point-name">${p.name}</div><div class="point-address">${p.address}</div></div>
      </div>`;
    }).join('');
    window.__highlightRoutePoint = (idx) => this.highlight(idx);
  }

  drawOnMap(points) {
    if (!this.map || !points.length) return;

    // clear
    this.markers.forEach(m => this.map.removeLayer(m)); this.markers = [];
    if (this.path) { this.map.removeLayer(this.path); this.path = null; }

    const coords = [];
    const bounds = L.latLngBounds();

    points.forEach((p, i) => {
      const ll = [p.lat, p.lng];
      coords.push(ll); bounds.extend(ll);
      const type = i === 0 ? 'start' : (i === points.length - 1 ? 'end' : 'collection');
      const color = type === 'start' ? '#28a745' : (type === 'end' ? '#dc3545' : '#007bff');
      const icon = L.divIcon({
        html: `<div style="background-color:${color};width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;font-size:12px;border:2px solid #fff;box-shadow:0 2px 4px rgba(0,0,0,.3);">${i+1}</div>`,
        className: 'custom-route-marker', iconSize: [24,24], iconAnchor: [12,12]
      });
      const m = L.marker(ll, { icon }).addTo(this.map).bindPopup(`<div style="min-width:150px;"><h6>${p.name}</h6><p class="mb-0"><small>${p.address}</small></p></div>`);
      this.markers.push(m);
    });

    // Draw road-following route via OSRM
    if (coords.length > 1) this.drawRoadRoute(coords);
    this.map.fitBounds(bounds, { padding: [20,20] });
  }

  async drawRoadRoute(coords) {
    const container = document.getElementById(this.mapId);
    const badge = document.createElement('div');
    badge.id = 'route-loading';
    badge.innerHTML = `<div style="position:absolute;top:10px;right:10px;background:rgba(255,255,255,.9);padding:8px 12px;border-radius:4px;font-size:12px;z-index:1000;box-shadow:0 2px 4px rgba(0,0,0,.2);"><i class="fas fa-spinner fa-spin me-1"></i>Calculating route...</div>`;
    container.appendChild(badge);

    try {
      const wp = coords.map(c => `${c[1]},${c[0]}`).join(';');
      const url = `https://router.project-osrm.org/route/v1/driving/${wp}?overview=full&geometries=geojson`;
      const r = await fetch(url); if (!r.ok) throw new Error(`Routing ${r.status}`);
      const data = await r.json(); const route = data.routes?.[0];
      badge.remove?.();
      if (!route) return this.drawStraight(coords);
      const rcoords = route.geometry.coordinates.map(c => [c[1], c[0]]);
      this.path = L.polyline(rcoords, { color:'#2196f3', weight:4, opacity:.8, smoothFactor:1 }).addTo(this.map);
      const km = (route.distance/1000).toFixed(1), min = Math.round(route.duration/60);
      this.path.bindPopup(`<div style="text-align:center;"><h6>📍 Route Information</h6><p style="margin:5px 0;"><strong>Distance:</strong> ${km} km</p><p style="margin:5px 0;"><strong>Duration:</strong> ${min} min</p><small style="color:#666;">Following actual roads</small></div>`);
    } catch (e) {
      console.warn('OSRM failed, drawing straight:', e);
      badge.remove?.();
      this.drawStraight(coords);
    }
  }

  drawStraight(coords) {
    if (!this.map) return;
    this.path = L.polyline(coords, { color:'#ff9800', weight:3, opacity:.7, dashArray:'10,5' }).addTo(this.map);
    this.path.bindPopup(`<div style="text-align:center;"><h6>📍 Direct Route</h6><p style="margin:5px 0;color:#ff9800;"><strong>⚠️ Straight line connection</strong></p><small style="color:#666;">Road routing unavailable</small></div>`);
  }

  highlight(idx) {
    document.querySelectorAll('.point-item').forEach(el => el.classList.remove('active'));
    const item = document.querySelector(`.point-item[data-point-index="${idx}"]`);
    if (item) item.classList.add('active');
    const m = this.markers[idx]; if (!m || !this.map) return;
    m.openPopup(); this.map.panTo(m.getLatLng(), { animate:true });
    setTimeout(() => this.map.setZoom(Math.max(this.map.getZoom(), 15), { animate:true }), 500);
  }

  cleanup() {
    // Blur any focused elements before cleanup
    if (document.activeElement && document.activeElement.closest('#routeDetailsModal')) {
        document.activeElement.blur();
    }
    
    if (this.map) { try { this.map.remove(); } catch{} this.map = null; }
    this.markers = []; this.path = null;
    document.querySelectorAll('.point-item').forEach(i => i.classList.remove('active'));
  }
}

window.RouteDetailsView = RouteDetailsView;
