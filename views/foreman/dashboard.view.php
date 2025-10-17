<?php
$title = 'Admin Dashboard - CEMOMS';
$pageTitle = 'Dashboard';

ob_start();
?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
.dashboard-stats{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:8px;
    margin-bottom:12px;
}
.dashboard-stats .stat-card{
    padding:8px 10px;
    display:flex;
    gap:8px;
    align-items:center;
    border-radius:6px;
    background:#fff;
    box-shadow:0 1px 3px rgba(0,0,0,0.05);
}
.dashboard-stats .stat-icon{
    width:36px;
    height:36px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:6px;
    color:#fff;
}
.dashboard-stats .stat-info h3{
    font-size:0.85rem;
    margin:0;
}
.dashboard-stats .stat-number{
    font-size:1rem;
    font-weight:600;
}
</style>
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
        
        <div class="filter-actions" style="display:flex; gap:8px; justify-content:center;">
            <button class="btn btn-primary" style="flex:1; padding:6px 10px; font-size:0.85rem;" onclick="applyFilters()">
                <i class="fas fa-search"></i> Apply
            </button>
            <button class="btn btn-secondary" style="flex:1; padding:6px 10px; font-size:0.85rem;" onclick="resetFilters()">
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

<!-- Reports panel moved to separate view -->
<div class="card" style="padding:18px; margin-bottom:18px;">
    <h3 style="margin:0 0 10px 0;">Reports</h3>
    <p style="margin:0 0 12px 0;">Submit and view your reports from the Reports panel.</p>
    <a href="/foreman/reports" class="btn btn-primary">Go to Reports</a>
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

<script>
function applyMyReportFilters() {
    const status = document.getElementById('filter-status').value.toLowerCase();
    const op = document.getElementById('filter-op').value.toLowerCase();
    const date = document.getElementById('filter-date').value;

    const tbody = document.getElementById('my-reports-body');
    if (!tbody) return;

    Array.from(tbody.querySelectorAll('tr')).forEach(row => {
        const cols = row.querySelectorAll('td');
        if (cols.length === 0) return; // skip header/empty

        const rowDate = (cols[1]?.textContent || '').trim();
        const rowOp = (cols[2]?.textContent || '').trim().toLowerCase();
        const rowStatus = (cols[4]?.textContent || '').trim().toLowerCase();

        let show = true;
        if (status && rowStatus.indexOf(status) === -1) show = false;
        if (op && rowOp.indexOf(op) === -1) show = false;
        if (date) {
            // match YYYY-MM-DD or similar; do simple contains
            if (rowDate.indexOf(date) === -1) show = false;
        }

        row.style.display = show ? '' : 'none';
    });
}
</script>

<?php
$content = ob_get_clean();

$additionalStyles = ' <link rel="stylesheet" href="/assets/css/admin-dashboard.css">';

require base_path('views/layout.php');