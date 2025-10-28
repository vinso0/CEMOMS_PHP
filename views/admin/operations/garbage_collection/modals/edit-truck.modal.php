<div class="modal fade" id="editTruckModal" tabindex="-1" aria-labelledby="editTruckModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTruckModalLabel">
                    <i class="fas fa-edit me-2"></i>Edit Truck
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form method="POST" action="/admin/operations/garbage_collection/update" id="editTruckForm" novalidate>
                <input type="hidden" id="editTruckId" name="truck_id" value="">
                <input type="hidden" id="editScheduleId" name="schedule_id" value="">
                <input type="hidden" id="editRouteId" name="route_id" value="">
                
                <div class="modal-body">
                    <!-- Truck Details Section -->
                    <section class="mb-4">
                        <h6 class="section-title mb-3">
                            <i class="fas fa-truck me-2"></i>Truck Details
                        </h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editPlateNumber" class="form-label required">Plate Number</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="editPlateNumber" 
                                    name="plate_number" 
                                    placeholder="e.g., ABC 1234" 
                                    required
                                    maxlength="20"
                                    pattern="[A-Za-z0-9\s-]+"
                                    title="Only letters, numbers, spaces, and hyphens allowed">
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="editBodyNumber" class="form-label required">Body Number</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="editBodyNumber" 
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
                                <label for="editAssignedForeman" class="form-label required">Assigned Foreman</label>
                                <select class="form-select" id="editAssignedForeman" name="foreman_id" required>
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
                                <label for="editScheduleType" class="form-label required">Schedule Type</label>
                                <select class="form-select" id="editScheduleType" name="schedule_type" required onchange="toggleEditWeeklyDays()">
                                    <option value="">Select Schedule</option>
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <!-- Weekly Schedule Days Selection -->
                        <div class="row" id="editWeeklyDaysSection" style="display: none;">
                            <div class="col-12">
                                <label class="form-label required">Select Days of the Week</label>
                                <div class="weekly-days-container">
                                    <div class="day-checkboxes">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="editMonday" name="schedule_days[]" value="Monday">
                                            <label class="form-check-label" for="editMonday">Mon</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="editTuesday" name="schedule_days[]" value="Tuesday">
                                            <label class="form-check-label" for="editTuesday">Tue</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="editWednesday" name="schedule_days[]" value="Wednesday">
                                            <label class="form-check-label" for="editWednesday">Wed</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="editThursday" name="schedule_days[]" value="Thursday">
                                            <label class="form-check-label" for="editThursday">Thu</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="editFriday" name="schedule_days[]" value="Friday">
                                            <label class="form-check-label" for="editFriday">Fri</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="editSaturday" name="schedule_days[]" value="Saturday">
                                            <label class="form-check-label" for="editSaturday">Sat</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="editSunday" name="schedule_days[]" value="Sunday">
                                            <label class="form-check-label" for="editSunday">Sun</label>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectEditWeekdays()">
                                            <i class="fas fa-business-time me-1"></i>Weekdays Only
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="selectEditAllDays()">
                                            <i class="fas fa-calendar-week me-1"></i>All Days
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearEditDays()">
                                            <i class="fas fa-times me-1"></i>Clear
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" id="editWeeklyDaysError"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Operation Time Section -->
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="editOperationTime" class="form-label required">Operation Time</label>
                                <input 
                                    type="time" 
                                    class="form-control" 
                                    id="editOperationTime" 
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
                            <label for="editRouteName" class="form-label required">Route Name</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="editRouteName" 
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
                                    <label for="editStartPoint" class="form-label required">Start Point</label>
                                    <input 
                                        type="text" 
                                        class="form-control route-point-input" 
                                        id="editStartPoint" 
                                        name="start_point" 
                                        placeholder="Click on map to select start point" 
                                        required
                                        readonly>
                                    <input type="hidden" id="editStartLat" name="start_lat">
                                    <input type="hidden" id="editStartLon" name="start_lon">
                                    <div class="invalid-feedback"></div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="editMidPoint" class="form-label">Mid Point <span class="text-muted">(Optional)</span></label>
                                    <input 
                                        type="text" 
                                        class="form-control route-point-input" 
                                        id="editMidPoint" 
                                        name="mid_point" 
                                        placeholder="Click on map to select mid point"
                                        readonly>
                                    <input type="hidden" id="editMidLat" name="mid_lat">
                                    <input type="hidden" id="editMidLon" name="mid_lon">
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="editEndPoint" class="form-label required">End Point</label>
                                    <input 
                                        type="text" 
                                        class="form-control route-point-input" 
                                        id="editEndPoint" 
                                        name="end_point" 
                                        placeholder="Click on map to select end point" 
                                        required
                                        readonly>
                                    <input type="hidden" id="editEndLat" name="end_lat">
                                    <input type="hidden" id="editEndLon" name="end_lon">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Route Mode Selector -->
                        <div class="route-controls mb-3">
                            <div class="route-mode-selector">
                                <strong>Select Point:</strong>
                                <div class="btn-group" role="group" aria-label="Point selection">
                                    <input type="radio" class="btn-check" name="editPointMode" id="editStartMode" value="start" checked>
                                    <label class="btn btn-outline-success" for="editStartMode">
                                        <i class="fas fa-play me-1"></i>Start
                                    </label>

                                    <input type="radio" class="btn-check" name="editPointMode" id="editMidMode" value="mid">
                                    <label class="btn btn-outline-primary" for="editMidMode">
                                        <i class="fas fa-pause me-1"></i>Mid
                                    </label>

                                    <input type="radio" class="btn-check" name="editPointMode" id="editEndMode" value="end">
                                    <label class="btn btn-outline-danger" for="editEndMode">
                                        <i class="fas fa-stop me-1"></i>End
                                    </label>
                                </div>
                                
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearEditRoute()">
                                    <i class="fas fa-eraser me-1"></i>Clear Route
                                </button>
                            </div>
                        </div>

                        <!-- Map Container -->
                        <div class="map-container">
                            <div class="map-instructions">
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>Instructions:</strong> Select a point type above, then click on the map to place markers. 
                                The system will automatically get the address for each point you select.
                            </div>
                            <div id="editRouteMap" class="route-map"></div>
                        </div>
                    </section>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update Truck
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editTruckModal = document.getElementById('editTruckModal');
    if (editTruckModal) {
        editTruckModal.addEventListener('shown.bs.modal', function() {
            console.log('🔧 Edit truck modal shown');
            
            // Initialize map first
            if (!window.editRouteMapSelector) {
                window.editRouteMapSelector = new RouteMapSelector('editRouteMap');
                console.log('✅ Edit RouteMapSelector initialized');
            }
            
            // Refresh map size
            setTimeout(() => {
                if (window.editRouteMapSelector) {
                    window.editRouteMapSelector.refreshMapSize();
                }
            }, 150);
        });

        // Clear form when modal is hidden
        editTruckModal.addEventListener('hidden.bs.modal', function() {
            console.log('🔧 Edit truck modal hidden');
            if (window.editRouteMapSelector) {
                window.editRouteMapSelector.clearAllMarkers();
            }
        });
        }
});
</script>