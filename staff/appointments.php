<?php
require_once '../auth/check_auth.php';
require_role('staff');
require_once '../config/database.php';

$staff_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apt_id = (int)$_POST['appointment_id'];
    $status = $_POST['status']; // Approved, Declined, Completed
    $remarks = trim($_POST['remarks']);
    
    // Verify appointment belongs to this staff's schedule
    $check = $pdo->prepare("SELECT a.id FROM appointments a JOIN staff_schedules s ON a.schedule_id = s.id WHERE a.id = ? AND s.staff_id = ?");
    $check->execute([$apt_id, $staff_id]);
    
    if ($check->rowCount() > 0) {
        $update = $pdo->prepare("UPDATE appointments SET status = ?, admin_remarks = ? WHERE id = ?");
        $update->execute([$status, $remarks, $apt_id]);
        $message = "Appointment updated.";
    }
}

// Fetch all appointments for this staff
$stmt = $pdo->prepare("
    SELECT a.id, a.appointment_code, a.purpose, a.status, a.admin_remarks, s.available_date, s.start_time, u.full_name as student_name, u.student_id_or_emp_id
    FROM appointments a
    JOIN staff_schedules s ON a.schedule_id = s.id
    JOIN users u ON a.student_id = u.id
    WHERE s.staff_id = ?
    ORDER BY a.created_at DESC
");
$stmt->execute([$staff_id]);
$appointments = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">Student Appointments</div>
            <div class="card-body">
                <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Date / Time</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($appointments as $a): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['student_name']) ?> <br><small><?= htmlspecialchars($a['student_id_or_emp_id']) ?></small></td>
                            <td><?= htmlspecialchars($a['available_date']) ?> <br><small><?= htmlspecialchars($a['start_time']) ?></small></td>
                            <td><?= htmlspecialchars($a['purpose']) ?></td>
                            <td><?= htmlspecialchars($a['status']) ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-<?= $a['id'] ?>">Update</button>
                                
                                <!-- Modal -->
                                <div class="modal fade" id="modal-<?= $a['id'] ?>" tabindex="-1">
                                  <div class="modal-dialog">
                                    <form class="modal-content" method="POST">
                                      <div class="modal-header">
                                        <h5 class="modal-title">Update Appointment</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                      </div>
                                      <div class="modal-body">
                                        <input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
                                        <div class="mb-3">
                                            <label>Status</label>
                                            <select name="status" class="form-select">
                                                <option value="Approved" <?= $a['status'] == 'Approved' ? 'selected' : '' ?>>Approve</option>
                                                <option value="Declined" <?= $a['status'] == 'Declined' ? 'selected' : '' ?>>Decline</option>
                                                <option value="Completed" <?= $a['status'] == 'Completed' ? 'selected' : '' ?>>Complete</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Remarks</label>
                                            <textarea name="remarks" class="form-control"><?= htmlspecialchars($a['admin_remarks'] ?? '') ?></textarea>
                                        </div>
                                      </div>
                                      <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Save changes</button>
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

