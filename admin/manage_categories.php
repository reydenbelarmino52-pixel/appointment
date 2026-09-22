<?php
require_once '../auth/check_auth.php';
require_role('admin');
require_once '../config/database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['category_name']);
    $dept = trim($_POST['department']);
    $desc = trim($_POST['description']);
    
    $insert = $pdo->prepare("INSERT INTO request_categories (category_name, department, description) VALUES (?, ?, ?)");
    if($insert->execute([$name, $dept, $desc])) {
        $message = "Category added.";
    }
}

$stmt = $pdo->query("SELECT * FROM request_categories ORDER BY category_name ASC");
$categories = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">Add Category</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-2">
                        <label>Category Name</label>
                        <input type="text" name="category_name" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Department</label>
                        <input type="text" name="department" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-info text-white w-100">Add Category</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-secondary text-white">Service Categories</div>
            <div class="card-body p-0">
                <?php if ($message): ?><div class="alert alert-success m-2"><?= htmlspecialchars($message) ?></div><?php endif; ?>
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($categories as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['category_name']) ?></td>
                            <td><?= htmlspecialchars($c['department']) ?></td>
                            <td><?= htmlspecialchars($c['description']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

