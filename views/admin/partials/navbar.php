<!-- Top Navbar -->
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
        <div class="user-avatar">
            <?= isset($username) ? strtoupper(substr($username, 0, 1)) : 'A' ?>
        </div>
        <span>Welcome, <?= isset($username) ? htmlspecialchars($username) : 'Admin' ?></span>
    </div>
</nav>