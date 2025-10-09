<?php
$title = 'Flushing - Operations';
$pageTitle = 'Flushing';

ob_start();
?>
<div class="operation-text">
    <h2>Flushing</h2>
</div>
<?php
$content = ob_get_clean();
require base_path('views/layout.php');