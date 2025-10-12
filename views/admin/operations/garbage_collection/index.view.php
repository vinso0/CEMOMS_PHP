<?php
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

<!-- Filters Section -->
<div class="filters-container">
    <div class="filters-header">
        <h3><i class="fas fa-filter"></i> Filters & Search</h3>
    </div>
    <div class="filters-content">
        <div class="filter-row">
            <div class="filter-item">
                <label for="filter-date">Date</label>
                <input type="date" id="filter-date" class="form-control">
            </div>
            
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
                <label for="filter-route">Route</label>
                <select id="filter-route" class="form-select">
                    <option value="">All Routes</option>
                    <?php if (!empty($routes)): ?>
                        <?php foreach ($routes as $route): ?>
                            <option value="<?= htmlspecialchars($route['id']) ?>">
                                <?= htmlspecialchars($route['route_name']) ?>
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
                    <th>Type</th>
                    <th>Personnel</th>
                    <th>Route Assigned</th>
                    <th>Foreman</th>
                    <th>Schedule</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($trucks)): ?>
                    <?php foreach ($trucks as $truck): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($truck['plate_number']) ?></strong></td>
                            <td><?= htmlspecialchars($truck['type']) ?></td>
                            <td><?= htmlspecialchars($truck['personnel']) ?></td>
                            <td><?= htmlspecialchars($truck['route_name'] ?? 'Not Assigned') ?></td>
                            <td><?= htmlspecialchars($truck['foreman_name'] ?? 'Not Assigned') ?></td>
                            <td><?= htmlspecialchars($truck['schedule']) ?></td>
                            <td>
                                <span class="status-badge status-<?= htmlspecialchars($truck['status']) ?>">
                                    <?= ucfirst(htmlspecialchars($truck['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-info" title="View Details" onclick="viewTruck(<?= $truck['id'] ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
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
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">
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
                                <span class="status-badge status-<?= htmlspecialchars($log['status']) ?>">
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
            <form method="POST" action="/admin/operations/collection/truck/store">
                <div class="modal-body">
                    <h6 class="mb-3">Truck Details</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="plate-number" class="form-label">Plate Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="plate-number" name="plate_number" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="truck-type" class="form-label">Truck Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="truck-type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="Compactor">Compactor</option>
                                <option value="Dump Truck">Dump Truck</option>
                                <option value="Roll-off">Roll-off</option>
                                <option value="Side Loader">Side Loader</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="personnel" class="form-label">Personnel (Driver & Crew) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="personnel" name="personnel" placeholder="e.g., Juan Dela Cruz (Driver), Pedro Santos (Crew)" required>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3">Route Assignment</h6>
                    <div class="mb-3">
                        <label for="route-start" class="form-label">Starting Point <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="route-start" name="route_start" placeholder="e.g., Bagong Barrio" required>
                    </div>

                    <div class="mb-3">
                        <label for="route-midpoints" class="form-label">Midpoints (Optional)</label>
                        <textarea class="form-control" id="route-midpoints" name="route_midpoints" rows="2" placeholder="e.g., Grace Park, Maypajo, Talipapa"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="route-endpoint" class="form-label">End Point <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="route-endpoint" name="route_endpoint" placeholder="e.g., Landfill/Transfer Station" required>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3">Assignment & Schedule</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="assigned-foreman" class="form-label">Assigned Foreman <span class="text-danger">*</span></label>
                            <select class="form-select" id="assigned-foreman" name="foreman_id" required>
                                <option value="">Select Foreman</option>
                                <?php if (!empty($foremen)): ?>
                                    <?php foreach ($foremen as $foreman): ?>
                                        <option value="<?= htmlspecialchars($foreman['id']) ?>">
                                            <?= htmlspecialchars($foreman['username']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="schedule-type" class="form-label">Schedule Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="schedule-type" name="schedule_type" required>
                                <option value="">Select Schedule</option>
                                <option value="Daily">Daily</option>
                                <option value="Mon-Wed-Fri">Monday, Wednesday, Friday</option>
                                <option value="Tue-Thu-Sat">Tuesday, Thursday, Saturday</option>
                                <option value="Custom">Custom Days</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="available">Available</option>
                            <option value="on-route">On Route</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="inactive">Inactive</option>
                        </select>
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
            <form method="POST" action="/admin/operations/collection/truck/update">
                <div class="modal-body">
                    <input type="hidden" id="edit-truck-id" name="id">
                    
                    <h6 class="mb-3">Truck Details</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit-plate-number" class="form-label">Plate Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit-plate-number" name="plate_number" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit-truck-type" class="form-label">Truck Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit-truck-type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="Compactor">Compactor</option>
                                <option value="Dump Truck">Dump Truck</option>
                                <option value="Roll-off">Roll-off</option>
                                <option value="Side Loader">Side Loader</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit-personnel" class="form-label">Personnel <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit-personnel" name="personnel" required>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3">Route Assignment</h6>
                    <div class="mb-3">
                        <label for="edit-route-start" class="form-label">Starting Point <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit-route-start" name="route_start" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit-route-midpoints" class="form-label">Midpoints</label>
                        <textarea class="form-control" id="edit-route-midpoints" name="route_midpoints" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit-route-endpoint" class="form-label">End Point <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit-route-endpoint" name="route_endpoint" required>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3">Assignment & Schedule</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit-assigned-foreman" class="form-label">Assigned Foreman <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit-assigned-foreman" name="foreman_id" required>
                                <option value="">Select Foreman</option>
                                <?php if (!empty($foremen)): ?>
                                    <?php foreach ($foremen as $foreman): ?>
                                        <option value="<?= htmlspecialchars($foreman['id']) ?>">
                                            <?= htmlspecialchars($foreman['username']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit-schedule-type" class="form-label">Schedule Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit-schedule-type" name="schedule_type" required>
                                <option value="">Select Schedule</option>
                                <option value="Daily">Daily</option>
                                <option value="Mon-Wed-Fri">Monday, Wednesday, Friday</option>
                                <option value="Tue-Thu-Sat">Tuesday, Thursday, Saturday</option>
                                <option value="Custom">Custom Days</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit-status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit-status" name="status" required>
                            <option value="available">Available</option>
                            <option value="on-route">On Route</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="inactive">Inactive</option>
                        </select>
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

<!-- Dispatch Modal -->
<div class="modal fade" id="dispatchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dispatch & Return Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/admin/operations/collection/dispatch/log">
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

<script>
function applyFilters() {
    const date = document.getElementById('filter-date').value;
    const truck = document.getElementById('filter-truck').value;
    const route = document.getElementById('filter-route').value;
    const foreman = document.getElementById('filter-foreman').value;
    const search = document.getElementById('filter-search').value;
    
    console.log('Applying filters:', { date, truck, route, foreman, search });
    // Implement filter logic here
}

function resetFilters() {
    document.getElementById('filter-date').value = '';
    document.getElementById('filter-truck').value = '';
    document.getElementById('filter-route').value = '';
    document.getElementById('filter-foreman').value = '';
    document.getElementById('filter-search').value = '';
}

function populateEditModal(truck) {
    document.getElementById('edit-truck-id').value = truck.id;
    document.getElementById('edit-plate-number').value = truck.plate_number;
    document.getElementById('edit-truck-type').value = truck.type;
    document.getElementById('edit-personnel').value = truck.personnel;
    document.getElementById('edit-route-start').value = truck.route_start || '';
    document.getElementById('edit-route-midpoints').value = truck.route_midpoints || '';
    document.getElementById('edit-route-endpoint').value = truck.route_endpoint || '';
    document.getElementById('edit-assigned-foreman').value = truck.foreman_id || '';
    document.getElementById('edit-schedule-type').value = truck.schedule || '';
    document.getElementById('edit-status').value = truck.status;
}

function populateDispatchModal(truck) {
    document.getElementById('dispatch-truck-id').value = truck.id;
    document.getElementById('dispatch-truck-plate').value = truck.plate_number;
    document.getElementById('dispatch-date').value = new Date().toISOString().split('T')[0];
}

function viewTruck(id) {
    console.log('Viewing truck:', id);
    // Implement view details
}
</script>

<?php
$content = ob_get_clean();
$additionalStyles = '<link rel="stylesheet" href="/assets/css/modal.css">';
$additionalStyles .= ' <link rel="stylesheet" href="/assets/css/garbage_collection.css">';
require base_path('views/layout.php');