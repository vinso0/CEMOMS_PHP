<?php
// views/admin/operations/garbage_collection/index.view.php

$title = 'Garbage Collection - Operations';
$pageTitle = 'Garbage Collection';

ob_start();
?>

<div class="section-header">
    <h2 class="section-title">Garbage Collection Management</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTruckModal">
        <i class="fas fa-truck"></i> Add New Truck
    </button>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['errors'])): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php unset($_SESSION['errors']); ?>
<?php endif; ?>

<!-- Filters Section -->
<div class="filters-container">
    <div class="filters-header">
        <h3><i class="fas fa-filter"></i> Filters & Search</h3>
    </div>
    <div class="filters-content">
        <div class="filter-row">
            <div class="filter-item">
                <label for="filter-truck">Truck</label>
                <select id="filter-truck" class="form-select">
                    <option value="">All Trucks</option>
                    <?php if (!empty($trucks)): ?>
                        <?php foreach ($trucks as $truck): ?>
                            <option value="<?= htmlspecialchars($truck['id']) ?>">
                                <?= htmlspecialchars($truck['plate_number']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="filter-item">
                <label for="filter-foreman">Foreman</label>
                <select id="filter-foreman" class="form-select">
                    <option value="">All Foremen</option>
                    <?php if (!empty($foremen)): ?>
                        <?php foreach ($foremen as $foreman): ?>
                            <option value="<?= htmlspecialchars($foreman['id']) ?>">
                                <?= htmlspecialchars($foreman['username']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="filter-item">
                <label for="filter-status">Status</label>
                <select id="filter-status" class="form-select">
                    <option value="">All Status</option>
                    <option value="Dispatched">Dispatched</option>
                    <option value="Parked">Parked</option>
                </select>
            </div>
            
            <div class="filter-item">
                <label for="filter-search">Search</label>
                <input type="text" id="filter-search" class="form-control" placeholder="Search...">
            </div>
            
            <div class="filter-actions">
                <button class="btn btn-primary" onclick="applyFilters()">
                    <i class="fas fa-search"></i> Apply
                </button>
                <button class="btn btn-secondary" onclick="resetFilters()">
                    <i class="fas fa-redo"></i> Reset
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Trucks Table -->
<div class="trucks-table-container">
    <div class="table-header">
        <h3><i class="fas fa-truck"></i> Trucks & Assignments</h3>
    </div>
    <div class="table-wrapper">
        <table class="trucks-table">
            <thead>
                <tr>
                    <th>Plate Number</th>
                    <th>Body Number</th>
                    <th>Route</th>
                    <th>Foreman</th>
                    <th>Schedule Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="trucks-tbody">
                <?php if (!empty($trucks)): ?>
                    <?php foreach ($trucks as $truck): ?>
                        <tr data-truck-id="<?= $truck['id'] ?>" 
                            data-foreman-id="<?= $truck['foreman_id'] ?? '' ?>"
                            data-status="<?= htmlspecialchars($truck['status'] ?? 'Parked') ?>">
                            <td><strong><?= htmlspecialchars($truck['plate_number']) ?></strong></td>
                            <td><?= htmlspecialchars($truck['body_number']) ?></td>
                            <td>
                                <?php if (!empty($truck['route_name'])): ?>
                                    <strong><?= htmlspecialchars($truck['route_name']) ?></strong><br>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($truck['start_point']) ?>
                                        <?php if (!empty($truck['mid_point'])): ?>
                                            → <?= htmlspecialchars($truck['mid_point']) ?>
                                        <?php endif; ?>
                                        → <?= htmlspecialchars($truck['end_point']) ?>
                                    </small>
                                <?php else: ?>
                                    <span class="text-muted">Not Assigned</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($truck['foreman_name'] ?? 'Not Assigned') ?></td>
                            <td><?= htmlspecialchars($truck['schedule'] ?? 'N/A') ?></td>
                            <td>
                                <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $truck['status'] ?? 'parked')) ?>">
                                    <?= ucfirst(htmlspecialchars($truck['status'] ?? 'Parked')) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-warning" title="Edit" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editTruckModal"
                                            onclick="populateEditModal(<?= htmlspecialchars(json_encode($truck)) ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-success" title="Dispatch Log"
                                            data-bs-toggle="modal"
                                            data-bs-target="#dispatchModal"
                                            onclick="populateDispatchModal(<?= htmlspecialchars(json_encode($truck)) ?>)">
                                        <i class="fas fa-clipboard-check"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" title="Delete"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteTruckModal"
                                            onclick="populateDeleteModal(<?= htmlspecialchars(json_encode($truck)) ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="empty-state">
                                <i class="fas fa-truck"></i>
                                <h5>No Trucks Found</h5>
                                <p>Click "Add New Truck" to register a garbage collection truck.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Dispatch & Return Logs -->
<div class="logs-container">
    <div class="logs-header">
        <h3><i class="fas fa-history"></i> Recent Dispatch & Return Logs</h3>
    </div>
    <div class="logs-wrapper">
        <table class="logs-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Truck</th>
                    <th>Route</th>
                    <th>Foreman</th>
                    <th>Dispatch Time</th>
                    <th>Return Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($dispatch_logs)): ?>
                    <?php foreach ($dispatch_logs as $log): ?>
                        <tr>
                            <td><?= htmlspecialchars($log['date']) ?></td>
                            <td><?= htmlspecialchars($log['plate_number']) ?></td>
                            <td><?= htmlspecialchars($log['route_name']) ?></td>
                            <td><?= htmlspecialchars($log['foreman_name']) ?></td>
                            <td><?= htmlspecialchars($log['dispatch_time']) ?></td>
                            <td><?= htmlspecialchars($log['return_time'] ?? 'Not Returned') ?></td>
                            <td>
                                <span class="status-badge status-<?= strtolower($log['status']) ?>">
                                    <?= ucfirst(htmlspecialchars($log['status'])) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="empty-state">
                                <i class="fas fa-clipboard-list"></i>
                                <h5>No Logs Found</h5>
                                <p>Dispatch and return logs will appear here.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Truck Modal -->
<div class="modal fade" id="addTruckModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Truck</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/admin/operations/garbage_collection/store">
                <div class="modal-body">
                    <h6 class="mb-3">Truck Details</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="plate-number" class="form-label">Plate Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="plate-number" name="plate_number" placeholder="e.g., ABC 1234" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="body-number" class="form-label">Body Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="body-number" name="body_number" placeholder="e.g., SWM-309" required>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3">Assignment</h6>
                    
                    <div class="mb-3">
                        <label for="assigned-foreman" class="form-label">Assigned Foreman <span class="text-danger">*</span></label>
                        <select class="form-select" id="assigned-foreman" name="foreman_id" required>
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
                        <label for="schedule-type" class="form-label">Schedule Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="schedule-type" name="schedule_type" required>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                        </select>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3">Route Configuration</h6>
                    
                    <div class="mb-3">
                        <label for="route-name" class="form-label">Route Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="route-name" name="route_name" placeholder="e.g., Downtown Collection Route" required>
                    </div>

                    <div class="mb-3">
                        <label for="start-point" class="form-label">Start Point <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="start-point" name="start_point" required readonly>
                        <div id="start-suggestions" class="autocomplete-suggestions"></div>
                        <input type="hidden" id="start-lat" name="start_lat">
                        <input type="hidden" id="start-lon" name="start_lon">
                    </div>

                    <div class="mb-3">
                        <label for="mid-point" class="form-label">Mid Point (Optional)</label>
                        <input type="text" class="form-control" id="mid-point" name="mid_point" readonly>
                        <div id="mid-suggestions" class="autocomplete-suggestions"></div>
                        <input type="hidden" id="mid-lat" name="mid_lat">
                        <input type="hidden" id="mid-lon" name="mid_lon">
                    </div>

                    <div class="mb-3">
                        <label for="end-point" class="form-label">End Point <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="end-point" name="end_point" required readonly>
                        <div id="end-suggestions" class="autocomplete-suggestions"></div>
                        <input type="hidden" id="end-lat" name="end_lat">
                        <input type="hidden" id="end-lon" name="end_lon">
                    </div>

                    <div class="mb-3 text-end">
                        <button type="button" class="btn btn-secondary reset-route-btn">
                            <i class="fas fa-redo"></i> Reset Route Points
                        </button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Route Map</label>
                        <div id="add-route-map" style="height: 400px; border-radius: 8px;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Add Truck
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Truck Modal -->
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

<!-- Delete Truck Modal -->
<div class="modal fade" id="deleteTruckModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="fas fa-trash-alt text-danger" style="font-size: 3rem;"></i>
                </div>
                <h5 class="mb-3">Delete Truck?</h5>
                <p class="text-muted mb-3">
                    Are you sure you want to delete truck <strong id="delete-truck-plate"></strong>? 
                    This action cannot be undone.
                </p>
                <form method="POST" action="/admin/operations/garbage_collection/delete">
                    <input type="hidden" id="delete-truck-id" name="id">
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Dispatch Modal -->
<div class="modal fade" id="dispatchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dispatch & Return Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/admin/operations/garbage_collection/dispatch">
                <div class="modal-body">
                    <input type="hidden" id="dispatch-truck-id" name="truck_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Truck</label>
                        <input type="text" class="form-control" id="dispatch-truck-plate" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="dispatch-date" class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="dispatch-date" name="date" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="dispatch-time" class="form-label">Dispatch Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="dispatch-time" name="dispatch_time" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="return-time" class="form-label">Return Time</label>
                            <input type="time" class="form-control" id="return-time" name="return_time">
                            <small class="form-text text-muted">Leave blank if not yet returned</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="log-notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="log-notes" name="notes" rows="3" placeholder="Any observations or issues..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Save Log
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.autocomplete-suggestions {
    position: absolute;
    z-index: 1000;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    max-height: 200px;
    overflow-y: auto;
    width: calc(100% - 30px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.autocomplete-suggestion {
    padding: 10px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
}

.autocomplete-suggestion:hover {
    background-color: #f8f9fa;
}

.autocomplete-suggestion:last-child {
    border-bottom: none;
}

.status-parked {
    background: #d4edda;
    color: #155724;
}

.status-dispatched {
    background: #cce5ff;
    color: #004085;
}

/* Add these styles to your existing <style> block */
.modal {
    z-index: 1050;
}

.modal-backdrop {
    z-index: 1040;
}

.leaflet-container {
    z-index: 1;
}

#add-route-map,
#edit-route-map {
    position: relative;
    z-index: 1;
    height: 400px;
    border-radius: 8px;
    margin-bottom: 15px;
}

/* Fix for map controls being under modal */
.leaflet-top,
.leaflet-bottom {
    z-index: 1000 !important;
}

/* Additional styles for modal and map responsiveness */
.modal-lg {
    max-width: 900px;
}

.modal-body {
    max-height: calc(100vh - 210px);
    overflow-y: auto;
}

.reset-route-btn {
    margin-top: 10px;
}

.reset-route-btn:hover {
    background-color: #6c757d;
    border-color: #6c757d;
}
</style>

<script>
//Nominatim geocoding for address autocomplete
let debounceTimer;
let currentFocus = -1;

function setupAutocomplete(inputId, suggestionsId, latId, lonId) {
    const input = document.getElementById(inputId);
    const suggestionsContainer = document.getElementById(suggestionsId);
    const latInput = document.getElementById(latId);
    const lonInput = document.getElementById(lonId);

    if (!input || !suggestionsContainer) return;

    input.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();

        if (query.length < 2) {
            suggestionsContainer.innerHTML = '';
            suggestionsContainer.style.display = 'none';
            return;
        }

        // Show loading indicator
        suggestionsContainer.innerHTML = '<div class="autocomplete-suggestion loading">Searching...</div>';
        suggestionsContainer.style.display = 'block';

        debounceTimer = setTimeout(() => {
            fetch(`/api/geocode?q=${encodeURIComponent(query)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    suggestionsContainer.innerHTML = '';
                    currentFocus = -1;
                    
                    if (!data || data.length === 0) {
                        suggestionsContainer.innerHTML = '<div class="autocomplete-suggestion no-results">No locations found. Try being more specific.</div>';
                        return;
                    }

                    data.forEach((place, index) => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-suggestion';
                        div.setAttribute('data-index', index);
                        
                        // Create a structured display
                        const displayText = place.display_name;
                        div.innerHTML = `
                            <i class="fas fa-map-marker-alt"></i>
                            <span class="suggestion-text">${displayText}</span>
                        `;
                        
                        div.addEventListener('click', () => {
                            input.value = place.display_name;
                            // Store coordinates in hidden fields
                            if (latInput) latInput.value = place.lat;
                            if (lonInput) lonInput.value = place.lon;
                            suggestionsContainer.innerHTML = '';
                            suggestionsContainer.style.display = 'none';
                        });
                        
                        suggestionsContainer.appendChild(div);
                    });
                    
                    suggestionsContainer.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error fetching suggestions:', error);
                    suggestionsContainer.innerHTML = '<div class="autocomplete-suggestion error">Error fetching results. Please try again.</div>';
                });
        }, 400); // Increased debounce time for better API usage
    });

    // Keyboard navigation
    input.addEventListener('keydown', function(e) {
        const suggestions = suggestionsContainer.getElementsByClassName('autocomplete-suggestion');
        
        if (e.keyCode === 40) { // Down arrow
            e.preventDefault();
            currentFocus++;
            addActive(suggestions);
        } else if (e.keyCode === 38) { // Up arrow
            e.preventDefault();
            currentFocus--;
            addActive(suggestions);
        } else if (e.keyCode === 13) { // Enter
            e.preventDefault();
            if (currentFocus > -1 && suggestions[currentFocus]) {
                suggestions[currentFocus].click();
            }
        } else if (e.keyCode === 27) { // Escape
            suggestionsContainer.innerHTML = '';
            suggestionsContainer.style.display = 'none';
        }
    });

    function addActive(suggestions) {
        if (!suggestions || suggestions.length === 0) return;
        
        removeActive(suggestions);
        
        if (currentFocus >= suggestions.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = suggestions.length - 1;
        
        suggestions[currentFocus].classList.add('active');
        suggestions[currentFocus].scrollIntoView({ block: 'nearest' });
    }

    function removeActive(suggestions) {
        for (let i = 0; i < suggestions.length; i++) {
            suggestions[i].classList.remove('active');
        }
    }

    // Close suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !suggestionsContainer.contains(e.target)) {
            suggestionsContainer.innerHTML = '';
            suggestionsContainer.style.display = 'none';
        }
    });
}

// Initialize autocomplete for all route inputs when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Add modal autocomplete
    setupAutocomplete('start-point', 'start-suggestions', 'start-lat', 'start-lon');
    setupAutocomplete('mid-point', 'mid-suggestions', 'mid-lat', 'mid-lon');
    setupAutocomplete('end-point', 'end-suggestions', 'end-lat', 'end-lon');
    
    // Edit modal autocomplete
    setupAutocomplete('edit-start-point', 'edit-start-suggestions', 'edit-start-lat', 'edit-start-lon');
    setupAutocomplete('edit-mid-point', 'edit-mid-suggestions', 'edit-mid-lat', 'edit-mid-lon');
    setupAutocomplete('edit-end-point', 'edit-end-suggestions', 'edit-end-lat', 'edit-end-lon');
    
    // Real-time search for trucks table
    const searchInput = document.getElementById('filter-search');
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }

    // Add modal map initialization
    const addModal = document.getElementById('addTruckModal');
    addModal.addEventListener('shown.bs.modal', function() {
        setTimeout(() => {
            if (!addRouteMap) {
                addRouteMap = new RouteMapSelector('add-route-map', {
                    modalId: 'add'
                });
                addRouteMap.invalidateSize();
                
                // Add reset button handler
                const resetBtn = addModal.querySelector('.reset-route-btn');
                if (resetBtn) {
                    resetBtn.addEventListener('click', () => addRouteMap.clearAllMarkers());
                }
            }
        }, 200);
    });

    // Edit modal map initialization
    const editModal = document.getElementById('editTruckModal');
    editModal.addEventListener('shown.bs.modal', function() {
        setTimeout(() => {
            if (!editRouteMap) {
                editRouteMap = new RouteMapSelector('edit-route-map', {
                    modalId: 'edit'
                });
                editRouteMap.invalidateSize();
                
                // Add reset button handler
                const resetBtn = editModal.querySelector('.reset-route-btn');
                if (resetBtn) {
                    resetBtn.addEventListener('click', () => editRouteMap.clearAllMarkers());
                }
            }
        }, 200);
    });

    // Cleanup on modal hide
    addModal.addEventListener('hide.bs.modal', function() {
        if (addRouteMap) {
            addRouteMap.destroy();
            addRouteMap = null;
        }
    });

    editModal.addEventListener('hide.bs.modal', function() {
        if (editRouteMap) {
            editRouteMap.destroy();
            editRouteMap = null;
        }
    });
});

// Enhanced styling for autocomplete
const style = document.createElement('style');
style.textContent = `
.autocomplete-suggestions {
    position: absolute;
    z-index: 1000;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    max-height: 250px;
    overflow-y: auto;
    width: calc(100% - 30px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    border-radius: 0 0 8px 8px;
    display: none;
}

.autocomplete-suggestion {
    padding: 12px 15px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    transition: background-color 0.2s;
}

.autocomplete-suggestion i {
    color: #4CAF50;
    margin-top: 2px;
    flex-shrink: 0;
}

.autocomplete-suggestion .suggestion-text {
    flex: 1;
    font-size: 0.9rem;
    line-height: 1.4;
}

.autocomplete-suggestion:hover,
.autocomplete-suggestion.active {
    background-color: #f8f9fa;
}

.autocomplete-suggestion:last-child {
    border-bottom: none;
}

.autocomplete-suggestion.loading,
.autocomplete-suggestion.no-results,
.autocomplete-suggestion.error {
    justify-content: center;
    color: #666;
    font-style: italic;
    cursor: default;
}

.autocomplete-suggestion.loading {
    color: #2196F3;
}

.autocomplete-suggestion.error {
    color: #f44336;
}

/* Scrollbar styling for suggestions */
.autocomplete-suggestions::-webkit-scrollbar {
    width: 8px;
}

.autocomplete-suggestions::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.autocomplete-suggestions::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.autocomplete-suggestions::-webkit-scrollbar-thumb:hover {
    background: #555;
}
`;
document.head.appendChild(style);

function applyFilters() {
    const truck = document.getElementById('filter-truck').value;
    const foreman = document.getElementById('filter-foreman').value;
    const status = document.getElementById('filter-status').value;
    const search = document.getElementById('filter-search').value.toLowerCase();
    
    const rows = document.querySelectorAll('#trucks-tbody tr');
    
    rows.forEach(row => {
        const truckId = row.dataset.truckId;
        const foremanId = row.dataset.foremanId;
        const rowStatus = row.dataset.status;
        const rowText = row.textContent.toLowerCase();
        
        let showRow = true;
        
        if (truck && truckId !== truck) showRow = false;
        if (foreman && foremanId !== foreman) showRow = false;
        if (status && rowStatus !== status) showRow = false;
        if (search && !rowText.includes(search)) showRow = false;
        
        row.style.display = showRow ? '' : 'none';
    });
}

function resetFilters() {
    document.getElementById('filter-truck').value = '';
    document.getElementById('filter-foreman').value = '';
    document.getElementById('filter-status').value = '';
    document.getElementById('filter-search').value = '';
    
    const rows = document.querySelectorAll('#trucks-tbody tr');
    rows.forEach(row => {
        row.style.display = '';
    });
}

function populateEditModal(truck) {
    document.getElementById('edit-truck-id').value = truck.id;
    document.getElementById('edit-plate-number').value = truck.plate_number;
    document.getElementById('edit-body-number').value = truck.body_number;
    document.getElementById('edit-assigned-foreman').value = truck.foreman_id || '';
    document.getElementById('edit-schedule-type').value = truck.schedule || 'daily';
    document.getElementById('edit-route-id').value = truck.route_id || '';
    document.getElementById('edit-route-name').value = truck.route_name || '';
    document.getElementById('edit-start-point').value = truck.start_point || '';
    document.getElementById('edit-mid-point').value = truck.mid_point || '';
    document.getElementById('edit-end-point').value = truck.end_point || '';

    // Populate coordinates
    document.getElementById('edit-start-lat').value = truck.start_lat || '';
    document.getElementById('edit-start-lon').value = truck.start_lon || '';
    document.getElementById('edit-mid-lat').value = truck.mid_lat || '';
    document.getElementById('edit-mid-lon').value = truck.mid_lon || '';
    document.getElementById('edit-end-lat').value = truck.end_lat || '';
    document.getElementById('edit-end-lon').value = truck.end_lon || '';

    // Ensure map is initialized before loading route
    if (editRouteMap) {
        editRouteMap.loadExistingRoute(
            truck.start_lat, truck.start_lon,
            truck.mid_lat, truck.mid_lon,
            truck.end_lat, truck.end_lon
        );
    }
}

function populateDeleteModal(truck) {
    document.getElementById('delete-truck-id').value = truck.id;
    document.getElementById('delete-truck-plate').textContent = truck.plate_number + ' (' + truck.body_number + ')';
}

function populateDispatchModal(truck) {
    document.getElementById('dispatch-truck-id').value = truck.id;
    document.getElementById('dispatch-truck-plate').value = truck.plate_number + ' (' + truck.body_number + ')';
    document.getElementById('dispatch-date').value = new Date().toISOString().split('T')[0];
}

// At the end of your file, before requiring layout.php
<?php
$content = ob_get_clean();
$additionalStyles = '
    <link rel="stylesheet" href="/assets/css/modal.css">
    <link rel="stylesheet" href="/assets/css/garbage_collection.css">
';
$additionalScripts = '
    <script src="/assets/js/components/RouteMapSelector.js"></script>
    <script>
        let addRouteMap = null;
        let editRouteMap = null;

        // Initialize maps when modals are shown
        document.getElementById("addTruckModal").addEventListener("shown.bs.modal", function () {
            if (!addRouteMap) {
                addRouteMap = new RouteMapSelector("add-route-map", {
                    modalId: "add"
                });
            }
        });

        document.getElementById("editTruckModal").addEventListener("shown.bs.modal", function () {
            if (!editRouteMap) {
                editRouteMap = new RouteMapSelector("edit-route-map", {
                    modalId: "edit"
                });
            }

            // Load existing route points if available
            const startLat = document.getElementById("edit-start-lat").value;
            const startLon = document.getElementById("edit-start-lon").value;
            const midLat = document.getElementById("edit-mid-lat").value;
            const midLon = document.getElementById("edit-mid-lon").value;
            const endLat = document.getElementById("edit-end-lat").value;
            const endLon = document.getElementById("edit-end-lon").value;

            if (startLat && startLon) {
                editRouteMap.loadExistingRoute(
                    startLat, startLon,
                    midLat, midLon,
                    endLat, endLon
                );
            }
        });

        // Clean up maps when modals are hidden
        document.getElementById("addTruckModal").addEventListener("hidden.bs.modal", function () {
            if (addRouteMap) {
                addRouteMap.destroy();
                addRouteMap = null;
            }
        });

        document.getElementById("editTruckModal").addEventListener("hidden.bs.modal", function () {
            if (editRouteMap) {
                editRouteMap.destroy();
                editRouteMap = null;
            }
        });
    </script>
';

require base_path('views/layout.php');