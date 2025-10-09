<!-- Sidebar -->
<link rel="stylesheet" href="/assets/css/layout.css">
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <img src="/assets/images/CEMD-Logo.png" alt="CEMOMS Logo" class="logo-image">
        </div>
        <span class="sidebar-title">CEMOMS</span>
    </div>
    
    <nav class="sidebar-nav">
        <?php
        $userType = $_SESSION['user_type'] ?? 'admin';
        $baseUrl = ($userType === 'admin') ? '/admin' : '/foreman';
        ?>
        
        <div class="nav-item">
            <a href="<?= $baseUrl ?>" class="nav-link <?= urlIs($baseUrl) ? 'active' : '' ?>">
                <img src="/assets/images/dashboard_icon.png" alt="Dashboard Icon" class="nav-icon">
                Dashboard
            </a>
        </div>
        
        <?php if ($userType === 'admin'): ?>
            <!-- Admin-only menu items -->
            <div class="nav-item">
                <a href="/admin/reports" class="nav-link <?= urlIs('/admin/reports') ? 'active' : '' ?>">
                    <img src="/assets/images/reports_icon.png" alt="Reports Icon" class="nav-icon">
                    Reports
                </a>
            </div>
            
            <div class="nav-item has-submenu">
                <button class="nav-link submenu-toggle" onclick="toggleSubmenu(event)">
                    <img src="/assets/images/operations_icon.png" alt="Operations Icon" class="nav-icon">
                    Operations Management
                    <span class="submenu-arrow">&#9662;</span>
                </button>
                <div class="sidebar-submenu" style="display: none;">
                    <a class="submenu-link" href="/admin/operations/collection">Garbage Collection</a>
                    <a class="submenu-link" href="/admin/operations/sweeping">Street Sweeping</a>
                    <a class="submenu-link" href="/admin/operations/flushing">Flushing</a>
                    <a class="submenu-link" href="/admin/operations/de-clogging">De-clogging</a>
                    <a class="submenu-link" href="/admin/operations/cleanup">Cleanup Drives</a>
                </div>
            </div>
            
            <div class="nav-item">
                <a href="/admin/users" class="nav-link <?= (urlIs('/admin/users') || urlIs('/admin/users/edit')) ? 'active' : '' ?>">
                    <img src="/assets/images/users_icon.png" alt="Users Icon" class="nav-icon">
                    Users Management
                </a>
            </div>
            
            <div class="nav-item">
                <a href="/admin/settings" class="nav-link <?= urlIs('/admin/settings') ? 'active' : '' ?>">
                    <img src="/assets/images/settings_icon.png" alt="Settings Icon" class="nav-icon">
                    Settings
                </a>
            </div>
        <?php else: ?>
            <!-- Foreman menu items -->
            <div class="nav-item">
                <a href="/foreman/reports" class="nav-link <?= urlIs('/foreman/reports') ? 'active' : '' ?>">
                    <img src="/assets/images/reports_icon.png" alt="Reports Icon" class="nav-icon">
                    My Reports
                </a>
            </div>
            
            <div class="nav-item">
                <a href="/foreman/submit-report" class="nav-link <?= urlIs('/foreman/submit-report') ? 'active' : '' ?>">
                    <img src="/assets/images/operations_icon.png" alt="Submit Icon" class="nav-icon">
                    Submit Report
                </a>
            </div>
            
            <div class="nav-item">
                <a href="/foreman/profile" class="nav-link <?= urlIs('/foreman/profile') ? 'active' : '' ?>">
                    <img src="/assets/images/users_icon.png" alt="Profile Icon" class="nav-icon">
                    My Profile
                </a>
            </div>
        <?php endif; ?>
    </nav>
    
    <div class="logout-section">
        <a href="/logout" class="logout-link">
            <img src="/assets/images/logout_icon.png" alt="Logout Icon" class="logout-icon">
            Logout
        </a>
    </div>
</aside>

<script>
function toggleSubmenu(event) {
    event.preventDefault();
    const btn = event.currentTarget;
    const submenu = btn.parentElement.querySelector('.sidebar-submenu');
    if (!submenu) return;
    if (submenu.style.display === 'block') {
        submenu.style.height = submenu.scrollHeight + 'px';
        setTimeout(() => {
            submenu.style.height = '0px';
            submenu.style.overflow = 'hidden';
        }, 10);
        setTimeout(() => {
            submenu.style.display = 'none';
        }, 300);
    } else {
        submenu.style.display = 'block';
        submenu.style.height = '0px';
        submenu.style.overflow = 'hidden';
        setTimeout(() => {
            submenu.style.height = submenu.scrollHeight + 'px';
        }, 10);
        setTimeout(() => {
            submenu.style.height = '';
            submenu.style.overflow = '';
        }, 300);
    }
}
</script>

<style>
.has-submenu .submenu-toggle {
    width: 100%;
    background: none;
    border: none;
    color: inherit;
    text-align: left;
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    cursor: pointer;
    font-size: 1rem;
}
.has-submenu .submenu-arrow {
    margin-left: auto;
    font-size: 0.9em;
    transition: transform 0.3s;
}
.has-submenu .sidebar-submenu {
    padding-left: 2.5rem;
    display: none;
    flex-direction: column;
    transition: height 0.3s;
    overflow: hidden;
}
.has-submenu .sidebar-submenu a.submenu-link {
    color: #fff;
    padding: 0.5rem 0;
    text-decoration: none;
    display: block;
    font-size: 0.97rem;
    border-left: 2px solid transparent;
    transition: background 0.2s, border-color 0.2s;
}
.has-submenu .sidebar-submenu a.submenu-link:hover {
    background: #172018ff;
    border-left: 2px solid #f5f5f5ff;
}
</style>