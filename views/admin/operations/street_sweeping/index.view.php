<?php
// views/admin/operations/street_sweeping/index.view.php

$title = 'Street Sweeping - Operations';
$pageTitle = 'Street Sweeping';

ob_start();
?>

<div class="section-header">
    <h2 class="section-title">Street Sweeping Management</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSweeperModal">
        <i class="fas fa-broom"></i> Add New Sweeper
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
                <label for="filter-schedule">Schedule Type</label>
                <select id="filter-schedule" class="form-select">
                    <option value="">All Schedules</option>
                    <option value="Daily">Daily</option>
                    <option value="Weekly">Weekly</option>
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

<!-- Sweepers Table -->
<div class="trucks-table-container">
    <div class="table-header">
        <h3><i class="fas fa-broom"></i> Sweepers & Assignments</h3>
    </div>
    <div class="table-wrapper">
        <table class="trucks-table">
            <thead>
                <tr>
                    <th>Foreman</th>
                    <th>Route Name</th>
                    <th>Schedule Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="sweepers-tbody">
                <?php if (!empty($sweepers)): ?>
                    <?php foreach ($sweepers as $sweeper): ?>
                        <tr data-sweeper-id="<?= htmlspecialchars((string)($sweeper['id'] ?? $sweeper['sweeper_id'] ?? '')) ?>"
                            data-foreman-id="<?= htmlspecialchars($sweeper['foreman_id'] ?? '') ?>"
                            data-status="<?= htmlspecialchars($sweeper['status'] ?? 'Parked') ?>"
                            data-route-id="<?= htmlspecialchars($sweeper['route_id'] ?? '') ?>">
                            
                            <td><strong><?= htmlspecialchars($sweeper['foreman_name'] ?? 'N/A') ?></strong></td>
                            
                            <td>
                                <?php if (!empty($sweeper['route_name'])): ?>
                                    <strong><?= htmlspecialchars($sweeper['route_name']) ?></strong><br>
                                <?php else: ?>
                                    <span class="text-muted">Not Assigned</span>
                                <?php endif; ?>
                            </td>
                            
                            <td><?= htmlspecialchars($sweeper['schedule_type'] ?? 'N/A') ?></td>
                            
                            <td>
                                <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $sweeper['status'] ?? 'parked')) ?>">
                                    <?= ucfirst(htmlspecialchars($sweeper['status'] ?? 'Parked')) ?>
                                </span>
                            </td>
                            
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-success route-details-btn" title="View"
                                            data-bs-toggle="modal"
                                            data-bs-target="#routeDetailsModal"
                                            data-sweeper-data="<?= htmlspecialchars(json_encode($sweeper)) ?>"
                                            data-truck-data="<?= htmlspecialchars(json_encode($sweeper)) ?>">
                                        <i class="fa-solid fa-expand"></i>
                                    </button>
                                    
                                    <button type="button" 
                                            class="btn btn-sm btn-warning edit-sweeper-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editSweeperModal"
                                            data-sweeper-id="<?= htmlspecialchars((string)($sweeper['id'] ?? $sweeper['sweeper_id'] ?? '')) ?>"
                                            data-route-name="<?= htmlspecialchars((string)($sweeper['route_name'] ?? '')) ?>"
                                            data-route-id="<?= htmlspecialchars((string)($sweeper['route_id'] ?? '')) ?>"
                                            data-foreman-id="<?= htmlspecialchars((string)($sweeper['foreman_id'] ?? '')) ?>"
                                            data-foreman-name="<?= htmlspecialchars((string)($sweeper['foreman_name'] ?? '')) ?>"
                                            data-schedule="<?= htmlspecialchars((string)($sweeper['schedule_type'] ?? '')) ?>"
                                            data-schedule-id="<?= htmlspecialchars((string)($sweeper['schedule_id'] ?? '')) ?>"
                                            data-operation-time="<?= htmlspecialchars((string)($sweeper['operation_time'] ?? '')) ?>"
                                            data-weekly-days="<?= htmlspecialchars(json_encode($sweeper['weekly_days'] ?? [])) ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button class="btn btn-sm btn-danger" 
                                            title="Delete"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteSweeperModal"
                                            onclick="document.getElementById('delete-sweeper-id').value='<?= htmlspecialchars($sweeper['schedule_id']) ?>'; document.getElementById('delete-sweeper-equipment').textContent='<?= htmlspecialchars($sweeper['operation_name']) ?>';">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">
                            <div class="empty-state">
                                <i class="fas fa-broom"></i>
                                <h5>No Sweepers Found</h5>
                                <p>Click "Add New Sweeper" to register a street sweeping equipment.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<?php 
require 'modals/add-sweeper.modal.php';
require 'modals/edit-sweeper.modal.php';
require 'modals/delete-sweeper.modal.php';
require 'modals/route-details.modal.php';
?>

<?php
$content = ob_get_clean();

// CSS Files
$additionalStyles = '
    <link rel="stylesheet" href="/assets/css/modal.css">
    <link rel="stylesheet" href="/assets/css/street_sweeping.css">
';

// JavaScript Files
$additionalScripts = '
    <script src="/assets/js/components/RouteMapSelector.js"></script>
    <script src="/assets/js/components/Filters.js"></script>
    <script src="/assets/js/components/AddSweeperView.js"></script>
    <script src="/assets/js/components/EditSweeperView.js"></script>
    <script src="/assets/js/components/RouteDetailsView.js"></script>
    <script type="text/javascript" src="/assets/js/boot-street-sweeping.js"></script>
    <script src="/assets/js/street-sweeping.js"></script>
';
require base_path('views/layout.php');
?>

