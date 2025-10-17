<?php
$title = 'Notifications - Foreman';
$pageTitle = 'Notifications';
ob_start();
?>

<div class="card">
    <div style="padding:12px 16px;">
        <h4 style="margin:0 0 8px 0;"><i class="fas fa-bell"></i> Notifications</h4>
        <p style="margin:0 0 12px 0; color:#666;">System notices and messages</p>

        <?php if (!empty($notifications)): ?>
            <div class="notifications-list">
                <?php foreach ($notifications as $n): ?>
                    <div class="notification-item" style="padding:10px 8px; border-bottom:1px solid #eee;">
                        <strong><?= htmlspecialchars($n['title']) ?></strong>
                        <div style="font-size:0.9rem; color:#555; margin-top:4px;"><?= htmlspecialchars($n['body']) ?></div>
                        <div style="font-size:0.8rem; color:#999; margin-top:6px;"><?= htmlspecialchars($n['time']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center">No notifications</p>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require base_path('views/layout.php');
?>