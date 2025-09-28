<?php
$title = 'Edit User - CEMOMS';
$pageTitle = 'Edit User';
$pageSubtitle = 'Update user information';

ob_start();
?>

<div class="content-header">
    <h1 class="content-title">Edit User</h1>
    <p class="content-subtitle">Update user information</p>
</div>

<div class="form-card">
    <?php if (!empty($errors)): ?>
        <div class="error-message" style="margin-bottom: 20px;">
            <i class="fas fa-exclamation-circle"></i>
            Please fix the following errors:
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="/admin/users/update">
        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
        
        <div class="form-grid">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" 
                       value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" 
                       value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="personnel" <?= $user['role'] === 'personnel' ? 'selected' : '' ?>>Personnel</option>
                    <option value="foreman" <?= $user['role'] === 'foreman' ? 'selected' : '' ?>>Foreman</option>
                </select>
            </div>
        </div>
        
        <div style="display: flex; gap: 10px; margin-top: 20px;">
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

$additionalScripts = '
<style>
    .btn-secondary {
        background: #6c757d;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
    }
</style>
';

require base_path('views/admin/layout.php');