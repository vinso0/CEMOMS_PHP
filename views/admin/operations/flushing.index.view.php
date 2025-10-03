<?php
$title = 'Flushing - Operations';
$pageTitle = 'Flushing';

ob_start();
?>
<div class="operation-text">
    <h2>Flushing</h2>
    <p>This is the Flushing operation page.</p>
</div>
<?php
$content = ob_get_clean();
require base_path('views/layout.php');