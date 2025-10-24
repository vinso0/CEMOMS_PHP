<?php
// views/admin/operations/garbage_collection/index.view.php

$title = 'Garbage Collection - Operations';
$pageTitle = 'Garbage Collection';

ob_start();
?>

<div class="gc-management">
    <!-- Header Section -->
    <div class="gc-header">
        <div class="header-content">
            <div class="header-left">
                <h1 class="page-title">
                    <i class="fas fa-truck"></i>
                    Garbage Collection Management
                </h1>
                <p class="page-subtitle">Manage trucks, routes, and assignments</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addTruckModal">
                    <i class="fas fa-plus me-2"></i>Add New Truck
                </button>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['errors'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-truck"></i>
            </div>
            <div class="stat-content">
                <h3><?= count($trucks ?? []) ?></h3>
                <p>Total Trucks</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-play-circle"></i>
            </div>
            <div class="stat-content">
                <h3><?= count(array_filter($trucks ?? [], fn($t) => ($t['status'] ?? '') === 'Dispatched')) ?></h3>
                <p>Active Trucks</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-warning">
                <i class="fas fa-pause-circle"></i>
            </div>
            <div class="stat-content">
                <h3><?= count(array_filter($trucks ?? [], fn($t) => ($t['status'] ?? '') === 'Parked')) ?></h3>
                <p>Parked Trucks</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-info">
                <i class="fas fa-route"></i>
            </div>
            <div class="stat-content">
                <h3><?= count(array_unique(array_column($trucks ?? [], 'route_name'))) ?></h3>
                <p>Active Routes</p>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="filters-section">
        <div class="filters-header">
            <h3><i class="fas fa-filter me-2"></i>Filters & Search</h3>
            <button class="btn btn-sm btn-outline-secondary" onclick="resetFilters()">
                <i class="fas fa-redo me-1"></i>Reset
            </button>
        </div>
        <div class="filters-content">
            <div class="filter-grid">
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
                    <div class="search-wrapper">
                        <input type="text" id="filter-search" class="form-control" placeholder="Search trucks, routes...">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                </div>
                
                <div class="filter-actions">
                    <button class="btn btn-primary" onclick="applyFilters()">
                        <i class="fas fa-search me-1"></i>Apply
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Trucks Table -->
    <div class="table-section">
        <div class="table-header">
            <h3><i class="fas fa-list me-2"></i>Trucks & Assignments</h3>
            <div class="table-actions">
                <div class="view-toggle">
                    <button class="btn btn-sm btn-outline-secondary active" data-view="table">
                        <i class="fas fa-table"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" data-view="grid">
                        <i class="fas fa-th-large"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="table-wrapper">
            <table class="trucks-table" id="trucksTable">
                <thead>
                    <tr>
                        <th>Truck Info</th>
                        <th>Route Assignment</th>
                        <th>Foreman</th>
                        <th>Schedule</th>
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
                                
                                <!-- Truck Info -->
                                <td>
                                    <div class="truck-info">
                                        <div class="truck-primary">
                                            <strong class="plate-number"><?= htmlspecialchars($truck['plate_number']) ?></strong>
                                            <span class="body-number"><?= htmlspecialchars($truck['body_number']) ?></span>
                                        </div>
                                        <div class="truck-meta">
                                            <small class="text-muted">ID: <?= $truck['id'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Route Assignment -->
                                <td>
                                    <?php if (!empty($truck['route_name'])): ?>
                                        <div class="route-info">
                                            <div class="route-name">
                                                <strong><?= htmlspecialchars($truck['route_name']) ?></strong>
                                            </div>
                                            <div class="route-path">
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt text-success"></i>
                                                    <?= htmlspecialchars($truck['start_point'] ?? 'N/A') ?>
                                                    <?php if (!empty($truck['mid_point'])): ?>
                                                        <i class="fas fa-arrow-right mx-1"></i>
                                                        <i class="fas fa-map-marker-alt text-warning"></i>
                                                        <?= htmlspecialchars($truck['mid_point']) ?>
                                                    <?php endif; ?>
                                                    <i class="fas fa-arrow-right mx-1"></i>
                                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                                    <?= htmlspecialchars($truck['end_point'] ?? 'N/A') ?>
                                                </small>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            No Route Assigned
                                        </span>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- Foreman -->
                                <td>
                                    <div class="foreman-info">
                                        <?php if (!empty($truck['foreman_name'])): ?>
                                            <i class="fas fa-user me-1"></i>
                                            <?= htmlspecialchars($truck['foreman_name']) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Not Assigned</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                
                                <!-- Schedule -->
                                <td>
                                    <?php if (!empty($truck['schedule'])): ?>
                                        <span class="badge bg-info">
                                            <i class="fas fa-clock me-1"></i>
                                            <?= ucfirst(htmlspecialchars($truck['schedule'])) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- Status -->
                                <td>
                                    <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $truck['status'] ?? 'parked')) ?>">
                                        <i class="fas fa-circle me-1"></i>
                                        <?= ucfirst(htmlspecialchars($truck['status'] ?? 'Parked')) ?>
                                    </span>
                                </td>
                                
                                <!-- Actions -->
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-info" title="View Details"
                                                onclick="viewTruckDetails(<?= htmlspecialchars(json_encode($truck)) ?>)">
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
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Trucks Found</h5>
                                    <p class="text-muted">Click "Add New Truck" to register your first garbage collection truck.</p>
                                    <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addTruckModal">
                                        <i class="fas fa-plus me-2"></i>Add New Truck
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="activity-section">
        <div class="activity-header">
            <h3><i class="fas fa-history me-2"></i>Recent Dispatch & Return Logs</h3>
            <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="activity-content">
            <?php if (!empty($dispatch_logs)): ?>
                <div class="activity-list">
                    <?php foreach (array_slice($dispatch_logs, 0, 5) as $log): ?>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">
                                    <?= htmlspecialchars($log['plate_number']) ?> - <?= htmlspecialchars($log['route_name']) ?>
                                </div>
                                <div class="activity-meta">
                                    <span class="text-muted">
                                        <?= htmlspecialchars($log['foreman_name']) ?> • 
                                        <?= htmlspecialchars($log['dispatch_time']) ?>
                                        <?php if (!empty($log['return_time'])): ?>
                                            → <?= htmlspecialchars($log['return_time']) ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="activity-status">
                                <span class="status-badge status-<?= strtolower($log['status']) ?>">
                                    <?= ucfirst(htmlspecialchars($log['status'])) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-activity">
                    <i class="fas fa-clipboard-list fa-2x text-muted mb-2"></i>
                    <p class="text-muted">No recent activity</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Truck Details Modal -->
<div class="modal fade" id="truckDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-truck me-2"></i>
                    Truck Details & Route Map
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="truckDetailsContent">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

<?php 
require 'modals/add-truck.modal.php';
require 'modals/edit-truck.modal.php';
require 'modals/delete-truck.modal.php';
?>

<?php
$content = ob_get_clean();

// CSS Files
$additionalStyles = '
    <link rel="stylesheet" href="/public/assets/css/modal.css">
    <link rel="stylesheet" href="/public/assets/css/garbage_collection.css">
';

// JavaScript Files
$additionalScripts = '
    <script src="/public/assets/js/components/RouteMapSelector.js"></script>
    <script src="/public/assets/js/garbage-collection.js"></script>
';

require base_path('views/layout.php');
?>
