<?php
$title = 'Users Management - CEMOMS';
$pageTitle = 'Users Management';

ob_start();
?>

<div class="section-header">
    <h2 class="section-title">Users List</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="fas fa-plus"></i> Add New User
    </button>
</div>

<!-- Users Table -->
<div class="users-table-container">
    <div class="table-header">
        <h2 class="section-title">Existing Users</h2>
    </div>
    <table class="users-table">
        <thead>
            <tr>
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
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <span class="role-badge role-<?= htmlspecialchars($user['role']) ?>">
                                <?= ucfirst(htmlspecialchars($user['role'])) ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-warning" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editUserModal"
                                        onclick="populateEditModal(<?= htmlspecialchars(json_encode($user)) ?>)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteUserModal"
                                        onclick="populateDeleteModal(<?= htmlspecialchars(json_encode($user)) ?>)">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <div class="empty-state">
                            <i class="fas fa-users mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                            <h5>No users found</h5>
                            <p>Click "Add New User" to create your first user account.</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/admin/users">
                <div class="modal-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
            
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Select Role</option>
                            <?php if (!empty($roles)): ?>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= htmlspecialchars($role['id']) ?>">
                                        <?= htmlspecialchars($role['role_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="foreman">Foreman</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="mb-3 password-container">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="6">
                        <button type="button" class="password-toggle-add" onclick="togglePassword()">
                            <i class="fas fa-eye" id="password-icon"></i>
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Add User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/admin/users/update">
                <div class="modal-body">
                    <input type="hidden" id="edit-user-id" name="id">
                    
                    <div class="mb-3">
                        <label for="edit-username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="edit-username" name="username" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit-email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit-email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit-role" class="form-label">Role</label>
                        <select class="form-select" id="edit-role" name="role" required>
                            <option value="">Select Role</option>
                            <?php if (!empty($roles)): ?>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= htmlspecialchars($role['id']) ?>">
                                        <?= htmlspecialchars($role['role_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="personnel">Personnel</option>
                                <option value="foreman">Foreman</option>
                                <option value="admin">Admin</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3 password-container">
                        <label for="edit-password" class="form-label">New Password (optional)</label>
                        <input type="password" class="form-control" id="edit-password" name="password" minlength="6">
                        <button type="button" class="password-toggle-edit" onclick="togglePassword()">
                            <i class="fas fa-eye" id="password-icon"></i>
                        </button>
                        <div class="form-text">Leave blank to keep current password</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="fas fa-trash-alt text-danger" style="font-size: 3rem;"></i>
                </div>
                <h5 class="mb-3">Delete User?</h5>
                <p class="text-muted mb-3">
                    Are you sure you want to delete <strong id="delete-username"></strong>? 
                    This action cannot be undone.
                </p>
                <form method="POST" action="/admin/users/delete">
                    <input type="hidden" id="delete-user-id" name="id">
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function populateEditModal(user) {
    document.getElementById('edit-user-id').value = user.id || '';
    document.getElementById('edit-username').value = user.username || '';
    document.getElementById('edit-email').value = user.email || '';
    document.getElementById('edit-role').value = user.role || '';
    document.getElementById('edit-password').value = '';
}

function populateDeleteModal(user) {
    document.getElementById('delete-user-id').value = user.id || '';
    document.getElementById('delete-username').textContent = user.username || '';
}

function togglePassword() {
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('password-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordIcon.classList.remove('fa-eye');
        passwordIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        passwordIcon.classList.remove('fa-eye-slash');
        passwordIcon.classList.add('fa-eye');
    }
}
</script>

<?php
$content = ob_get_clean();

$additionalStyles = '<link rel="stylesheet" href="/assets/css/modal.css">';

require base_path('views/layout.php');