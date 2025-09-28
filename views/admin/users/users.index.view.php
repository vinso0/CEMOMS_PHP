<?php
$title = 'Users Management - CEMOMS';
$pageTitle = 'Users Management';
$pageSubtitle = 'Manage personnel and foreman accounts';

ob_start();
?>
<div class="content-header">
    <h1 class="content-title">Users Management</h1>
</div>

<div class="section-header">
    <h2 class="section-title">Users List</h2>
    <button class="btn-primary" onclick="showAddForm()">
        <i class="fas fa-plus"></i> Add New User
    </button>
</div>

<!-- Add User Form (Initially hidden) -->
<div class="form-card" id="add-user-form" style="display: none;">
    <h3 style="margin-bottom: 20px;">Add New User</h3>
    <form method="POST" action="/admin/users">
        <div class="form-grid">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="">Select Role</option>
                    <option value="personnel">Personnel</option>
                    <option value="foreman">Foreman</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Add User
            </button>
            <button type="button" class="btn-secondary" onclick="hideAddForm()">
                <i class="fas fa-times"></i> Cancel
            </button>
        </div>
    </form>
</div>

<!-- Users Table -->
<div class="users-table-container">
    <div class="table-header">
        <h2 class="section-title">Existing Users</h2>
    </div>
    <table class="users-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <span class="role-badge role-<?= htmlspecialchars($user['role']) ?>">
                                <?= ucfirst(htmlspecialchars($user['role'])) ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="/admin/users/edit?id=<?= $user['id'] ?>" class="btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" action="/admin/users/delete" style="display: inline;">
                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                    <button type="submit" class="btn-delete" onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">
                        No users found. Click "Add New User" to create your first user.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
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

<script>
    function showAddForm() {
        document.getElementById("add-user-form").style.display = "block";
    }
    
    function hideAddForm() {
        document.getElementById("add-user-form").style.display = "none";
    }
</script>
';

require base_path('views/admin/layout.php');