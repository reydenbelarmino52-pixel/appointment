<?php
require_once '../auth/check_auth.php';
require_role('admin');
require_once '../config/database.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_user') {
        $id = trim($_POST['id_num']);
        $name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $role = $_POST['role']; // staff or admin
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $check = $pdo->prepare("SELECT id FROM users WHERE student_id_or_emp_id = ? OR email = ?");
        $check->execute([$id, $email]);
        if ($check->rowCount() > 0) {
            $error = "User ID or Email already exists.";
        } else {
            $insert = $pdo->prepare("INSERT INTO users (student_id_or_emp_id, full_name, email, password_hash, role) VALUES (?, ?, ?, ?, ?)");
            $insert->execute([$id, $name, $email, $password, $role]);
            $message = ucfirst($role) . " added successfully.";
        }
    }
}

$stmt = $pdo->query("SELECT id, student_id_or_emp_id, full_name, email, role, department, created_at FROM users ORDER BY role ASC, full_name ASC");
$users = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">Add Staff / Admin</div>
            <div class="card-body">
                <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="action" value="add_user">
                    <div class="mb-2">
                        <label>Employee ID</label>
                        <input type="text" name="id_num" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Full Name</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Role</label>
                        <select name="role" class="form-select" required>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add User</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-secondary text-white">All Users</div>
            <div class="card-body p-0">
                <?php if ($message): ?><div class="alert alert-success m-2"><?= htmlspecialchars($message) ?></div><?php endif; ?>
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($users as $u): ?>
                            <tr>
                                <td><?= htmlspecialchars($u['student_id_or_emp_id']) ?></td>
                                <td><?= htmlspecialchars($u['full_name']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><span class="badge bg-<?= $u['role']=='admin'?'danger':($u['role']=='staff'?'success':'primary') ?>"><?= ucfirst($u['role']) ?></span></td>
                                <td><?= date('Y-m-d', strtotime($u['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

