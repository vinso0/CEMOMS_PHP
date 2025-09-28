<h2>Delete User</h2>
<p>Are you sure you want to delete user <strong><?= htmlspecialchars($user['username']) ?></strong>?</p>

<form method="POST" action="/admin/users/delete">
    <input type="hidden" name="id" value="<?= $user['id'] ?>">
    <button type="submit" class="btn-delete">Yes, Delete</button>
    <a href="/admin/users" class="btn-secondary">Cancel</a>
</form>


