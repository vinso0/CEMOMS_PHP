<!-- Top Navbar -->
<link rel="stylesheet" href="/assets/css/layout.css">
<nav class="top-navbar">
    <div class="breadcrumb">
        <button class="mobile-menu-toggle" onclick="toggleSidebar()" style="display: none;">
            <i class="fas fa-bars"></i>
        </button>
        <div>
            <h1 class="page-title"><?= $pageTitle ?? 'Admin Page' ?></h1>
        </div>
    </div>
    <div class="user-profile">
        <span><?= isset($username) ? htmlspecialchars($username) : 'Admin' ?></span>
        <div class="user-avatar">
            <img src="/assets/images/users_avatar.png" alt="User Avatar" class="avatar-image">
        </div>
    </div>
</nav>