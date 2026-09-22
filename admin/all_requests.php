<?php
require_once '../auth/check_auth.php';
require_role('admin');
require_once '../config/database.php';

$search = $_GET['search'] ?? '';

$query = "
    SELECT r.tracking_code, r.status, r.created_at, 
           c.category_name, 
           u_student.full_name as student_name,
           u_staff.full_name as staff_name
    FROM student_requests r
    JOIN request_categories c ON r.category_id = c.id
    JOIN users u_student ON r.student_id = u_student.id
    LEFT JOIN users u_staff ON r.assigned_staff_id = u_staff.id
";

if ($search) {
    $query .= " WHERE r.tracking_code LIKE :search OR u_student.full_name LIKE :search ";
}

$query .= " ORDER BY r.created_at DESC";

$stmt = $pdo->prepare($query);

if ($search) {
    $stmt->execute(['search' => "%$search%"]);
} else {
    $stmt->execute();
}

$requests = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="card">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Requests (System-wide)</h5>
        <form method="GET" class="d-flex">
            <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Search code/student" value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-sm btn-light">Search</button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Tracking Code</th>
                        <th>Student</th>
                        <th>Category</th>
                        <th>Staff Assigned</th>
                        <th>Status</th>
                        <th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($requests as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['tracking_code']) ?></td>
                        <td><?= htmlspecialchars($r['student_name']) ?></td>
                        <td><?= htmlspecialchars($r['category_name']) ?></td>
                        <td><?= htmlspecialchars($r['staff_name'] ?? 'Unassigned') ?></td>
                        <td><?= htmlspecialchars($r['status']) ?></td>
                        <td><?= htmlspecialchars($r['created_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

