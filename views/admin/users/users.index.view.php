<?php
$title = 'Users Management - CEMOMS';
$pageTitle = 'Users Management';

ob_start();
?>

<div class="section-header">
    <h2 class="section-title">Foreman Users List</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="fas fa-plus"></i> Add New Foreman
    </button>
</div>

<!-- Users Table -->
<div class="users-table-container">
    <div class="table-header">
        <h2 class="section-title">Existing Foremen</h2>
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
                            <span class="role-badge role-foreman">
                                <?= htmlspecialchars($user['role']) ?>
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
                    <td colspan="4" class="text-center py-4">
                        <div class="empty-state">
                            <i class="fas fa-users mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                            <h5>No foremen found</h5>
                            <p>Click "Add New Foreman" to create your first foreman account.</p>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// Include modals
require base_path('views/admin/users/add-user.modal.php');
require base_path('views/admin/users/edit-user.modal.php');
require base_path('views/admin/users/delete-user.modal.php');
?>

<script>
function populateEditModal(user) {
    document.getElementById('edit-user-id').value = user.id;
    document.getElementById('edit-username').value = user.username;
    document.getElementById('edit-email').value = user.email;
    document.getElementById('edit-role').value = user.foreman_role_id;
}

function populateDeleteModal(user) {
    document.getElementById('delete-user-id').value = user.id;
    document.getElementById('delete-username').textContent = user.username;
}
</script>

<?php
$content = ob_get_clean();
$additionalScripts = '<script src="/assets/js/users.js"></script>';
$additionalStyles = '<link rel="stylesheet" href="/assets/css/modal.css">';

require base_path('views/layout.php');