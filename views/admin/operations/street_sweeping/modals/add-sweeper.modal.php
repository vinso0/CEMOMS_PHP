<div class="modal fade" id="addSweeperModal" tabindex="-1" aria-labelledby="addSweeperModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSweeperModalLabel">Add New Sweeper</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form method="POST" action="/admin/operations/street_sweeping/store" id="addSweeperForm" novalidate>
                <div class="modal-body">

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
                                <select class="form-select" id="scheduleType" name="schedule_type" required>
                                    <option value="">Select Schedule</option>
                                    <option value="Daily">Daily</option>
                                </select>
                                <div class="invalid-feedback"></div>
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
                                    What time should this street sweeping operation start?
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
                                placeholder="e.g., Downtown Sweeping Route" 
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
                            <div id="addSweeperRouteMap" class="route-map"></div>
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
                    <button type="submit" class="btn btn-primary" id="submitSweeperBtn">
                        <i class="fas fa-save me-1"></i>Add Sweeper
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
