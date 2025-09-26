<?php
$title = 'User Management - CEMOMS';
$pageTitle = 'User Management';
$pageSubtitle = 'Manage system users and their roles';

require base_path('views/admin/partials/navbar.php');
require base_path('views/admin/partials/sidebar.php');

ob_start();
?>

<div class="content-header">
    <h1 class="content-title">User Management</h1>
    <p class="content-subtitle">Manage system users and their roles</p>
</div>

<!-- Add User Section -->
<div class="form-card">
    <div class="section-header">
        <h2 class="section-title">Add New User</h2>
    </div>
    
    <form action="/admin/users" method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       required 
                       value="<?= htmlspecialchars($old['username'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       required 
                       value="<?= htmlspecialchars($old['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="">Select Role</option>
                    <option value="personnel" <?= (isset($old['role']) && $old['role'] === 'personnel') ? 'selected' : '' ?>>Personnel</option>
                    <option value="foreman" <?= (isset($old['role']) && $old['role'] === 'foreman') ? 'selected' : '' ?>>Foreman</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
        </div>
        
        <?php if (!empty($errors)) : ?>
            <div class="error-message">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($errors as $error) : ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <button type="submit" class="btn-primary">
            <i class="fas fa-plus"></i>
            Add User
        </button>
    </form>
</div>

<!-- Users List Section -->
<div class="users-table-container">
    <div class="section-header" style="padding: 20px 25px;">
        <h2 class="section-title">User Accounts</h2>
        <a href="#" class="btn-primary" onclick="showAddUserForm()">
            <i class="fas fa-plus"></i>
            Add User
        </a>
    </div>
    
    <table class="users-table">
        <thead>
            <tr>
                <th>Email</th>
                <th>Username</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($users) === 0) : ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 40px; color: #666;">
                        <i class="fas fa-users" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                        <br>No users found.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td>
                            <span class="role-badge <?= $user['role'] === 'foreman' ? 'role-foreman' : 'role-personnel' ?>">
                                <?= strtoupper($user['role']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="/admin/users/edit?id=<?= $user['id'] ?>" class="btn-edit">
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <form action="/admin/users/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                    <button type="submit" class="btn-delete">
                                        <i class="fas fa-trash"></i>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();

$additionalScripts = '
<style>
    .d-inline {
        display: inline;
    }
    
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        
        .action-buttons {
            flex-direction: column;
            gap: 5px;
        }
        
        .btn-edit, .btn-delete {
            font-size: 0.7rem;
            padding: 4px 8px;
        }
    }
</style>

<script>
    function showAddUserForm() {
        const formCard = document.querySelector(\'.form-card\');
        formCard.scrollIntoView({ behavior: \'smooth\' });
        document.getElementById(\'username\').focus();
    }

    // Form validation
    document.querySelector(\'form\').addEventListener(\'submit\', function(e) {
        const username = document.getElementById(\'username\').value.trim();
        const email = document.getElementById(\'email\').value.trim();
        const role = document.getElementById(\'role\').value;
        const password = document.getElementById(\'password\').value;

        if (!username || !email || !role || !password) {
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

        // Password validation
        if (password.length < 6) {
            e.preventDefault();
            alert(\'Password must be at least 6 characters long.\');
            return;
        }
    });
</script>
';