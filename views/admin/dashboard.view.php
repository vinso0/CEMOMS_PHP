<?php
$title = 'Admin Dashboard - CEMOMS';
$pageTitle = 'Dashboard';

ob_start();
?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!--
<div class="content-header">
    <h1 class="content-title">Dashboard</h1>
</div>
-->

<!-- Stats Cards Row -->
<div class="dashboard-stats">
    <div class="stat-card">
        <div class="stat-icon" style="background: #4CAF50;">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="stat-info">
            <h3>Total Reports</h3>
            <span class="stat-number"><?= $stats['total_reports'] ?? 0 ?></span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #FF9800;">
            <i class="fa-solid fa-clipboard-list"></i>
        </div>
        <div class="stat-info">
            <h3>Scheduled Operation</h3>
            <span class="stat-number"><?= $stats['active_foremen'] ?? 0 ?></span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #9C27B0;">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <h3>Pending Reports</h3>
            <span class="stat-number"><?= $stats['pending_reports'] ?? 0 ?></span>
        </div>
    </div>
</div>



<!-- Recent Reports Table -->
<div class="reports-container">
    <div class="reports-header">
        <h3><i class="fas fa-table"></i> Recent Reports</h3>
        <button class="btn btn-primary btn-sm" onclick="window.location.href='/admin/reports'">
            <i class="fas fa-eye"></i> View All
        </button>
    </div>
    <div class="reports-table-wrapper">
        <table class="reports-table">
            <thead>
                <tr>
                    <th>Report ID</th>
                    <th>Date</th>
                    <th>Operation Type</th>
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
                            <td><?= htmlspecialchars($report['date']) ?></td>
                            <td><?= htmlspecialchars($report['operation_type']) ?></td>
                            <td><?= htmlspecialchars($report['area']) ?></td>
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
                                <h5>No Recent Reports</h5>
                                <p>Reports will appear here once operations are logged</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Operations data (this would come from your PHP/backend)
const operationsData = {
    all: <?= $stats['operations']['all'] ?? 0 ?>,
    collection: <?= $stats['operations']['collection'] ?? 0 ?>,
    sweeping: <?= $stats['operations']['sweeping'] ?? 0 ?>,
    flushing: <?= $stats['operations']['flushing'] ?? 0 ?>,
    deClogging: <?= $stats['operations']['deClogging'] ?? 0 ?>,
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
    const area = document.getElementById('area').value;
    const dateRange = document.getElementById('date-range').value;
    
    console.log('Applying filters:', { operationType, area, dateRange });
    // Add your filter logic here
    alert('Filters applied! Implement your filter logic here.');
}

function resetFilters() {
    document.getElementById('operation-type').value = '';
    document.getElementById('area').value = '';
    document.getElementById('date-range').value = '';
    console.log('Filters reset');
}

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize map for Caloocan City (South)
        var map = L.map('map').setView([14.6396, 120.9822], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        // Add marker with popup
        /*L.marker([14.6396, 120.9822]).addTo(map)
            .bindPopup('Caloocan City (South), Philippines')
            .openPopup(); */
    });
</script>

<?php
$content = ob_get_clean();

$additionalStyles = ' <link rel="stylesheet" href="/assets/css/admin-dashboard.css">';

require base_path('views/layout.php');