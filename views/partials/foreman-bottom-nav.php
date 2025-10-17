<?php
// Bottom nav for foreman mobile-like layout
?>
<style>
.foreman-bottom-nav{
    position:fixed;
    left:0;
    right:0;
    bottom:0;
    height:62px;
    background:#fff;
    border-top:1px solid #e6e6e6;
    display:flex;
    justify-content:space-around;
    align-items:center;
    z-index:9999;
}
.foreman-bottom-nav .nav-item{display:flex;flex-direction:column;align-items:center;font-size:12px;color:#444}
.foreman-bottom-nav .nav-item .icon{font-size:20px;margin-bottom:4px}
.body-with-foreman-pad{padding-bottom:72px}

/* Account panel styles */
.foreman-account-panel {
    position: fixed;
    bottom: 62px;
    left: 0;
    right: 0;
    background: #fff;
    border-top: 1px solid #e6e6e6;
    box-shadow: 0 -2px 6px rgba(0, 0, 0, 0.1);
    padding: 12px 16px;
    display: none;
    z-index: 10000;
}
.foreman-account-panel.active {
    display: block;
}
</style>

<div class="foreman-bottom-nav" id="foremanBottomNav">
    <div class="nav-item" data-target="/foreman" onclick="foremanNavClick(event)">
        <div class="icon"><i class="fas fa-home"></i></div>
        <div class="label">Home</div>
    </div>
    <div class="nav-item" data-target="/foreman/reports" onclick="foremanNavClick(event)">
        <div class="icon"><i class="fas fa-file-alt"></i></div>
        <div class="label">Reports</div>
    </div>
    <div class="nav-item" data-target="/foreman/notifications" onclick="foremanNavClick(event)">
        <div class="icon"><i class="fas fa-bell"></i></div>
        <div class="label">Messages</div>
    </div>
    <div class="nav-item" data-target="/foreman/account" onclick="foremanNavClick(event)">
        <div class="icon"><i class="fas fa-user"></i></div>
        <div class="label">Account</div>
    </div>
</div>

<div class="foreman-account-panel" id="foremanAccountPanel">
    <h4 style="margin:0 0 8px 0;"><i class="fas fa-user"></i> Account</h4>
    <p style="margin:0 0 12px 0; color:#666;">Manage your account settings</p>

    <a href="/logout" class="btn btn-danger" style="margin-top:12px;">Logout</a>
</div>

<script>
// Keep some bottom padding so content isn't hidden behind nav
(function(){
    document.documentElement.classList.add('body-with-foreman-pad');
    document.body.classList.add('body-with-foreman-pad');
})();

function foremanNavClick(e){
    var target = e.currentTarget.getAttribute('data-target');
    if (!target) return;
    // small client-side navigation: just change location
    window.location.href = target;
}

function toggleAccountPanel() {
    const panel = document.getElementById('foremanAccountPanel');
    if (panel) {
        panel.classList.toggle('active');
    }
}
</script>