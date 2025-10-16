<?php
$title = 'Cleanup Drives - Operations';
$pageTitle = 'Cleanup Drives';

ob_start();
?>
<div class="section-header">
    <h2 class="section-title">Cleanup Drives Management</h2>
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
                <input type="date" id="filter-date" class="form-control" placeholder="dd/mm/yyyy">
            </div>

            <div class="filter-item">
                <label for="filter-route">Route</label>
                <select id="filter-route" class="form-select">
                    <option value="">All Routes</option>
                    <?php if (!empty($routes)): ?>
                        <?php foreach ($routes as $route): ?>
                            <option value="<?= htmlspecialchars($route['id']) ?>"><?= htmlspecialchars($route['route_name']) ?></option>
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
                            <option value="<?= htmlspecialchars($foreman['id']) ?>"><?= htmlspecialchars($foreman['username']) ?></option>
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

<!-- Cleanup Drives Table -->
<div class="trucks-table-container">
    <div class="table-header d-flex" style="align-items:center; justify-content:space-between; gap:0.75rem;">
        <h3><i class="fas fa-leaf"></i> Cleanup Drives</h3>
        <form class="d-flex" method="GET" action="/admin/operations/cleanup" id="schedule-search-form" style="gap: 0.5rem; margin-left:auto;">
            <input type="search" name="q" id="schedule-search-input" class="form-control form-control-sm" placeholder="Search schedules..." aria-label="Search schedules" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            <button class="btn btn-primary btn-sm" type="submit">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
    </div>
    <div class="table-wrapper">
        <table class="trucks-table">
            <thead>
                <tr>
                    <th>Route / Area</th>
                    <th>Date &amp; Time</th>
                    <th>Foreman</th>
                    <th>Active Personnel</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($drives)): ?>
                    <?php foreach ($drives as $d): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($d['route_name'] ?? 'Unassigned') ?></strong></td>
                            <td><?= htmlspecialchars($d['schedule'] ?? '') ?></td>
                            <td><?= htmlspecialchars($d['foreman_name'] ?? 'Not Assigned') ?></td>
                            <td><?= htmlspecialchars($d['active_personnel'] ?? '') ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-info" title="View Details" onclick="viewDrive(<?= htmlspecialchars($d['id'] ?? 0) ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">
                            <div class="empty-state">
                                <i class="fas fa-leaf"></i>
                                <h5>No Cleanup Drives Found</h5>
                                <p>Create a cleanup drive to start assigning personnel.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function applyFilters() {
    const date = document.getElementById('filter-date').value;
    const route = document.getElementById('filter-route').value;
    const foreman = document.getElementById('filter-foreman').value;
    const search = document.getElementById('filter-search').value;
    console.log('Applying cleanup filters:', { date, route, foreman, search });
}

function resetFilters() {
    document.getElementById('filter-date').value = '';
    document.getElementById('filter-route').value = '';
    document.getElementById('filter-foreman').value = '';
    document.getElementById('filter-search').value = '';
}

function viewDrive(id) {
    console.log('View drive', id);
}

document.addEventListener('DOMContentLoaded', function () {
    const searchForm = document.getElementById('schedule-search-form');
    if (!searchForm) return;

    searchForm.addEventListener('submit', function (e) {
        ['filter-date', 'filter-route', 'filter-foreman'].forEach(function (id) {
            const el = document.getElementById(id);
            if (!el) return;
            let input = searchForm.querySelector('[name="' + id + '"]');
            if (!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = id;
                searchForm.appendChild(input);
            }
            input.value = el.value || '';
        });
    });
});
</script>

<?php
$content = ob_get_clean();
$additionalStyles = '<link rel="stylesheet" href="/assets/css/modal.css">';
$additionalStyles .= ' <link rel="stylesheet" href="/assets/css/garbage_collection.css">';
require base_path('views/layout.php');