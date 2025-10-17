<?php
$title = 'My Reports - CEMOMS';
$pageTitle = 'Reports';

ob_start();
?>

<style>
.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
    margin-bottom: 12px;
}
.dashboard-stats .stat-card {
    padding: 8px 10px;
    display: flex;
    gap: 8px;
    align-items: center;
    border-radius: 6px;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}
.dashboard-stats .stat-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    color: #fff;
}
.dashboard-stats .stat-info h3 {
    font-size: 0.85rem;
    margin: 0;
}
.dashboard-stats .stat-number {
    font-size: 1rem;
    font-weight: 600;
}
/* Mobile submit modal styles */
.mobile-modal-overlay{position:fixed;left:0;top:0;right:0;bottom:0;background:rgba(0,0,0,0.45);display:none;align-items:center;justify-content:center;z-index:12000}
.mobile-modal{background:#fff;border-radius:8px;max-width:390px;width:94%;box-shadow:0 8px 30px rgba(0,0,0,0.2);overflow:hidden}
.mobile-modal .modal-header{padding:12px 14px;background:#faf7f2}
.mobile-modal .modal-body{padding:12px 14px}
.mobile-modal .modal-footer{padding:10px 14px;display:flex;gap:8px;justify-content:flex-end}
.mobile-modal .form-control, .mobile-modal select{font-size:0.95rem;padding:8px}
.mobile-modal .btn{padding:6px 10px;font-size:0.92rem}
.mobile-modal .upload-preview{height:90px;background:#eee;border-radius:4px;margin-bottom:8px}
@media (max-width:420px){
    .mobile-modal{height:100vh;border-radius:0;width:100%;}
    .mobile-modal .modal-body{padding:14px 16px}
}
</style>



<!-- Stats Cards Row (Foreman) -->
<div class="dashboard-stats" style="margin-bottom:18px;">
    <div class="stat-card">
        <div class="stat-icon" style="background: #4CAF50;">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="stat-info">
            <h3>Total Reports Submitted</h3>
            <span class="stat-number"><?= $stats['total_reports'] ?? 0 ?></span>
        </div>
    </div>
    
        <!-- Mobile Submit Report Modal -->
        <div class="mobile-modal-overlay" id="mobileModal">
            <div class="mobile-modal" role="dialog" aria-modal="true">
                <div class="modal-header">
                    <strong>Submit Report</strong>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Activity Type</label>
                        <input type="text" id="sr-activity" class="form-control" placeholder="e.g. Garbage Collection">
                    </div>
                    <div class="form-group" style="margin-top:8px;">
                        <label>Location</label>
                        <input type="text" id="sr-location" class="form-control" placeholder="Enter location or tap to pick">
                    </div>
                    <div class="form-group" style="margin-top:8px;">
                        <label>Upload Photos</label>
                        <div class="upload-preview" id="sr-preview"></div>
                        <input type="file" id="sr-photos" accept="image/*" multiple style="width:100%" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" id="sr-cancel">Cancel</button>
                    <button class="btn btn-success" id="sr-submit">Submit</button>
                </div>
            </div>
        </div>

        <script>
        // Modal open/close
        document.addEventListener('DOMContentLoaded', function(){
            var modal = document.getElementById('mobileModal');
            var openBtn = document.getElementById('open-submit-modal');
            var cancelBtn = document.getElementById('sr-cancel');
            var submitBtn = document.getElementById('sr-submit');
            var photosInput = document.getElementById('sr-photos');
            var preview = document.getElementById('sr-preview');

            function openModal(){
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
            function closeModal(){
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }

            if (openBtn) openBtn.addEventListener('click', openModal);
            if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

            // simple client-side image preview (first image)
            if (photosInput) photosInput.addEventListener('change', function(e){
                preview.innerHTML = '';
                var file = e.target.files && e.target.files[0];
                if (!file) return;
                var img = document.createElement('img');
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'cover';
                img.src = URL.createObjectURL(file);
                preview.appendChild(img);
            });

            if (submitBtn) submitBtn.addEventListener('click', function(){
                // For now do a simple validation and close
                var activity = document.getElementById('sr-activity').value.trim();
                if (!activity) { alert('Please enter activity type'); return; }
                // In a real app we'd post via fetch/XHR here
                alert('Report submitted (demo)');
                closeModal();
            });
        });
        </script>
    <div class="stat-card">
        <div class="stat-icon" style="background: #FFB300;">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <h3>Pending Reports</h3>
            <span class="stat-number"><?= $stats['pending_reports'] ?? 0 ?></span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #4CAF50;">
            <i class="fas fa-check"></i>
        </div>
        <div class="stat-info">
            <h3>Approved Reports</h3>
            <span class="stat-number"><?= $stats['approved_reports'] ?? 0 ?></span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #f44336;">
            <i class="fas fa-times"></i>
        </div>
        <div class="stat-info">
            <h3>Rejected Reports</h3>
            <span class="stat-number"><?= $stats['rejected_reports'] ?? 0 ?></span>
        </div>
    </div>
</div>

<!-- filters will be rendered inside the header below (smaller controls) -->

<!-- filters -->
        <div style="flex:1; display:flex; justify-content:center;">
            <div class="compact-filters" style="display:flex; gap:6px; align-items:center;">
                <select id="filter-status" class="form-select" style="width:100px; padding:4px 6px; font-size:0.85rem;">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
                <select id="filter-op" class="form-select" style="width:110px; padding:4px 6px; font-size:0.85rem;">
                    <option value="">All Operations</option>
                    <option value="collection">Garbage Collection</option>
                    <option value="sweeping">Street Sweeping</option>
                    <option value="flushing">Flushing</option>
                    <option value="de-clogging">De-clogging</option>
                    <option value="cleanup">Cleanup</option>
                </select>
                <input id="filter-date" type="date" class="form-control" style="width:110px; padding:4px 6px; font-size:0.85rem;" />
                <button class="btn btn-primary btn-sm" onclick="applyMyReportFilters()" style="padding:4px 8px; font-size:0.85rem;"><i class="fas fa-filter"></i></button>
            </div>
        </div>

<!-- Reports container (compact header with centered small filters and submit to the right) -->
<div class="reports-container card">
    <div class="reports-table-header" style="display:flex; align-items:center; justify-content:space-between; padding:12px 18px; gap:12px;">
        <div>
            <h4 style="margin:0;"><i class="fas fa-table"></i> Reports Table</h4>
        </div>

         <!-- right: submit button -->
        <div style="flex:0 0 auto;">
            <button id="open-submit-modal" class="btn btn-primary" style="padding:6px 10px; font-size:0.95rem;">Submit Report</button>
        </div>
       
    </div>
    <div class="reports-table-wrapper" style="padding:12px;">
        <table class="reports-table">
            <thead>
                <tr>
                    <th>Report ID</th>
                    <th>Date</th>
                    <th>Operation Type</th>
                    <th>Area</th>
                    <th>Proof</th>
                    <th>Status</th>
                    <th>Admin Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="my-reports-body">
                <?php if (!empty($my_reports)): ?>
                    <?php foreach ($my_reports as $report): ?>
                        <tr>
                            <td><?= htmlspecialchars($report['report_id'] ?? $report['id']) ?></td>
                            <td><?= htmlspecialchars($report['date']) ?></td>
                            <td><?= htmlspecialchars($report['operation_type']) ?></td>
                            <td><?= htmlspecialchars($report['area'] ?? '') ?></td>
                            <td>
                                <?php if (!empty($report['proof'])): ?>
                                    <img src="<?= htmlspecialchars($report['proof']) ?>" alt="proof" style="width:50px;height:40px;object-fit:cover;border-radius:6px;" />
                                <?php endif; ?>
                            </td>
                            <td><span class="status-badge status-<?= htmlspecialchars($report['status']) ?>"><?= ucfirst(htmlspecialchars($report['status'])) ?></span></td>
                            <td><?= htmlspecialchars($report['admin_remarks'] ?? '') ?></td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="/foreman/reports/view/<?= htmlspecialchars($report['id']) ?>">View</a>
                                <?php if (($report['status'] ?? '') === 'pending'): ?>
                                    <a class="btn btn-sm btn-edit" href="/foreman/reports/edit/<?= htmlspecialchars($report['id']) ?>">Edit</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No reports found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function applyMyReportFilters() {
    const statusFilter = document.getElementById('filter-status').value.toLowerCase();
    const opFilter = document.getElementById('filter-op').value.toLowerCase();
    const dateFilter = document.getElementById('filter-date').value;

    const table = document.querySelector('.reports-table');
    if (!table) return;

    // Find column indices by header text to be robust
    const headers = Array.from(table.querySelectorAll('thead th')).map(h => h.textContent.trim().toLowerCase());
    const idxDate = headers.indexOf('date');
    const idxOp = headers.indexOf('operation type');
    const idxStatus = headers.indexOf('status');

    const tbody = document.getElementById('my-reports-body');
    if (!tbody) return;

    Array.from(tbody.querySelectorAll('tr')).forEach(row => {
        const cols = row.querySelectorAll('td');
        if (cols.length === 0) return;

        const rowDate = (cols[idxDate]?.textContent || '').trim();
        const rowOp = (cols[idxOp]?.textContent || '').trim().toLowerCase();
        const rowStatus = (cols[idxStatus]?.textContent || '').trim().toLowerCase();

        let show = true;
        if (statusFilter && rowStatus.indexOf(statusFilter) === -1) show = false;
        if (opFilter && rowOp.indexOf(opFilter) === -1) show = false;
        if (dateFilter) {
            if (rowDate.indexOf(dateFilter) === -1) show = false;
        }

        row.style.display = show ? '' : 'none';
    });
}
</script>

<script>
// Ensure sidebar/overlay are closed when this page loads so the panel is clickable
document.addEventListener('DOMContentLoaded', function() {
    try {
        var overlay = document.querySelector('.sidebar-overlay');
        var sidebar = document.querySelector('.sidebar');
        if (overlay) {
            overlay.classList.remove('active');
            overlay.style.display = 'none';
            overlay.style.pointerEvents = 'none';
        }
        if (sidebar) {
            sidebar.classList.remove('open');
        }
        document.body.style.overflow = '';
    } catch (e) {
        console.error('Error cleaning overlay/sidebar on reports page', e);
    }
});
</script>

<?php
$content = ob_get_clean();

$additionalStyles = ' <link rel="stylesheet" href="/assets/css/admin-dashboard.css">';

require base_path('views/layout.php');

?>
