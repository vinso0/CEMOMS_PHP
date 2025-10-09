<!-- Top Navbar -->
<link rel="stylesheet" href="/assets/css/layout.css">
<nav class="top-navbar">
    <div class="breadcrumb">
        <button class="mobile-menu-toggle" onclick="toggleSidebar()" style="display: none;">
            <i class="fas fa-bars"></i>
        </button>
        <div>
            <h1 class="page-title"><?= $pageTitle ?? 'Dashboard' ?></h1>
        </div>
    </div>
    <div class="user-profile">
        <?php
        // Display username for foremen, email for admin
        $displayName = '';
        if (isset($_SESSION['user_type'])) {
            if ($_SESSION['user_type'] === 'admin') {
                $displayName = $_SESSION['username'] ?? 'Admin';
            } else {
                $displayName = $_SESSION['username'] ?? 'User';
            }
        }
        ?>
        <span><?= htmlspecialchars($displayName) ?></span>
        <div class="user-avatar">
            <img src="/assets/images/users_avatar.png" alt="User Avatar" class="avatar-image">
        </div>
    </div>
</nav>