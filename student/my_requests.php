<?php
require_once '../auth/check_auth.php';
require_role('student');
require_once '../config/database.php';

$student_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT r.tracking_code, r.details, r.status, r.admin_remarks, r.created_at, c.category_name, c.department
    FROM student_requests r
    JOIN request_categories c ON r.category_id = c.id
    WHERE r.student_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$student_id]);
$requests = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">My Requests</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Tracking Code</th>
                                <th>Category</th>
                                <th>Department</th>
                                <th>Date Submitted</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($requests) > 0): ?>
                                <?php foreach ($requests as $req): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($req['tracking_code']) ?></td>
                                        <td><?= htmlspecialchars($req['category_name']) ?></td>
                                        <td><?= htmlspecialchars($req['department']) ?></td>
                                        <td><?= htmlspecialchars($req['created_at']) ?></td>
                                        <td>
                                            <?php 
                                            $badge = 'secondary';
                                            if ($req['status'] == 'Approved') $badge = 'success';
                                            if ($req['status'] == 'Pending') $badge = 'warning';
                                            if ($req['status'] == 'Declined') $badge = 'danger';
                                            if ($req['status'] == 'Completed') $badge = 'primary';
                                            if ($req['status'] == 'In Process') $badge = 'info';
                                            ?>
                                            <span class="badge bg-<?= $badge ?>"><?= htmlspecialchars($req['status']) ?></span>
                                        </td>
                                        <td><?= htmlspecialchars($req['admin_remarks'] ?? '') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No requests found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

