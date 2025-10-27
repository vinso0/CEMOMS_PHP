<div class="modal fade" id="addTruckModal" tabindex="-1" aria-labelledby="addTruckModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTruckModalLabel">Add New Truck</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form method="POST" action="/admin/operations/garbage_collection/store" id="addTruckForm" novalidate>
                <div class="modal-body">
                    <!-- Truck Details Section -->
                    <section class="mb-4">
                        <h6 class="section-title mb-3">
                            <i class="fas fa-truck me-2"></i>Truck Details
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="plateNumber" class="form-label required">Plate Number</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="plateNumber" 
                                    name="plate_number" 
                                    placeholder="e.g., ABC 1234" 
                                    required
                                    maxlength="20"
                                    pattern="[A-Za-z0-9\s-]+"
                                    title="Only letters, numbers, spaces, and hyphens allowed">
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="bodyNumber" class="form-label required">Body Number</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="bodyNumber" 
                                    name="body_number" 
                                    placeholder="e.g., SWM-309" 
                                    required
                                    maxlength="20"
                                    pattern="[A-Za-z0-9\-]+"
                                    title="Only letters, numbers, and hyphens allowed">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </section>

                    <!-- Assignment Section -->
                    <section class="mb-4">
                        <h6 class="section-title mb-3">
                            <i class="fas fa-user-tie me-2"></i>Assignment
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="assignedForeman" class="form-label required">Assigned Foreman</label>
                                <select class="form-select" id="assignedForeman" name="foreman_id" required>
                                    <option value="">Select Foreman</option>
                                    <?php if (!empty($foremen)): ?>
                                        <?php foreach ($foremen as $foreman): ?>
                                            <option value="<?= htmlspecialchars($foreman['id']) ?>">
                                                <?= htmlspecialchars($foreman['username']) ?> - <?= htmlspecialchars($foreman['role']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="scheduleType" class="form-label required">Schedule Type</label>
                                <select class="form-select" id="scheduleType" name="schedule_type" required onchange="toggleWeeklyDays()">
                                    <option value="">Select Schedule</option>
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <!-- ENHANCED: Weekly Schedule Days Selection -->
                        <div class="row" id="weeklyDaysSection" style="display: none;">
                            <div class="col-12">
                                <label class="form-label required">Select Days of the Week</label>
                                <div class="weekly-days-container">
                                    <div class="day-checkboxes">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="monday" name="schedule_days[]" value="Monday">
                                            <label class="form-check-label" for="monday">Mon</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="tuesday" name="schedule_days[]" value="Tuesday">
                                            <label class="form-check-label" for="tuesday">Tue</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="wednesday" name="schedule_days[]" value="Wednesday">
                                            <label class="form-check-label" for="wednesday">Wed</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="thursday" name="schedule_days[]" value="Thursday">
                                            <label class="form-check-label" for="thursday">Thu</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="friday" name="schedule_days[]" value="Friday">
                                            <label class="form-check-label" for="friday">Fri</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="saturday" name="schedule_days[]" value="Saturday">
                                            <label class="form-check-label" for="saturday">Sat</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="sunday" name="schedule_days[]" value="Sunday">
                                            <label class="form-check-label" for="sunday">Sun</label>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectWeekdays()">
                                            <i class="fas fa-business-time me-1"></i>Weekdays Only
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="selectAllDays()">
                                            <i class="fas fa-calendar-week me-1"></i>All Days
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearDays()">
                                            <i class="fas fa-times me-1"></i>Clear
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" id="weeklyDaysError"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Operation Time Section -->
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="operationTime" class="form-label required">Operation Time</label>
                                <input 
                                    type="time" 
                                    class="form-control" 
                                    id="operationTime" 
                                    name="operation_time" 
                                    required>
                                <div class="form-text">
                                    <i class="fas fa-clock me-1"></i>
                                    What time should this garbage collection operation start?
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </section>

                    <!-- Route Configuration Section -->
                    <section class="mb-4">
                        <h6 class="section-title mb-3">
                            <i class="fas fa-route me-2"></i>Route Configuration
                        </h6>
                        
                        <div class="mb-3">
                            <label for="routeName" class="form-label required">Route Name</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="routeName" 
                                name="route_name" 
                                placeholder="e.g., Downtown Collection Route" 
                                required
                                maxlength="100">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Route Points -->
                        <div class="route-points-container">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="startPoint" class="form-label required">Start Point</label>
                                    <input 
                                        type="text" 
                                        class="form-control route-point-input" 
                                        id="startPoint" 
                                        name="start_point" 
                                        placeholder="Click on map to select start point" 
                                        required
                                        readonly>
                                    <input type="hidden" id="startLat" name="start_lat">
                                    <input type="hidden" id="startLon" name="start_lon">
                                    <div class="invalid-feedback"></div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="midPoint" class="form-label">Mid Point <span class="text-muted">(Optional)</span></label>
                                    <input 
                                        type="text" 
                                        class="form-control route-point-input" 
                                        id="midPoint" 
                                        name="mid_point" 
                                        placeholder="Click on map to select mid point"
                                        readonly>
                                    <input type="hidden" id="midLat" name="mid_lat">
                                    <input type="hidden" id="midLon" name="mid_lon">
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="endPoint" class="form-label required">End Point</label>
                                    <input 
                                        type="text" 
                                        class="form-control route-point-input" 
                                        id="endPoint" 
                                        name="end_point" 
                                        placeholder="Click on map to select end point" 
                                        required
                                        readonly>
                                    <input type="hidden" id="endLat" name="end_lat">
                                    <input type="hidden" id="endLon" name="end_lon">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="route-controls mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="route-mode-selector">
                                        <label class="form-label">Select Point:</label>
                                        <div class="btn-group" role="group">
                                            <input type="radio" class="btn-check" name="pointMode" id="startMode" value="start" checked>
                                            <label class="btn btn-outline-primary btn-sm" for="startMode">Start</label>
                                            
                                            <input type="radio" class="btn-check" name="pointMode" id="midMode" value="mid">
                                            <label class="btn btn-outline-secondary btn-sm" for="midMode">Mid</label>
                                            
                                            <input type="radio" class="btn-check" name="pointMode" id="endMode" value="end">
                                            <label class="btn btn-outline-danger btn-sm" for="endMode">End</label>
                                        </div>
                                    </div>
                                    
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="clearRouteBtn">
                                        <i class="fas fa-undo me-1"></i>Clear Route
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Map Container -->
                        <div class="map-container mb-3">
                            <label class="form-label">Route Map</label>
                            <div id="addRouteMap" class="route-map"></div>
                            <div class="map-instructions mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Click on the map to set route points. Select the point type (Start, Mid, End) before clicking.
                                </small>
                            </div>
                        </div>
                    </section>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitTruckBtn">
                        <i class="fas fa-save me-1"></i>Add Truck
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Initialize RouteMapSelector for add truck modal
$('#addTruckModal').on('shown.bs.modal', function() {
    if (!window.routeMapSelector) {
        window.routeMapSelector = new RouteMapSelector('addRouteMap');
    }
    
    // Refresh map after modal is fully shown
    setTimeout(() => {
        if (window.routeMapSelector) {
            window.routeMapSelector.refreshMapSize();
        }
    }, 200);
});

// Clear form when modal is hidden
$('#addTruckModal').on('hidden.bs.modal', function() {
    if (window.routeMapSelector) {
        window.routeMapSelector.clearAllMarkers();
    }
});
</script>