<?php
$title = 'Account - Foreman';
$pageTitle = 'Account';
ob_start();
?>

<div class="card">
    <div style="padding:12px 16px;">
        <h4 style="margin:0 0 8px 0;"><i class="fas fa-user"></i> Account</h4>
        <p style="margin:0 0 12px 0; color:#666;">Manage your account settings</p>

        <a href="/logout" class="btn btn-danger" style="margin-top:12px;">Logout</a>
    </div>
</div>

<?php
$content = ob_get_clean();
require base_path('views/layout.php');
?>