<?php
$title = 'Edit User - CEMOMS';
$pageTitle = 'Edit User';
$pageSubtitle = 'Modify user information and roles';

require base_path('views/admin/partials/navbar.php');
require base_path('views/admin/partials/sidebar.php');

ob_start();
?>

<div class="content-header">
    <div style="display: flex; align-items: center; gap: 15px;">
        <a href="/admin/users" class="btn-secondary" style="background: #6c757d; color: white; text-decoration: none; padding: 8px 12px; border-radius: 6px;">
            <i class="fas fa-arrow-left"></i>
            Back to Users
        </a>
        <div>
            <h1 class="content-title">Edit User</h1>
            <p class="content-subtitle">Update user information and permissions</p>
        </div>
    </div>
</div>

<!-- Edit User Form -->
<div class="form-card">
    <div class="section-header">
        <h2 class="section-title">User Information</h2>
    </div>
    
    <form action="/admin/users/update" method="POST">
        <input type="hidden" name="id" value="<?= $user['id'] ?>">
        
        <div class="form-grid">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       required 
                       value="<?= htmlspecialchars($user['username']) ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       required 
                       value="<?= htmlspecialchars($user['email']) ?>">
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="">Select Role</option>
                    <option value="personnel" <?= $user['role'] === 'personnel' ? 'selected' : '' ?>>Personnel</option>
                    <option value="foreman" <?= $user['role'] === 'foreman' ? 'selected' : '' ?>>Foreman</option>
                </select>
            </div>
        </div>
        
        <?php if (!empty($errors)) : ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($errors as $error) : ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i>
                Update User
            </button>
            <a href="/admin/users" class="btn-secondary" style="background: #6c757d; color: white; text-decoration: none; padding: 10px 20px; border-radius: 8px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-times"></i>
                Cancel
            </a>
        </div>
    </form>
</div>

<!-- User Details Card -->
<div class="users-table-container">
    <div class="table-header">
        <h2 class="section-title">Current User Details</h2>
    </div>
    <div style="padding: 25px;">
        <div class="user-detail-grid">
            <div class="detail-item">
                <label>User ID</label>
                <span><?= $user['id'] ?></span>
            </div>
            <div class="detail-item">
                <label>Username</label>
                <span><?= htmlspecialchars($user['username']) ?></span>
            </div>
            <div class="detail-item">
                <label>Email Address</label>
                <span><?= htmlspecialchars($user['email']) ?></span>
            </div>
            <div class="detail-item">
                <label>Current Role</label>
                <span class="role-badge <?= $user['role'] === 'foreman' ? 'role-foreman' : 'role-personnel' ?>">
                    <?= strtoupper($user['role']) ?>
                </span>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

$additionalScripts = '
<style>
    .user-detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .detail-item label {
        font-weight: 600;
        color: #666;
        font-size: 0.9rem;
    }

    .detail-item span {
        color: #2c3e50;
        font-weight: 500;
    }

    .btn-secondary {
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: #5a6268 !important;
        transform: translateY(-1px);
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .content-header > div {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
    }
</style>

<script>
    // Form validation
    document.querySelector(\'form\').addEventListener(\'submit\', function(e) {
        const username = document.getElementById(\'username\').value.trim();
        const email = document.getElementById(\'email\').value.trim();
        const role = document.getElementById(\'role\').value;

        if (!username || !email || !role) {
            e.preventDefault();
            alert(\'Please fill in all required fields.\');
            return;
        }

        // Email validation
        const emailRegex = /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert(\'Please enter a valid email address.\');
            return;
        }
    });
</script>
';

