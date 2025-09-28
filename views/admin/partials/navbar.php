<!-- Top Navbar -->
<link rel="stylesheet" href="/assets/css/admin-styles.css">
<nav class="top-navbar">
    <div class="breadcrumb">
        <button class="mobile-menu-toggle" onclick="toggleSidebar()" style="display: none;">
            <i class="fas fa-bars"></i>
        </button>
        <div>
            <h1 class="page-title"><?= $pageTitle ?? 'Dashboard' ?></h1>
            <p class="page-subtitle"><?= $pageSubtitle ?? 'System overview' ?></p>
        </div>
    </div>
    <div class="user-profile">
        <span><?= isset($username) ? htmlspecialchars($username) : 'Admin' ?></span>
        <div class="user-avatar">
            <img src="/assets/images/users.png" alt="User Avatar" class="avatar-image">
        </div>
    </div>
</nav>