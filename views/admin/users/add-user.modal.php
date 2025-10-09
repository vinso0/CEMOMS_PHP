<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Foreman</h5>
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
                        <label for="role" class="form-label">Foreman Role</label>
                        <select class="form-select" id="role" name="role" required>
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
                        <i class="fas fa-save"></i> Add Foreman
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>