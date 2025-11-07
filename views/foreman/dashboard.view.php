<?php
$title = 'Foreman Dashboard - CEMOMS';
$pageTitle = 'Dashboard';

ob_start();
?>

<style>
/* Modern Dashboard Styling */
.dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Status Cards */
.status-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.status-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 6px 18px rgba(20, 20, 40, 0.06);
    transition: transform 0.16s ease, box-shadow 0.16s ease;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.status-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 28px rgba(20,20,40,0.10);
}

.status-card .icon {
    font-size: 28px;
    margin-bottom: 8px;
    color: rgba(0,0,0,0.6);
}

.status-card.completed .icon { color: #2e7d32; }
.status-card.ongoing .icon { color: #1565c0; }
.status-card.pending .icon { color: #ff8f00; }

.status-card h3 {
    margin: 0 0 8px 0;
    color: #222;
    font-size: 1rem;
}

.status-card .count {
    font-size: 2rem;
    font-weight: 700;
    color: #0f1720;
    margin-bottom: 6px;
}

.status-meta {
    color: #6b7280;
    font-size: 0.9rem;
}

/* Modal Styling Fixes */
.modal {
    padding-right: 0 !important;
}

.modal-dialog {
    margin: 1rem;
    max-width: 500px;
    width: calc(100% - 2rem);
}

.modal-content {
    max-height: calc(100vh - 3.5rem);
    border-radius: 12px;
    overflow: hidden;
}

.modal-body {
    max-height: calc(100vh - 210px);
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    padding: 1.5rem;
}

/* For mobile devices */
@media (max-width: 576px) {
    .modal-dialog {
        margin: 0.5rem;
        width: calc(100% - 1rem);
    }
    
    .modal-content {
        max-height: calc(100vh - 1rem);
        border-radius: 12px 12px 0 0;
    }
    
    .modal-body {
        max-height: calc(100vh - 180px);
        padding: 1rem;
    }
}

/* Recent Reports Section */
.reports-section {
    background: #fff;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 6px 18px rgba(20, 20, 40, 0.04);
    margin-bottom: 20px;
}

.reports-section h2 {
    margin: 0 0 16px 0;
    color: #111827;
    font-size: 1.25rem;
}

.reports-list {
    margin-bottom: 18px;
}

.report-card {
    padding: 14px;
    border-radius: 10px;
    background: linear-gradient(180deg, #fbfdff 0%, #f8f9fb 100%);
    margin-bottom: 12px;
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 12px;
    align-items: center;
    border: 1px solid rgba(15,23,36,0.03);
}

.report-info h4 {
    margin: 0 0 6px 0;
    color: #0f1720;
    font-size: 1rem;
}

.report-meta {
    color: #6b7280;
    font-size: 0.9rem;
    display: flex;
    gap: 12px;
    align-items: center;
}

.report-date {
    color: #9ca3af;
    font-size: 0.9rem;
}

/* Floating Action Button */
.fab {
    position: fixed !important;
    /* sit above bottom-nav: move slightly above (80px) */
    bottom: 80px !important;
    right: 20px !important;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg,#2196F3,#1976D2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    box-shadow: 0 10px 30px rgba(25,118,210,0.18);
    cursor: pointer;
    border: none;
    z-index: 20000; /* sit above bottom-nav and other elements */
    will-change: transform, box-shadow;
    transition: transform 180ms ease, box-shadow 180ms ease;
}

.fab:hover {
    transform: scale(1.06);
    box-shadow: 0 14px 40px rgba(25,118,210,0.22);
}

/* Ensure FAB has adequate spacing on small screens (move further above fixed bottom nav) */
@media (max-width: 480px) {
    .fab { bottom: 92px !important; right: 16px !important; }
}

@media (min-width: 481px) and (max-width: 900px) {
    .fab { bottom: 88px !important; }
}

/* Modal form grid for responsive inputs */
.modal-form-grid{
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap:12px;
}

.modal-form-grid .mb-3{margin-bottom:0}

@media (max-width:768px){
    .modal-form-grid{grid-template-columns:1fr}
}

/* Slightly larger, modern modal appearance */
.modal-content{border-radius:12px}

/* Mobile Responsiveness */
@media (max-width: 900px) {
    .status-cards { grid-template-columns: 1fr; }
    .report-card { grid-template-columns: 1fr; }
}

</style>
<style>
/* Reports grid layout for cards */
.report-grid {
    display: grid;
    grid-template-columns: 1.2fr 1.2fr 2fr 2fr 2fr;
    gap: 10px;
    align-items: center;
}
.report-header {
    background: #e8f5e9;
    font-weight: bold;
    box-shadow: none;
    border: 1px solid #b2dfdb;
    margin-bottom: 0;
}
.report-col {
    font-size: 1rem;
    color: #222;
    word-break: break-word;
}
@media (max-width: 900px) {
    .report-grid {
        grid-template-columns: 1fr 1fr 1.5fr 1.5fr 1.5fr;
        font-size: 0.95rem;
    }
}
@media (max-width: 600px) {
    .report-grid {
        grid-template-columns: 1fr;
        gap: 6px;
    }
    .report-col {
        font-size: 0.98rem;
        padding-bottom: 2px;
    }
    .report-header .report-grid {
        display: none;
    }
    .report-card:not(.report-header) .report-grid {
        display: grid;
        grid-template-columns: 1fr;
    }
}

/* Today's operations card (desktop: grid with 3 columns; mobile: stacked) */
.today-op-card{
    display:grid;
    grid-template-columns: 2fr 2fr 1fr;
    gap:12px;
    align-items:center;
    padding:12px 16px;
    background:#fff;
    border-radius:10px;
    border:1px solid rgba(15,23,36,0.06);
    box-shadow:0 6px 18px rgba(20,20,40,0.04);
    margin-bottom:10px;
    transition: transform 180ms ease, box-shadow 180ms ease;
}
.today-op-card:hover{ transform: translateY(-6px); box-shadow:0 12px 30px rgba(20,20,40,0.08); }
.today-op-route{font-weight:700;color:#0f1720}
.today-op-date{color:#6b7280}
.today-op-actions{display:flex;gap:8px;align-items:center;justify-self:end}

@media (max-width:768px){
    .today-op-card{grid-template-columns:1fr;align-items:flex-start}
    .today-op-actions{width:100%;display:flex;justify-content:flex-start}
}

/* Button hover/transition */
.btn{ transition: transform 120ms ease, box-shadow 120ms ease; }
.btn:hover{ transform: translateY(-2px); }

/* Scheduled operations cards */
.scheduled-operations {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-top: 12px;
}
.operation-card{
    background: #fff;
    border-radius: 12px;
    padding: 14px;
    box-shadow: 0 6px 18px rgba(20,20,40,0.04);
    display:flex;
    align-items:center;
    justify-content:space-between;
    border:1px solid rgba(15,23,36,0.03);
}
.operation-card-left{display:flex;flex-direction:column;}
.operation-card-actions{display:flex;gap:8px;align-items:center}

@media (max-width:900px){
    .scheduled-operations{grid-template-columns:1fr}
}

</style>

<div class="dashboard-container">
    <div id="screenSizeIndicator" style="position:fixed;top:12px;right:12px;background:#111827;color:#fff;padding:6px 8px;border-radius:6px;font-size:12px;opacity:0.9;z-index:1040;">--</div>
    <!-- Status Cards -->
    <div class="status-cards">
        <div class="status-card completed">
            <div class="icon"><i class="fas fa-check-circle" aria-hidden="true"></i></div>
            <h3>Completed</h3>
            <div class="count"><?= $stats['completed_operations'] ?? 0 ?></div>
            <div class="status-meta">Operations completed</div>
        </div>

        <div class="status-card ongoing">
            <div class="icon"><i class="fas fa-truck-moving" aria-hidden="true"></i></div>
            <h3>Ongoing</h3>
            <div class="count"><?= $stats['ongoing_operations'] ?? 0 ?></div>
            <div class="status-meta">Operations in progress</div>
        </div>

        <div class="status-card pending">
            <div class="icon"><i class="fas fa-clock" aria-hidden="true"></i></div>
            <h3>Pending</h3>
            <div class="count"><?= $stats['pending_operations'] ?? 0 ?></div>
            <div class="status-meta">Operations awaiting action</div>
        </div>
    </div>

    <!-- Recent Reports Section -->
    <div class="reports-section">
    <h2>Pending Operation</h2>
    <div class="reports-list" id="todayReportsList">
        <?php
            // DEV helper: if no scheduled operations are provided, inject sample data for local preview
            if ((empty($scheduledOperations) || !is_array($scheduledOperations)) && in_array($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', ['127.0.0.1', '::1'])) {
                $scheduledOperations = [
                    ['id' => 'OP-001', 'scheduled_at' => '2025-11-07 09:30:00', 'route_name' => 'Route A-123', 'operation_type' => 'Garbage Collection', 'status' => 'pending'],
                    ['id' => 'OP-002', 'scheduled_at' => '2025-11-07 10:00:00', 'route_name' => 'Route B-456', 'operation_type' => 'Street Sweeping', 'status' => 'ongoing'],
                    ['id' => 'OP-003', 'scheduled_at' => '2025-11-07 11:30:00', 'route_name' => 'Route C-789', 'operation_type' => 'Flushing', 'status' => 'pending'],
                ];
            }
        ?>
        <?php
            $today = date('Y-m-d');
            $foundToday = false;

            if (!empty($scheduledOperations) && is_array($scheduledOperations)):
                foreach ($scheduledOperations as $op):
                    $dtRaw = $op['scheduled_at'] ?? $op['date_time'] ?? $op['datetime'] ?? $op['date'] ?? null;
                    $opDate = $dtRaw ? date('Y-m-d', strtotime($dtRaw)) : null;
                    if ($opDate !== $today) continue; // skip non-today
                    $foundToday = true;
                    $displayDt = $dtRaw ? date('M d, Y h:i A', strtotime($dtRaw)) : 'TBD';
                    $routeName = $op['route_name'] ?? $op['route'] ?? $op['area'] ?? ($op['operation_title'] ?? 'Unknown Route');
                    $opId = $op['id'] ?? $op['operation_id'] ?? '';
                    $opType = $op['operation_type'] ?? $op['operation_title'] ?? '';
        ?>
            <div class="today-op-card">
                <div class="today-op-col today-op-route">Route: <?= htmlspecialchars($routeName) ?></div>
                <div class="today-op-col today-op-date">Date: <?= htmlspecialchars($displayDt) ?></div>
                <div class="today-op-col today-op-actions">
                    <button type="button" class="btn btn-outline-primary" onclick="openDetailsModal('<?= htmlspecialchars($opId) ?>', '<?= htmlspecialchars($routeName) ?>', '<?= htmlspecialchars($displayDt) ?>', '<?= htmlspecialchars($opType) ?>', '<?= htmlspecialchars($op['status'] ?? '') ?>')">Details</button>
                    <button type="button" class="btn btn-success" onclick="openAddReportForOperation('<?= htmlspecialchars($opId) ?>', '<?= htmlspecialchars($opType) ?>', '<?= htmlspecialchars($routeName) ?>')">Add Report</button>
                </div>
            </div>
        <?php
                endforeach;
            endif;

            if (!$foundToday):
        ?>
            <div class="report-card">
                <div class="report-info">
                    <h4>No operations scheduled for today</h4>
                    <div class="report-meta">—</div>
                </div>
                <div class="report-date">—</div>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Add Report Modal (Bootstrap 5) -->
<div class="modal fade" id="addReportModal" tabindex="-1" aria-labelledby="addReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addReportModalLabel">Add Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addReportForm" class="needs-validation" enctype="multipart/form-data" novalidate>
                    <input type="hidden" id="addReportOpId" name="operation_id">
                    
                    <!-- Route Input -->
                    <div class="mb-3">
                        <label for="addReportRoute" class="form-label">Route <span class="text-danger">*</span></label>
                        <input type="text" 
                               id="addReportRoute" 
                               name="route" 
                               class="form-control bg-light" 
                               required
                               readonly
                               placeholder="Route will be automatically filled">
                        <div class="form-text text-muted"><i class="fas fa-info-circle me-1"></i> This field is automatically filled based on the selected operation</div>
                    </div>

                    <!-- File Upload Section -->
                    <div class="mb-4">
                        <label class="form-label d-block mb-2">Documentation Photos</label>
                        
                        <!-- Before Picture -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fas fa-camera text-primary"></i>
                                <label for="addProofBefore" class="form-label mb-0">Before Picture</label>
                            </div>
                            <input type="file" 
                                   id="addProofBefore" 
                                   name="proof_before" 
                                   accept="image/*" 
                                   class="form-control"
                                   aria-describedby="beforePictureHelp">
                            <div id="beforePictureHelp" class="form-text">Take a photo before starting the operation</div>
                        </div>

                        <!-- After Picture -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fas fa-camera text-success"></i>
                                <label for="addProofAfter" class="form-label mb-0">After Picture</label>
                            </div>
                            <input type="file" 
                                   id="addProofAfter" 
                                   name="proof_after" 
                                   accept="image/*" 
                                   class="form-control"
                                   aria-describedby="afterPictureHelp">
                            <div id="afterPictureHelp" class="form-text">Take a photo after completing the operation</div>
                        </div>
                    </div>

                    <!-- Remarks Textarea -->
                    <div class="mb-3">
                        <label for="addRemarks" class="form-label">Remarks</label>
                        <textarea id="addRemarks" 
                                  name="remarks" 
                                  class="form-control" 
                                  rows="3"
                                  placeholder="Add any additional notes or observations"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-success" id="addReportSubmit">
                    <i class="fas fa-check me-1"></i> Submit Report
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Operation Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <dl class="row">
                    <dt class="col-4">Route</dt>
                    <dd class="col-8" id="detailsRoute">—</dd>

                    <dt class="col-4">Date &amp; Time</dt>
                    <dd class="col-8" id="detailsDate">—</dd>

                    <dt class="col-4">Operation Type</dt>
                    <dd class="col-8" id="detailsType">—</dd>

                    <dt class="col-4">Status</dt>
                    <dd class="col-8" id="detailsStatus">—</dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Operations data (from PHP/backend)
const operationsData = {
    all: <?= $stats['operations']['all'] ?? 0 ?>,
    collection: <?= $stats['operations']['collection'] ?? 0 ?>,
    sweeping: <?= $stats['operations']['sweeping'] ?? 0 ?>,
    flushing: <?= $stats['operations']['flushing'] ?? 0 ?>,
    deClogging: <?= $stats['operations']['deClogging'] ?? 0 ?>,
    cleanup: <?= $stats['operations']['cleanup'] ?? 0 ?>
};

// Modal and FAB handling (Bootstrap 5)
document.addEventListener('DOMContentLoaded', function() {
    const addReportButton = document.getElementById('addReportButton');
    const modalEl = document.getElementById('addReportModal');
    let bsModal = null;
    if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        try { bsModal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true }); } catch(e) { console.warn('Bootstrap modal init failed', e); }
    }
    // expose for form submit handler
    window.__addReportBsModal = bsModal;

    // Ensure the FAB is a direct child of <body> so it's never clipped by parent stacking contexts
    try {
        if (addReportButton && addReportButton.parentElement !== document.body) {
            document.body.appendChild(addReportButton);
        }
    } catch (e) {
        console.warn('Could not move FAB to body:', e);
    }

    if (addReportButton) {
        addReportButton.addEventListener('click', function() {
            if (bsModal && typeof bsModal.show === 'function') bsModal.show();
        });
    }

    // Add Report submit button handler
    const addReportSubmitBtn = document.getElementById('addReportSubmit');
    if (addReportSubmitBtn) {
        addReportSubmitBtn.addEventListener('click', function(e){
            e.preventDefault();
            handleAddReportSubmit();
        });
    }
});

// Submit new report implementation
function handleAddReportSubmit(e) {
    // Prevent default form submission
    if (e) e.preventDefault();
    
    // Get form and do validation
    const form = document.getElementById('addReportForm');
    if (!form) {
        console.error('Add Report form not found');
        return;
    }

    // Add was-validated class to show validation feedback
    form.classList.add('was-validated');

    // Check form validity
    if (!form.checkValidity()) {
        // Focus first invalid field
        const firstInvalid = form.querySelector(':invalid');
        if (firstInvalid) firstInvalid.focus();
        return;
    }

    // Get form data
    const opId = document.getElementById('addReportOpId')?.value || '';
    const route = document.getElementById('addReportRoute')?.value || '';
    const beforeInput = document.getElementById('addProofBefore');
    const afterInput = document.getElementById('addProofAfter');
    const remarks = document.getElementById('addRemarks')?.value || '';

    // Create FormData object
    const fd = new FormData();
    if (opId) fd.append('operation_id', opId);
    fd.append('route', route);
    fd.append('remarks', remarks);
    
    // Add files if selected
    if (beforeInput?.files?.[0]) {
        fd.append('proof_before', beforeInput.files[0]);
    }
    if (afterInput?.files?.[0]) {
        fd.append('proof_after', afterInput.files[0]);
    }

    // Disable submit button and show loading state
    const submitBtn = document.getElementById('addReportSubmit');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Submitting...';
    }

    // Send to server
    fetch('/api/reports', {
        method: 'POST',
        body: fd
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        // Add to DOM optimistically
        addReportToDom({
            type: 'Operation Report',
            datetime: new Date().toISOString(),
            route
        }, true);

        // Show success message
        const toast = new bootstrap.Toast(document.getElementById('successToast'));
        if (toast) toast.show();

        // Close modal and reset form
        const modal = bootstrap.Modal.getInstance(document.getElementById('addReportModal'));
        if (modal) modal.hide();
        
        form.reset();
        form.classList.remove('was-validated');
    })
    .catch(error => {
        console.error('Error submitting report:', error);
        alert('Failed to submit report. Please try again.');
    })
    .finally(() => {
        // Re-enable submit button
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check me-1"></i> Submit Report';
        }
    });
}

function addReportToDom(report, prepend = true){
    // Build card showing only Operation Type, Date & Time, Route/Area
    const list = document.getElementById('todayReportsList');
    if (!list) return;

    const card = document.createElement('div');
    card.className = 'report-card';

    // Format date/time for display
    let displayDt = report.datetime;
    try {
        const d = new Date(report.datetime);
        displayDt = d.toLocaleString();
    } catch (e) {}

    card.innerHTML = `
        <div class="report-info">
            <h4>${escapeHtml(report.type)}</h4>
            <div class="report-meta">
                <span><i class="fas fa-clock"></i> ${escapeHtml(displayDt)}</span>
                <span><i class="fas fa-map-marker-alt"></i> ${escapeHtml(report.route)}</span>
            </div>
        </div>
        <div class="report-date">${escapeHtml(displayDt.split(',')[0] || displayDt)}</div>
    `;

    if (prepend && list.firstChild) list.insertBefore(card, list.firstChild);
    else list.appendChild(card);

    // Optional: animate new card
    card.style.transform = 'translateY(-6px)';
    card.style.opacity = '0';
    setTimeout(() => { card.style.transition = 'all 220ms ease'; card.style.transform = ''; card.style.opacity = '1'; }, 20);
}

// Open Add Report modal prefilled for a specific operation
function openAddReportForOperation(opId, opType, route){
    console.log('openAddReportForOperation called', {opId, opType, route});
    try{
        const opIdEl = document.getElementById('addReportOpId');
        const routeEl = document.getElementById('addReportRoute');
        if(opIdEl) opIdEl.value = opId || '';
        if(routeEl) routeEl.value = route || '';

        // Prefer cached modal instance
        const modalEl = document.getElementById('addReportModal');
        const bs = window.__addReportBsModal || (typeof bootstrap !== 'undefined' && modalEl ? bootstrap.Modal.getInstance(modalEl) : null);
        if(bs && typeof bs.show === 'function') {
            bs.show();
        } else {
            console.warn('Bootstrap modal not available for Add Report — falling back to alert');
            alert('Add Report for:\nRoute: ' + (route||'—') + '\nOperation: ' + (opType||'—') + '\nOperation ID: ' + (opId||'—'));
        }
    }catch(err){ console.error('openAddReportForOperation error', err); alert('Error opening Add Report: '+err.message); }
}

// For testing only: simple alert-based handlers (no complex modal)
function openDetailsModal(opId, route, displayDt, opType, status){
    console.log('openDetailsModal called', {opId, route, displayDt, opType, status});
    try{
        const detailsRoute = document.getElementById('detailsRoute');
        const detailsDate = document.getElementById('detailsDate');
        const detailsType = document.getElementById('detailsType');
        const detailsStatus = document.getElementById('detailsStatus');
        if(detailsRoute) detailsRoute.textContent = route || '—';
        if(detailsDate) detailsDate.textContent = displayDt || '—';
        if(detailsType) detailsType.textContent = opType || '—';
        if(detailsStatus) detailsStatus.textContent = status || '—';

        const modalEl = document.getElementById('detailsModal');
        const dm = (typeof bootstrap !== 'undefined' && modalEl) ? (bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl)) : null;
        if(dm && typeof dm.show === 'function') dm.show();
        else {
            console.warn('Bootstrap modal not available for Details — falling back to alert');
            alert(['Route: ' + (route||'—'), 'Date & Time: ' + (displayDt||'—'), 'Type: ' + (opType||'—'), 'Status: ' + (status||'—')].join('\n'));
        }
    }catch(err){ console.error('openDetailsModal error', err); alert('Error opening Details: '+err.message); }
}

function escapeHtml(str){
    if (!str) return '';
    return String(str).replace(/[&<>\"']/g, function(s){
        return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'})[s];
    });
}

// Screen size indicator update
function updateScreenSizeIndicator(){
    const el = document.getElementById('screenSizeIndicator');
    if(!el) return;
    const w = window.innerWidth;
    if(w >= 1024) el.textContent = 'Desktop ('+w+'px)';
    else if (w >= 768) el.textContent = 'Tablet ('+w+'px)';
    else el.textContent = 'Mobile ('+w+'px)';
}
window.addEventListener('resize', updateScreenSizeIndicator);
document.addEventListener('DOMContentLoaded', updateScreenSizeIndicator);
// Expose functions to global scope for inline onclick handlers
try{
    window.openDetailsModal = openDetailsModal;
    window.openAddReportForOperation = openAddReportForOperation;
}catch(e){ /* ignore */ }
</script>

<!-- Success Toast -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-check-circle me-2"></i> Report submitted successfully!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- Modal and Z-index Styling -->
<style>
    /* Ensure modal properly covers everything */
    .modal-backdrop {
        z-index: 1048 !important;
    }
    .modal {
        z-index: 1049 !important;
    }
</style>

<!-- Modal Styling -->
<style>
    /* File input styling */
    .form-control[type="file"] {
        padding: 0.375rem 0.75rem;
        line-height: 1.5;
    }

    /* Custom file input feedback */
    .form-control[type="file"]:not(:invalid) {
        border-color: #198754;
    }

    /* Toast styling */
    .toast-container {
        z-index: 1056;
    }
    
    /* Form validation styling */
    .form-control:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    }
    
    .was-validated .form-control:valid {
        border-color: #198754;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    
    .was-validated .form-control:invalid {
        border-color: #dc3545;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
</style>

<?php
$content = ob_get_clean();

$additionalStyles = ' <link rel="stylesheet" href="/assets/css/admin-dashboard.css">';

require base_path('views/layout.php');