<?php
$title = 'Flushing - Operations';
$pageTitle = 'Flushing';

ob_start();
?>
<div class="section-header">
    <h2 class="section-title">Flushing Management</h2>
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

<!-- Flushing Schedules Table (mirrors street sweeping markup) -->
<div class="trucks-table-container">
    <div class="table-header d-flex" style="align-items:center; justify-content:space-between; gap:0.75rem;">
        <h3><i class="fas fa-tint"></i> Flushing Schedules</h3>
        <form class="d-flex" method="GET" action="/admin/operations/flushing" id="schedule-search-form" style="gap: 0.5rem; margin-left:auto;">
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
                    <th>Foreman</th>
                       <th>Date &amp; Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($schedules)): ?>
                    <?php foreach ($schedules as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['route_name'] ?? $s['area'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($s['foreman_name'] ?? 'Unassigned') ?></td>
                            <td><?= htmlspecialchars($s['schedule']) ?></td>
                            <td>
                                <span class="status-badge status-<?= htmlspecialchars($s['status'] ?? 'scheduled') ?>">
                                    <?= ucfirst(htmlspecialchars($s['status'] ?? 'scheduled')) ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" title="View Details" onclick="viewSchedule(<?= htmlspecialchars($s['id'] ?? 0) ?>)">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">
                            <div class="empty-state">
                                <i class="fas fa-tint"></i>
                                <h5>No Schedules Found</h5>
                                <p>Create a new flushing schedule to get started.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Flushing Schedule Modal (mirrors street sweeping modal layout) -->
<div class="modal fade" id="addScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Flushing Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/admin/operations/flushing/schedule/store">
                <div class="modal-body">
                    <input type="hidden" id="add-schedule-id" name="id" value="">

                    <div class="mb-3">
                        <label for="add-route" class="form-label">Route</label>
                        <select id="add-route" name="route_id" class="form-select">
                            <option value="">Select Route</option>
                            <?php if (!empty($routes)): ?>
                                <?php foreach ($routes as $route): ?>
                                    <option value="<?= htmlspecialchars($route['id']) ?>"><?= htmlspecialchars($route['route_name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                       <label for="add-schedule" class="form-label">Date &amp; Time</label>

                    <div class="mb-3">
                        <label for="add-foreman" class="form-label">Foreman</label>
                        <select id="add-foreman" name="foreman_id" class="form-select">
                            <option value="">Select Foreman</option>
                            <?php if (!empty($foremen)): ?>
                                <?php foreach ($foremen as $foreman): ?>
                                    <option value="<?= htmlspecialchars($foreman['id']) ?>"><?= htmlspecialchars($foreman['username']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="add-schedule" class="form-label">Schedule</label>
                        <input type="text" id="add-schedule" name="schedule" class="form-control" placeholder="e.g., 2025-10-15, 7:00 AM">
                    </div>

                    <div class="mb-3">
                        <label for="add-status" class="form-label">Status</label>
                        <select id="add-status" name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function applyFilters() {
    const date = document.getElementById('filter-date').value;
    const route = document.getElementById('filter-route').value;
    const foreman = document.getElementById('filter-foreman').value;
    const search = document.getElementById('filter-search').value;

    // For now, submit as GET to the same page with query params
    const params = new URLSearchParams();
    if (date) params.append('date', date);
    if (route) params.append('route', route);
    if (foreman) params.append('foreman', foreman);
    if (search) params.append('q', search);

    window.location = '/admin/operations/flushing?' + params.toString();
}

function resetFilters() {
    document.getElementById('filter-date').value = '';
    document.getElementById('filter-route').value = '';
    document.getElementById('filter-foreman').value = '';
    document.getElementById('filter-search').value = '';
    window.location = '/admin/operations/flushing';
}

function viewSchedule(id) {
    // Navigate to a schedule view page or open a modal
    if (!id) return;
    window.location = '/admin/operations/flushing/schedule/view?id=' + encodeURIComponent(id);
}

// Ensure the search form includes the top filter values (date/route/foreman) when submitted
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