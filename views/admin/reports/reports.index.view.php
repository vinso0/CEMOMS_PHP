<?php
$title = 'Reports Management - CEMOMS';
$pageTitle = 'Reports Management';

ob_start();
?>

<div class="section-header">
    <h2 class="section-title">Reports List</h2>
</div>

<!-- Filters Section -->
<div class="filters-container">
    <div class="filters-header">
        <h3><i class="fas fa-filter"></i> Filters:</h3>
    </div>
    <div class="filters-content">
        <div class="filter-item">
            <label for="operation-type">Operation Type</label>
            <select id="operation-type" class="form-select">
                <option value="collection">Garbage Collection</option>
                <option value="sweeping">Street Sweeping</option>
                <option value="flushing">Flushing</option>
                <option value="de-clogging">De-clogging</option>
                <option value="cleanup">Cleanup Drives</option>
            </select>
        </div>
        
        <div class="filter-item">
            <label for="date-range">Date Range</label>
            <input type="date" id="date-range" class="form-control">
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


<!-- Recent Reports Table -->
<div class="reports-container">
    <div class="reports-header">
        <h3><i class="fas fa-table"></i> Reports Table</h3>
        <form class="d-flex" method="GET" action="/admin/reports" style="gap: 0.5rem;">
            <input type="search" name="q" class="form-control form-control-sm" placeholder="Search reports..." aria-label="Search reports">
            <button class="btn btn-primary btn-sm" type="submit">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
    </div>
    <div class="reports-table-wrapper">
        <table class="reports-table">
            <thead>
                <tr>
                    <th>Report ID</th>
                    <th>Operation Type</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Foreman</th>
                    <th>Proof</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recent_reports)): ?>
                    <?php foreach ($recent_reports as $report): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($report['id']) ?></td>
                            <td><?= htmlspecialchars($report['operation_type']) ?></td>
                            <td><?= htmlspecialchars($report['date']) ?></td>
                            <td><?= htmlspecialchars($report['location']) ?></td>
                            <td><?= htmlspecialchars($report['foreman']) ?></td>
                            <td>
                                <span class="status-badge status-<?= htmlspecialchars($report['status']) ?>">
                                    <?= ucfirst(htmlspecialchars($report['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h5>No Reports</h5>
                                <p>Reports will appear here once operations are logged</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Operations data (this would come from your PHP/backend)
const operationsData = {
    all: <?= $stats['operations']['all'] ?? 0 ?>,
    collection: <?= $stats['operations']['collection'] ?? 0 ?>,
    sweeping: <?= $stats['operations']['sweeping'] ?? 0 ?>,
    flushing: <?= $stats['operations']['flushing'] ?? 0 ?>,
    deClogging: <?= $stats['operations']['deClogging'] ?? 0 ?>
    cleanup: <?= $stats['operations']['cleanup'] ?? 0 ?>
};

function updateOperationCount() {
    const filterValue = document.getElementById('operation-type-filter').value;
    const countElement = document.getElementById('operation-count');
    
    // Animate the number change
    countElement.style.opacity = '0.5';
    
    setTimeout(() => {
        countElement.textContent = operationsData[filterValue];
        countElement.style.opacity = '1';
    }, 150);
}

function applyFilters() {
    const operationType = document.getElementById('operation-type').value;
    const dateRange = document.getElementById('date-range').value;

    console.log('Applying filters:', { operationType, dateRange });
    // Add your filter logic here
    alert('Filters applied! Implement your filter logic here.');
}

function resetFilters() {
    document.getElementById('operation-type').value = '';
    document.getElementById('date-range').value = '';
    console.log('Filters reset');
}
</script>

<?php
$content = ob_get_clean();

$additionalStyles = ' <link rel="stylesheet" href="/assets/css/admin-dashboard.css">';

require base_path('views/layout.php');