<?php
$title = 'De-clogging - Operations';
$pageTitle = 'De-clogging';

ob_start();
?>
<div class="operation-text">
    <h2>De-clogging</h2>
    <p>This is the De-clogging operation page.</p>
</div>
<?php
$content = ob_get_clean();
require base_path('views/layout.php');