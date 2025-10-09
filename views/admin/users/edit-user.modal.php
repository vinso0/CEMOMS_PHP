<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Foreman</h5>
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
                        <label for="edit-role" class="form-label">Foreman Role</label>
                        <select class="form-select" id="edit-role" name="role" required>
                            <option value="">Select Role</option>
                            <?php if (!empty($roles)): ?>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= htmlspecialchars($role['id']) ?>">
                                        <?= htmlspecialchars($role['role_name']) ?>
                                    </option>
                                <?php endforeach; ?>
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
                        <i class="fas fa-save"></i> Update Foreman
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>