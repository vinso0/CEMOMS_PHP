<?php
// Since we're now using modals, this file can be simplified or removed
// But keeping it for cases where direct edit links are used

$title = 'Edit User - CEMOMS';
$pageTitle = 'Edit User';

ob_start();
?>

<div class="content-header">
    <h1 class="content-title">Edit User</h1>
    <p class="content-subtitle">Update user information</p>
</div>

<div class="form-card">
    <?php if (!empty($errors)): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                Please fix the following errors:
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <form method="POST" action="/admin/users/update">
        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
        
        <div class="form-grid">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" 
                       value="<?= htmlspecialchars($user['username']) ?>" 
                       placeholder="Enter username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" 
                       value="<?= htmlspecialchars($user['email']) ?>" 
                       placeholder="Enter email address" required>
            </div>
        </div>
        
        <div class="form-grid">
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="">Select Role</option>
                    <?php if (!empty($roles)): ?>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= htmlspecialchars($role['id']) ?>" 
                                    <?= ($user['role'] == $role['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($role['role_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="personnel" <?= ($user['role'] == 'personnel') ? 'selected' : '' ?>>Personnel</option>
                        <option value="foreman" <?= ($user['role'] == 'foreman') ? 'selected' : '' ?>>Foreman</option>
                        <option value="admin" <?= ($user['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="password">New Password (optional)</label>
                <input type="password" id="password" name="password" 
                       placeholder="Leave blank to keep current password" minlength="6">
                <small style="color: #666; font-size: 0.85rem; margin-top: 5px; display: block;">
                    Leave blank to keep the current password
                </small>
            </div>
        </div>
        
        <div style="display: flex; gap: 15px; margin-top: 25px; flex-wrap: wrap;">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Update User
            </button>
            <a href="/admin/users" class="btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();

require base_path('views/layout.php');