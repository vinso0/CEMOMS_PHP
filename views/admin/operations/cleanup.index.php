<?php
$title = 'Cleanup Drives - Operations';
$pageTitle = 'Cleanup Drives';

ob_start();
?>
<div class="operation-text">
    <h2>Cleanup Drives</h2>
</div>
<?php
$content = ob_get_clean();
require base_path('views/layout.php');