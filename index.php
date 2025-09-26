<?php
require_once 'config.php';

// Handle CRUD operations for users (only personnel and foreman)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $username = $_POST['username'];
                $email = $_POST['email'];
                $role = $_POST['role']; // Only 'personnel' or 'foreman'
                $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                
                if (in_array($role, ['personnel', 'foreman'])) {
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$username, $email, $hashed_password, $role]);
                    $success = "User  added successfully.";
                } else {
                    $error = "Invalid role.";
                }
                break;

            case 'edit':
                $id = $_POST['id'];
                $username = $_POST['username'];
                $email = $_POST['email'];
                $role = $_POST['role'];
                
                if (in_array($role, ['personnel', 'foreman'])) {
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ? AND role IN ('personnel', 'foreman')");
                    $stmt->execute([$username, $email, $role, $id]);
                    $success = "User  updated successfully.";
                } else {
                    $error = "Invalid role or user not found.";
                }
                break;

            case 'delete':
                $id = $_POST['id'];
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role IN ('personnel', 'foreman')");
                $stmt->execute([$id]);
                $success = "User  deleted successfully.";
                break;
        }
    }
}

// Fetch users with personnel/foreman roles
$stmt = $pdo->prepare("SELECT id, username, email, role FROM users WHERE role IN ('personnel', 'foreman')");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Welcome, <?php echo $_SESSION['username']; ?></span>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar bg-secondary">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white" href="#dashboard">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white active" href="#users">Users Management</a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="content flex-grow-1 p-4">
            <h2>Users Management (Personnel & Foreman)</h2>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Add User Form -->
            <div class="card mb-4">
                <div class="card-header">Add New User</div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="add">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="username" placeholder="Username" required>
                            </div>
                            <div class="col-md-3">
                                <input type="email" class="form-control" name="email" placeholder="Email" required>
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="personnel">Personnel</option>
                                    <option value="foreman">Foreman</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="password" class="form-control" name="password" placeholder="Password" required>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Add</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card">
                <div class="card-header">Users List</div>
                <div class="card-body">
                    <table class="table table-striped">
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
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo ucfirst($user['role']); ?></td>
                                    <td>
                                        <!-- Edit Form (Inline) -->
                                        <form method="POST" class="d-inline" style="display: none;" id="edit-form-<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="edit">
                                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" class="form-control d-inline-block w-auto" required>
                                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-control d-inline-block w-auto" required>
                                            <select name="role" class="form-control d-inline-block w-auto" required>
                                                <option value="personnel" <?php echo $user['role'] === 'personnel' ? 'selected' : ''; ?>>Personnel</option>
                                                <option value="foreman" <?php echo $user['role'] === 'foreman' ? 'selected' : ''; ?>>Foreman</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-warning">Save</button>
                                        </form>
                                        <button class="btn btn-sm btn-info edit-btn" onclick="toggleEdit(<?php echo $user['id']; ?>)">Edit</button>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this user?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($users)): ?>
                                <tr><td colspan="5" class="text-center">No users found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleEdit(id) {
            const form = document.getElementById('edit-form-' + id);
            const btn = event.target;
            if (form.style.display === 'none') {
                form.style.display = 'inline';
                btn.textContent = 'Cancel';
                btn.onclick = () => toggleEdit(id); // Toggle back
            } else {
                form.style.display = 'none';
                btn.textContent = 'Edit';
                btn.onclick = () => toggleEdit(id);
            }
        }
    </script>
</body>
</html>
