<?php
require_once '../auth/check_auth.php';
require_role('student');
require_once '../config/database.php';

$student_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT a.appointment_code, a.purpose, a.status, a.admin_remarks, s.available_date, s.start_time, s.end_time, u.full_name as staff_name 
    FROM appointments a
    JOIN staff_schedules s ON a.schedule_id = s.id
    JOIN users u ON s.staff_id = u.id
    WHERE a.student_id = ?
    ORDER BY a.created_at DESC
");
$stmt->execute([$student_id]);
$appointments = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">My Appointments</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Staff</th>
                                <th>Schedule</th>
                                <th>Purpose</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($appointments) > 0): ?>
                                <?php foreach ($appointments as $apt): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($apt['appointment_code']) ?></td>
                                        <td><?= htmlspecialchars($apt['staff_name']) ?></td>
                                        <td><?= htmlspecialchars($apt['available_date']) ?> <br> <small><?= htmlspecialchars($apt['start_time']) ?> - <?= htmlspecialchars($apt['end_time']) ?></small></td>
                                        <td><?= htmlspecialchars($apt['purpose']) ?></td>
                                        <td>
                                            <?php 
                                            $badge = 'secondary';
                                            if ($apt['status'] == 'Approved') $badge = 'success';
                                            if ($apt['status'] == 'Pending') $badge = 'warning';
                                            if ($apt['status'] == 'Declined' || $apt['status'] == 'Cancelled') $badge = 'danger';
                                            if ($apt['status'] == 'Completed') $badge = 'info';
                                            ?>
                                            <span class="badge bg-<?= $badge ?>"><?= htmlspecialchars($apt['status']) ?></span>
                                        </td>
                                        <td><?= htmlspecialchars($apt['admin_remarks'] ?? '') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No appointments found.</td>
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

