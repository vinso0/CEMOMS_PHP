<?php
$title = 'My Reports - CEMOMS';
$pageTitle = 'Reports';
ob_start();
?>

<style>
    :root{ --bg:#f9fafb; --card:#ffffff; --muted:#6b7280; }
    body { background: var(--bg); }
    .reports-wrap { max-width:1100px; margin:0 auto; padding:1rem; }
    .card { background: var(--card); border-radius:8px; box-shadow:0 6px 18px rgba(2,6,23,0.06); padding:14px; }
    .table-scroll { overflow-x:auto; }
    table.reports { width:100%; border-collapse:collapse; }
    table.reports thead th { text-align:left; padding:14px 18px; font-weight:600; color:#111827; background:#f5f6fa; }
    table.reports tbody tr { border-bottom:1px solid #e6e9ee; transition:background .12s ease; }
    table.reports tbody tr:hover { background: #fbfdff; }
    table.reports td { padding:12px 18px; vertical-align:middle; }
    .status-badge { display:inline-block; padding:6px 8px; border-radius:6px; font-size:13px; }
    .status-pending{ background:#fff7ed; color:#92400e; }
    .status-approved{ background:#ecfdf5; color:#065f46; }
    .status-rejected{ background:#fff1f2; color:#7f1d1d; }
    .btn { border:none; cursor:pointer; border-radius:6px; padding:8px 12px; font-size:14px; transition:transform .08s ease,opacity .08s ease; }
    .btn:active{ transform:translateY(1px); }
    .btn-edit{ background:#3b82f6; color:white; }
    .btn-edit:hover{ background:#2563eb; }
    .btn-delete{ background:#ef4444; color:white; }
    .btn-delete:hover{ background:#dc2626; }

    /* Mobile: transform rows into cards */
    @media (max-width: 767px){
        table.reports thead{ display:none; }
        table.reports, table.reports tbody, table.reports tr, table.reports td { display:block; width:100%; }
        table.reports tr { margin-bottom:12px; padding:12px; border-radius:8px; border:1px solid #e6e9ee; background:var(--card); box-shadow:0 2px 6px rgba(2,6,23,0.04); }
        table.reports td{ padding:8px 0; border:none; }
        table.reports td::before{ content: attr(data-label) ": "; font-weight:600; display:inline-block; width:120px; color:var(--muted); }
    }

    /* Modal common styles */
    .modal-backdrop{ position:fixed; inset:0; background:rgba(2,6,23,0.6); display:flex; align-items:flex-start; justify-content:center; z-index:1200; backdrop-filter:blur(4px); overflow-y:auto; padding:20px 0; }
    .modal-content{ background:var(--card); border-radius:8px; max-width:720px; width:94%; padding:18px; box-shadow:0 12px 30px rgba(2,6,23,0.18); margin:auto; }
    .modal-close{ float:right; background:transparent; border:none; font-size:22px; cursor:pointer; padding:4px 8px; }
    .modal-close:hover{ opacity:0.8; }
    .hidden{ display:none !important; }

    /* Proof modal */
    .proof-modal .images{ display:flex; gap:12px; }
    .proof-modal img{ width:100%; height:300px; object-fit:cover; border-radius:6px; border:1px solid #e6e9ee; }

    /* Edit modal */
    .edit-form{ display:flex; flex-direction:column; height:calc(100vh - 180px); max-height:800px; margin-top:16px; }
    .form-fields{ flex:1; overflow-y:auto; padding-right:8px; display:grid; gap:16px; }
    .form-fields::-webkit-scrollbar { width:8px; }
    .form-fields::-webkit-scrollbar-track { background:#f1f1f1; border-radius:4px; }
    .form-fields::-webkit-scrollbar-thumb { background:#d1d5db; border-radius:4px; }
    .form-fields::-webkit-scrollbar-thumb:hover { background:#9ca3af; }
    .form-group{ display:flex; flex-direction:column; gap:6px; }
    .form-group label{ font-weight:600; color:#374151; font-size:14px; }
    .form-group input[type="text"],
    .form-group input[type="datetime-local"],
    .form-group select,
    .form-group textarea{ 
        padding:8px 12px; 
        border:1px solid #d1d5db; 
        border-radius:6px;
        font-size:14px;
        transition: border-color .15s ease;
    }
    .form-group input[type="text"]:focus,
    .form-group input[type="datetime-local"]:focus,
    .form-group select:focus,
    .form-group textarea:focus{
        outline:none;
        border-color:#3b82f6;
        box-shadow:0 0 0 2px rgba(59,130,246,0.1);
    }
    .form-group textarea{ min-height:80px; resize:vertical; }
    .form-actions{ display:flex; gap:12px; justify-content:flex-end; padding-top:16px; border-top:1px solid #e5e7eb; margin-top:16px; }
    .readonly-field { background-color:#f3f4f6; cursor:not-allowed; opacity:0.8; }
    .form-group input[type="file"] { 
        padding:8px; 
        border:1px dashed #d1d5db; 
        border-radius:6px;
        background:#f9fafb;
        cursor:pointer;
        width:100%;
    }
    .form-group input[type="file"]:hover {
        border-color:#3b82f6;
        background:#f3f4f6;
    }
    .btn-primary{ background:#3b82f6; color:white; }
    .btn-primary:hover{ background:#2563eb; }
    .btn-secondary{ background:#9ca3af; color:white; }
    .btn-secondary:hover{ background:#6b7280; }
    .form-error{ color:#dc2626; font-size:13px; margin-top:2px; }

</style>

<div class="reports-wrap">
    <div class="card">
        <h3 style="margin:0 0 12px 0;">Reports Table</h3>
        <div class="table-scroll">
            <table class="reports" id="reportsTable">
                <thead>
                    <tr>
                        <th>Report ID</th>
                        <th>Date & Time</th>
                        <th>Operation Type</th>
                        <th>Route</th>
                        <th>Proof Picture</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="reportsBody">
                    <!-- rendered by JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Proof Modal -->
<div id="proofModal" class="modal-backdrop proof-modal hidden" onclick="if(event.target.id==='proofModal') closeProofModal()">
    <div class="modal-content" role="dialog" aria-modal="true" id="proofModalDialog">
        <button onclick="closeProofModal()" class="modal-close">&times;</button>
        <h4 style="margin-top:0;">Proof Pictures</h4>
        <div class="images">
            <div style="flex:1">
                <div style="font-size:12px;color:var(--muted);margin-bottom:6px;">Before</div>
                <img id="proofBeforeImg" src="" alt="Before" />
            </div>
            <div style="flex:1">
                <div style="font-size:12px;color:var(--muted);margin-bottom:6px;">After</div>
                <img id="proofAfterImg" src="" alt="After" />
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal-backdrop hidden" onclick="if(event.target.id==='editModal') closeEditModal()">
    <div class="modal-content" role="dialog" aria-modal="true" id="editModalDialog">
        <button onclick="closeEditModal()" class="modal-close">&times;</button>
        <h4 style="margin-top:0;">Edit Report</h4>
        <form id="editForm" onsubmit="handleEditSubmit(event)" class="edit-form">
            <input type="hidden" id="editReportId" name="reportId">
            <div class="form-fields">
                
                <div class="form-group">
                    <label for="editOperationType">Operation Type</label>
                    <select id="editOperationType" name="operationType" disabled class="readonly-field">
                        <option value="Garbage Collection">Garbage Collection</option>
                        <option value="Street Sweeping">Street Sweeping</option>
                        <option value="Flushing">Flushing</option>
                        <option value="De-clogging">De-clogging</option>
                        <option value="Cleanup">Cleanup</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editRoute">Route</label>
                    <input type="text" id="editRoute" name="route" readonly class="readonly-field">
                </div>
                <div class="form-group">
                    <label for="editProofBefore">Before Picture</label>
                    <input type="file" id="editProofBefore" name="proofBefore" accept="image/*">
                    <img id="editProofBeforePreview" src="" alt="" style="display:none;max-width:100%;margin-top:8px;border-radius:4px;">
                    <div class="form-error"></div>
                </div>
                <div class="form-group">
                    <label for="editProofAfter">After Picture</label>
                    <input type="file" id="editProofAfter" name="proofAfter" accept="image/*">
                    <img id="editProofAfterPreview" src="" alt="" style="display:none;max-width:100%;margin-top:8px;border-radius:4px;">
                    <div class="form-error"></div>
                </div>
                <div class="form-group">
                    <label for="editRemarks">Remarks (Optional)</label>
                    <textarea id="editRemarks" name="remarks"></textarea>
                    <div class="form-error"></div>
                </div>
                
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    // sample reports data
    let reports = [
        { reportId: 'RPT-001', dateTime: '2025-11-07 09:30', operationType: 'Garbage Collection', route: 'Route A-123', proofBefore: 'https://via.placeholder.com/800x600?text=Before+1', proofAfter: 'https://via.placeholder.com/800x600?text=After+1', remarks: 'Completed on schedule', status: 'approved' },
        { reportId: 'RPT-002', dateTime: '2025-11-07 10:45', operationType: 'Street Sweeping', route: 'Route B-456', proofBefore: 'https://via.placeholder.com/800x600?text=Before+2', proofAfter: 'https://via.placeholder.com/800x600?text=After+2', remarks: 'Delayed due to rain', status: 'pending' },
        { reportId: 'RPT-003', dateTime: '2025-11-06 14:20', operationType: 'Flushing', route: 'Route C-789', proofBefore: null, proofAfter: null, remarks: 'No incident', status: 'rejected' }
    ];

    const reportsBody = document.getElementById('reportsBody');

    function renderReports(){
        if(!reportsBody) return;
        reportsBody.innerHTML = '';
        if(reports.length === 0){
            const tr = document.createElement('tr');
            tr.className = 'block';
            const td = document.createElement('td');
            td.colSpan = 8;
            td.style.padding = '16px';
            td.style.textAlign = 'center';
            td.textContent = 'No reports found.';
            tr.appendChild(td);
            reportsBody.appendChild(tr);
            return;
        }

        reports.forEach((r, idx) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td data-label="Report ID">${escapeHtml(r.reportId)}</td>
                <td data-label="Date & Time">${escapeHtml(r.dateTime)}</td>
                <td data-label="Operation Type">${escapeHtml(r.operationType)}</td>
                <td data-label="Route">${escapeHtml(r.route)}</td>
                <td data-label="Proof Picture">${r.proofBefore || r.proofAfter ? `<button class="btn" onclick="viewProof(${idx})" style="background:transparent;color:#2563eb;padding:0;font-weight:600;text-decoration:underline;">View Proof</button>` : '<span style="color:#9ca3af;">No Proof</span>'}</td>
                <td data-label="Remarks">${escapeHtml(r.remarks)}</td>
                <td data-label="Status"><span class="status-badge status-${escapeHtml(r.status)}">${capitalize(r.status)}</span></td>
                <td data-label="Actions"><div style="display:flex;gap:8px;"><button class="btn btn-edit" onclick="handleEdit('${escapeAttr(r.reportId)}')">Edit</button><button class="btn btn-delete" onclick="handleDelete('${escapeAttr(r.reportId)}')">Delete</button></div></td>
            `;
            reportsBody.appendChild(tr);
        });
    }

    function viewProof(index){
        const r = reports[index];
        const before = document.getElementById('proofBeforeImg');
        const after = document.getElementById('proofAfterImg');
        before.src = r.proofBefore || 'https://via.placeholder.com/800x600?text=No+Image';
        after.src = r.proofAfter || 'https://via.placeholder.com/800x600?text=No+Image';
        document.getElementById('proofModal').classList.remove('hidden');
    }

    function closeProofModal(){
        document.getElementById('proofModal').classList.add('hidden');
    }

    function handleEdit(reportId){
        try {
            const report = reports.find(r => r.reportId === reportId);
            if(!report) return;

            // Fill form with report data (guard against missing fields)
            const reportIdEl = document.getElementById('editReportId');
            if(reportIdEl) reportIdEl.value = report.reportId || '';

            const opEl = document.getElementById('editOperationType');
            if(opEl) opEl.value = report.operationType || '';

            const routeEl = document.getElementById('editRoute');
            if(routeEl) routeEl.value = report.route || '';

            // Show existing images in preview
            const beforePreview = document.getElementById('editProofBeforePreview');
            if(beforePreview) {
                if(report.proofBefore) {
                    beforePreview.src = report.proofBefore;
                    beforePreview.style.display = 'block';
                } else {
                    beforePreview.style.display = 'none';
                }
            }

            const afterPreview = document.getElementById('editProofAfterPreview');
            if(afterPreview) {
                if(report.proofAfter) {
                    afterPreview.src = report.proofAfter;
                    afterPreview.style.display = 'block';
                } else {
                    afterPreview.style.display = 'none';
                }
            }

            const remarksEl = document.getElementById('editRemarks');
            if(remarksEl) remarksEl.value = report.remarks || '';

            // Clear any previous error messages
            document.querySelectorAll('.form-error').forEach(el => el.textContent = '');

            // Show modal
            const modal = document.getElementById('editModal');
            if(modal) modal.classList.remove('hidden');

            // Focus first available input
            const firstInput = opEl || routeEl || remarksEl;
            if(firstInput && typeof firstInput.focus === 'function') firstInput.focus();
        } catch (err) {
            // Log to console to aid debugging in browser
            console.error('handleEdit error:', err);
        }
    }

    function closeEditModal(){
        document.getElementById('editModal').classList.add('hidden');
        document.getElementById('editForm').reset();
    }

    function handleEditSubmit(event){
        event.preventDefault();

        // Clear previous errors
        document.querySelectorAll('.form-error').forEach(el => el.textContent = '');

        // Get form data
        const formData = new FormData(event.target);
        const data = Object.fromEntries(formData.entries());

        // No validation needed for read-only fields (operationType, route)
        let hasError = false;
        
        // File validation (if files selected)
        const beforeFile = document.getElementById('editProofBefore').files[0];
        const afterFile = document.getElementById('editProofAfter').files[0];
        
        if(beforeFile && !beforeFile.type.startsWith('image/')) {
            const errorEl = document.querySelector('#editProofBefore + .form-error');
            if(errorEl) errorEl.textContent = 'Please select an image file';
            hasError = true;
        }
        
        if(afterFile && !afterFile.type.startsWith('image/')) {
            const errorEl = document.querySelector('#editProofAfter + .form-error');
            if(errorEl) errorEl.textContent = 'Please select an image file';
            hasError = true;
        }        if(hasError) return;

    // No date field in form (removed)

        // Update report in the array
        const index = reports.findIndex(r => r.reportId === data.reportId);
        if(index === -1) return;

        reports[index] = {...reports[index], ...data};

        // Update table
        renderReports();

        // Close modal
        closeEditModal();
    }

    // Handle ESC key for modals
    document.addEventListener('keydown', function(event) {
        if(event.key === 'Escape') {
            if(!document.getElementById('proofModal').classList.contains('hidden')){
                closeProofModal();
            }
            if(!document.getElementById('editModal').classList.contains('hidden')){
                closeEditModal();
            }
        }
    });

    function handleDelete(reportId){
        if(!confirm('Are you sure you want to delete ' + reportId + '?')) return;
        reports = reports.filter(r => r.reportId !== reportId);
        renderReports();
    }

    function capitalize(s){ return (s||'').charAt(0).toUpperCase() + (s||'').slice(1); }
    function escapeHtml(str){ if(!str) return ''; return String(str).replace(/[&<>\"']/g, function(m){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','\"':'&quot;',"'":'&#39;'})[m]; }); }
    function escapeAttr(str){ return (str||'').replace(/'/g, "\\'"); }

    // File upload preview handlers
    document.getElementById('editProofBefore').addEventListener('change', function(e) {
        const preview = document.getElementById('editProofBeforePreview');
        if(preview && e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    document.getElementById('editProofAfter').addEventListener('change', function(e) {
        const preview = document.getElementById('editProofAfterPreview');
        if(preview && e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    document.addEventListener('DOMContentLoaded', function(){ renderReports(); });

</script>

<?php
$content = ob_get_clean();

$additionalStyles = ' <link rel="stylesheet" href="/assets/css/admin-dashboard.css">';

require base_path('views/layout.php');

?>