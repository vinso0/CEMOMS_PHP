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
                    <th>Route Name</th>
                    <th>Foreman</th>
                    <th>Schedule Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="trucks-tbody">
                <?php if (!empty($trucks)): ?>
                    <?php foreach ($trucks as $truck): ?>
                        <tr data-truck-id="<?= htmlspecialchars($truck['truck_id'] ?? '') ?>" 
                            data-foreman-id="<?= htmlspecialchars($truck['foreman_id'] ?? '') ?>"
                            data-status="<?= htmlspecialchars($truck['status'] ?? 'Parked') ?>"
                            data-route-id="<?= htmlspecialchars($truck['route_id'] ?? '') ?>">
                            
                            <td><strong><?= htmlspecialchars($truck['plate_number'] ?? 'N/A') ?></strong></td>
                            <td><?= htmlspecialchars($truck['body_number'] ?? 'N/A') ?></td>
                            
                            <td>
                                <?php if (!empty($truck['route_name'])): ?>
                                    <strong><?= htmlspecialchars($truck['route_name']) ?></strong><br>
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
                                    <button class="btn btn-sm btn-success" title="View"
                                            data-bs-toggle="modal"
                                            data-bs-target="#routeDetailsModal"
                                            onclick="populateRouteDetailsModal(<?= htmlspecialchars(json_encode($truck)) ?>)">
                                        <i class="fa-solid fa-expand"></i>
                                    </button>
                                    
                                    <button type="button" 
                                            class="btn btn-sm btn-warning edit-truck-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editTruckModal"
                                            data-truck-id="<?= htmlspecialchars((string)($truck['id'] ?? '')) ?>"
                                            data-plate-number="<?= htmlspecialchars((string)($truck['plate_number'] ?? '')) ?>"
                                            data-body-number="<?= htmlspecialchars((string)($truck['body_number'] ?? '')) ?>"
                                            data-route-name="<?= htmlspecialchars((string)($truck['route_name'] ?? '')) ?>"
                                            data-route-id="<?= htmlspecialchars((string)($truck['route_id'] ?? '')) ?>"
                                            data-foreman-id="<?= htmlspecialchars((string)($truck['foreman_id'] ?? '')) ?>"
                                            data-foreman-name="<?= htmlspecialchars((string)($truck['foreman_name'] ?? '')) ?>"
                                            data-schedule="<?= htmlspecialchars((string)($truck['schedule'] ?? '')) ?>"
                                            data-schedule-id="<?= htmlspecialchars((string)($truck['schedule_id'] ?? '')) ?>"
                                            data-operation-time="<?= htmlspecialchars((string)($truck['operation_time'] ?? '')) ?>"
                                            data-weekly-days="<?= htmlspecialchars(json_encode($truck['weekly_days'] ?? [])) ?>">
                                        <i class="fas fa-edit"></i>
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

<?php 
require 'modals/add-truck.modal.php';
require 'modals/edit-truck.modal.php';
require 'modals/delete-truck.modal.php';
require 'modals/route-details.modal.php';
?>

<?php
$content = ob_get_clean();

// CSS Files
$additionalStyles = '
    <link rel="stylesheet" href="/assets/css/modal.css">
    <link rel="stylesheet" href="/assets/css/garbage_collection.css">
';

// JavaScript Files
$additionalScripts = '
    <script src="/assets/js/components/RouteMapSelector.js"></script>
    <script src="/assets/js/garbage-collection.js"></script>
    <script type="module" src="/public/assets/js/boot-garbage-collection.js"></script>
';

require base_path('views/layout.php');
?>