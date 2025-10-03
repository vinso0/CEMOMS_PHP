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
        <div class="stat-icon" style="background: #2196F3;">
            <i class="fas fa-recycle"></i>
        </div>
        <div class="stat-info">
            <h3>Operations: </h3>
            <div class="options">
                <select id="operation-type-filter" class="operation-type-select" onchange="updateOperationCount()">
                <option value="all">All Types</option>
                <option value="collection">Garbage Collection</option>
                <option value="sweeping">Street Sweeping</option>
                <option value="flushing">Flushing</option>
                <option value="deClogging">De-clogging</option>
                <option value="cleanup">Cleanup Drives</option>
                </select>
            </div>
            <span class="stat-number" id="operation-count">
                <?= $stats['operations']['all'] ?? 0 ?>
            </span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #FF9800;">
            <i class="fas fa-user-tie"></i>
        </div>
        <div class="stat-info">
            <h3>Active Foremen</h3>
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

<!-- Filters Section -->
<div class="filters-container">
    <div class="filters-header">
        <h3><i class="fas fa-filter"></i> Filters:</h3>
    </div>
    <div class="filters-content">
        <div class="filter-item">
            <label for="operation-type">Operation Type</label>
            <select id="operation-type" class="form-select">
                <option value="all">All Types</option>
                <option value="collection">Garbage Collection</option>
                <option value="sweeping">Street Sweeping</option>
                <option value="flushing">Flushing</option>
                <option value="de-clogging">De-clogging</option>
                <option value="cleanup">Cleanup Drives</option>
            </select>
        </div>
        
        <div class="filter-item">
            <label for="area">Area</label>
            <select id="area" class="form-select">
                <option value="">All Areas</option>
                <option value="north">Bagong Barrio East</option>
                <option value="south">Bagong Barrio West</option>
                <option value="east">Balintawak</option>
                <option value="west">Bario San Jose</option>
                <option value="west">Dagat-Dagatan</option>
                <option value="west">Grace Park East</option>
                <option value="west">Grace Park West</option>
                <option value="west">Kaunlaran Village</option>
                <option value="west">Libis Baesa/Reparo</option>
                <option value="west">Marulas</option>
                <option value="west">Maypajo</option>
                <option value="west">Morning Breeze</option>
                <option value="west">Poblacion</option>
                <option value="west">Sangandaan</option>
                <option value="west">Santa Quiteria</option>
                <option value="west">Talipapa</option>
                <option value="west">University Hills</option>
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

<!-- Map Section -->
<div class="map-container">
        <!-- NOTE: Not working yet, pag react + leaflet.js 'tong mga naka comment -->
        <!-- Option 1: Relative path (works if dashboard URL is /admin/dashboard) 
        <iframe 
           src="/react/index.html" 
           width="100%" 
           height="600px" 
           frameborder="0" 
           style="border: none; display: block; min-height: 500px;">
       </iframe>
        -->
        <!-- Option 2: Absolute path from web root (more reliable, works from any URL) -->
        <!-- <iframe src="/react/index.html" width="100%" height="500px" frameborder="0" style="border: none;"></iframe> -->
        <div id="map" class="dashboard-map" style="height: 500px; width: 100%; border: 1px solid #ccc; border-radius: 8px; margin: 20px 0;"></div>
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