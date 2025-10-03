<?php
$title = 'Garbage Collection - Operations';
$pageTitle = 'Garbage Collection';

ob_start();
?>
<div class="operation-text">
    <h2>Garbage Collection</h2>
    <p>This is the Garbage Collection operation page.</p>
</div>
<?php
$content = ob_get_clean();
require base_path('views/layout.php');