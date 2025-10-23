<div class="modal fade" id="editTruckModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Truck</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/admin/operations/garbage_collection/update">
                <div class="modal-body">
                    <input type="hidden" id="edit-truck-id" name="id">
                    
                    <h6 class="mb-3">Truck Details</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit-plate-number" class="form-label">Plate Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-plate-number" name="plate_number" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit-body-number" class="form-label">Body Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-body-number" name="body_number" required>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3">Assignment</h6>

                    <div class="mb-3">
                        <label for="edit-assigned-foreman" class="form-label">Assigned Foreman <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit-assigned-foreman" name="foreman_id" required>
                            <option value="">Select Foreman</option>
                            <?php if (!empty($foremen)): ?>
                                <?php foreach ($foremen as $foreman): ?>
                                    <option value="<?= htmlspecialchars($foreman['id']) ?>">
                                        <?= htmlspecialchars($foreman['username']) ?> - <?= htmlspecialchars($foreman['role']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit-schedule-type" class="form-label">Schedule Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit-schedule-type" name="schedule_type" required>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                        </select>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3">Route Configuration</h6>
                    
                    <input type="hidden" id="edit-route-id" name="route_id">
                    
                    <div class="mb-3">
                        <label for="edit-route-name" class="form-label">Route Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit-route-name" name="route_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit-start-point" class="form-label">Start Point <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit-start-point" name="start_point" required readonly>
                        <div id="edit-start-suggestions" class="autocomplete-suggestions"></div>
                        <!-- Add hidden fields for coordinates -->
                        <input type="hidden" id="edit-start-lat" name="start_lat">
                        <input type="hidden" id="edit-start-lon" name="start_lon">
                    </div>

                    <div class="mb-3">
                        <label for="edit-mid-point" class="form-label">Mid Point (Optional)</label>
                        <input type="text" class="form-control" id="edit-mid-point" name="mid_point" readonly>
                        <div id="edit-mid-suggestions" class="autocomplete-suggestions"></div>
                        <!-- Add hidden fields for coordinates -->
                        <input type="hidden" id="edit-mid-lat" name="mid_lat">
                        <input type="hidden" id="edit-mid-lon" name="mid_lon">
                    </div>

                    <div class="mb-3">
                        <label for="edit-end-point" class="form-label">End Point <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit-end-point" name="end_point" required readonly>
                        <div id="edit-end-suggestions" class="autocomplete-suggestions"></div>
                        <!-- Add hidden fields for coordinates -->
                        <input type="hidden" id="edit-end-lat" name="end_lat">
                        <input type="hidden" id="edit-end-lon" name="end_lon">
                    </div>

                    <div class="mb-3 text-end">
                        <button type="button" class="btn btn-secondary reset-route-btn">
                            <i class="fas fa-redo"></i> Reset Route Points
                        </button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Route Map</label>
                        <div id="edit-route-map" style="height: 400px; border-radius: 8px;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Truck
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>