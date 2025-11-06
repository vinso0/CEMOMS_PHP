class EditTruckView {
    constructor(modalId = 'editTruckModal', mapId = 'editRouteMap') {
        this.modal = document.getElementById(modalId);
        this.mapId = mapId;
        this.selector = null;
        this.wire();
    }

    wire() {
        if (!this.modal) return;

        // CRITICAL: Use shown.bs.modal for proper timing
        this.modal.addEventListener('shown.bs.modal', () => {
            console.log('Edit modal shown, initializing map...');
            this.initMap();
        });
        
        this.modal.addEventListener('hidden.bs.modal', () => {
            console.log('Edit modal hidden, resetting...');
            this.reset();
        });

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
        window.populateEditModal = (d) => this.populate(d);

        // Weekly helpers
        window.toggleEditWeeklyDays = () => this.toggleWeeklyDays();
        window.selectEditWeekdays = () => this.selectWeekdays();
        window.selectEditAllDays = () => this.selectAllDays();
        window.clearEditDays = () => this.clearDays();
        window.clearEditRoute = () => this.clearRoute();
    }

    initMap() { 
        const container = document.getElementById(this.mapId);
        if (!container) {
            console.error(`Map container ${this.mapId} not found`);
            return;
        }

        try {
            // CRITICAL: Destroy existing instance first
            if (this.selector) {
                this.selector.destroy();
            }
            
            // Create new instance  
            this.selector = new RouteMapSelector(this.mapId, {
                defaultLat: 14.5995, 
                defaultLng: 120.9842, 
                defaultZoom: 13
            });
            
            // CRITICAL: Use modal-specific initialization
            setTimeout(() => {
                if (this.selector) {
                    this.selector.initializeForModal();
                }
            }, 150);
            
            container.classList.add('map-loaded');
            console.log('✅ Edit modal map initialized successfully');
            
        } catch (error) {
            console.error('❌ Failed to initialize edit modal map:', error);
        }
    }

    reset() {
        const form = document.getElementById('editTruckForm');
        if (form) { 
            form.reset(); 
            form.classList.remove('was-validated'); 
        }
        
        // CRITICAL: Proper cleanup
        if (this.selector) {
            this.selector.clearAllMarkers();
        }
        
        const container = document.getElementById(this.mapId);
        if (container) {
            container.classList.remove('map-loaded');
        }
        
        console.log('✅ Edit modal reset completed');
    }

    populate(data) {
        if (!data?.truck_id) {
            console.warn('Missing truck_id in EditTruckView.populate payload:', data);
            return;
        }

        const setVal = (id, v) => { 
            const el = document.getElementById(id); 
            if (el) el.value = v || ''; 
        };
        
        setVal('editTruckId', data.truck_id);
        setVal('editScheduleId', data.schedule_id);  
        setVal('editRouteId', data.route_id);
        setVal('editPlateNumber', data.plate_number);
        setVal('editBodyNumber', data.body_number);
        setVal('editRouteName', data.route_name);
        
        const foreman = document.getElementById('editAssignedForeman');
        if (foreman) foreman.value = data.foreman_id || '';
        
        const sched = document.getElementById('editScheduleType');
        if (sched) { 
            sched.value = data.schedule || ''; 
            this.toggleWeeklyDays(); 
        }
        
        setVal('editOperationTime', data.operation_time);

        if (data.schedule === 'Weekly' && data.weekly_days) {
            this.clearDays();
            (Array.isArray(data.weekly_days) ? data.weekly_days : String(data.weekly_days).split(','))
                .map(d => d.trim())
                .forEach(day => { 
                    const cb = document.getElementById(`edit${day}`); 
                    if (cb) cb.checked = true; 
                });
        }

        // CRITICAL: Load points after map is ready and properly initialized
        setTimeout(() => {
            if (this.selector && this.selector.isInitialized) {
                if (data.route_id) {
                    this.loadRoutePoints(data.route_id);
                } else {
                    this.clearRouteInputs();
                }
            } else {
                // Retry after a longer delay if map isn't ready
                setTimeout(() => {
                    if (data.route_id) {
                        this.loadRoutePoints(data.route_id);
                    } else {
                        this.clearRouteInputs();
                    }
                }, 1000);
            }
        }, 500);
    }

    async loadRoutePoints(routeId) {
        try {
            const res = await fetch(`/admin/operations/garbage_collection/get_route_points?route_id=${routeId}`);
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
        if (!this.selector || !this.selector.isInitialized) {
            console.warn('Map selector not ready for populating route points');
            return;
        }
        
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

    toggleWeeklyDays() {
        const type = document.getElementById('editScheduleType')?.value;
        const section = document.getElementById('editWeeklyDaysSection');
        if (!section) return;
        if (type === 'Weekly') section.style.display = 'block';
        else { section.style.display = 'none'; this.clearDays(); }
    }
    
    selectWeekdays() { 
        this.clearDays(); 
        ['editMonday','editTuesday','editWednesday','editThursday','editFriday'].forEach(id => {
            const checkbox = document.getElementById(id);
            if (checkbox) checkbox.checked = true;
        }); 
    }
    
    selectAllDays() { 
        document.querySelectorAll('#editTruckModal input[name="schedule_days[]"]').forEach(cb => cb.checked = true); 
    }
    
    clearDays() { 
        document.querySelectorAll('#editTruckModal input[name="schedule_days[]"]').forEach(cb => cb.checked = false); 
    }

    clearRoute() { 
        if (this.selector) {
            this.selector.clearAllMarkers(); 
        }
        this.clearRouteInputs(); 
    }
}

window.EditTruckView = EditTruckView;
