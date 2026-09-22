<?php
require_once '../auth/check_auth.php';
require_role('staff');
require_once '../config/database.php';

$staff_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $req_id = (int)$_POST['request_id'];
    $status = $_POST['status']; // Approved, In Process, Completed, Declined
    $remarks = trim($_POST['remarks']);
    
    $update = $pdo->prepare("UPDATE student_requests SET status = ?, admin_remarks = ?, assigned_staff_id = ? WHERE id = ?");
    $update->execute([$status, $remarks, $staff_id, $req_id]);
    $message = "Request updated.";
}

// Fetch all requests (Staff can generally see all requests and process them, or they could be filtered by department. Here we show all for simplicity, or we can filter by staff department if we added that logic.)
$stmt = $pdo->prepare("
    SELECT r.id, r.tracking_code, r.details, r.status, r.admin_remarks, r.created_at, 
           c.category_name, u.full_name as student_name, u.student_id_or_emp_id
    FROM student_requests r
    JOIN request_categories c ON r.category_id = c.id
    JOIN users u ON r.student_id = u.id
    ORDER BY r.created_at DESC
");
$stmt->execute();
$requests = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-info text-white">Manage Student Requests</div>
            <div class="card-body">
                <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Code & Category</th>
                            <th>Details</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($requests as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['student_name']) ?> <br><small><?= htmlspecialchars($r['student_id_or_emp_id']) ?></small></td>
                            <td><?= htmlspecialchars($r['tracking_code']) ?> <br><span class="badge bg-secondary"><?= htmlspecialchars($r['category_name']) ?></span></td>
                            <td><?= htmlspecialchars(substr($r['details'], 0, 50)) ?>...</td>
                            <td><?= htmlspecialchars($r['status']) ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modal-req-<?= $r['id'] ?>">Process</button>
                                
                                <!-- Modal -->
                                <div class="modal fade" id="modal-req-<?= $r['id'] ?>" tabindex="-1">
                                  <div class="modal-dialog">
                                    <form class="modal-content" method="POST">
                                      <div class="modal-header">
                                        <h5 class="modal-title">Process Request</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                      </div>
                                      <div class="modal-body">
                                        <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                                        <div class="mb-3">
                                            <p><strong>Details:</strong><br><?= nl2br(htmlspecialchars($r['details'])) ?></p>
                                        </div>
                                        <div class="mb-3">
                                            <label>Status</label>
                                            <select name="status" class="form-select">
                                                <option value="Pending" <?= $r['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="Approved" <?= $r['status'] == 'Approved' ? 'selected' : '' ?>>Approve</option>
                                                <option value="In Process" <?= $r['status'] == 'In Process' ? 'selected' : '' ?>>In Process</option>
                                                <option value="Completed" <?= $r['status'] == 'Completed' ? 'selected' : '' ?>>Complete</option>
                                                <option value="Declined" <?= $r['status'] == 'Declined' ? 'selected' : '' ?>>Decline</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Remarks</label>
                                            <textarea name="remarks" class="form-control"><?= htmlspecialchars($r['admin_remarks'] ?? '') ?></textarea>
                                        </div>
                                      </div>
                                      <div class="modal-footer">
                                        <button type="submit" class="btn btn-info">Save changes</button>
                                      </div>
                                    </form>
                                  </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

