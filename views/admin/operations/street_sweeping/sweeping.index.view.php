<?php
$title = 'Street Sweeping - Operations';
$pageTitle = 'Street Sweeping';

ob_start();
?>
<div class="operation-text">
    <h2>Street Sweeping</h2>
</div>
<?php
$content = ob_get_clean();
require base_path('views/layout.php');