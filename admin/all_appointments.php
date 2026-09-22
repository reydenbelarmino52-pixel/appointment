<?php
require_once '../auth/check_auth.php';
require_role('admin');
require_once '../config/database.php';

$search = $_GET['search'] ?? '';

$query = "
    SELECT a.appointment_code, a.purpose, a.status, a.created_at, 
           s.available_date, s.start_time, 
           u_student.full_name as student_name, 
           u_staff.full_name as staff_name
    FROM appointments a
    JOIN staff_schedules s ON a.schedule_id = s.id
    JOIN users u_student ON a.student_id = u_student.id
    JOIN users u_staff ON s.staff_id = u_staff.id
";

if ($search) {
    $query .= " WHERE a.appointment_code LIKE :search OR u_student.full_name LIKE :search ";
}

$query .= " ORDER BY a.created_at DESC";

$stmt = $pdo->prepare($query);

if ($search) {
    $stmt->execute(['search' => "%$search%"]);
} else {
    $stmt->execute();
}

$appointments = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Appointments (System-wide)</h5>
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
                        <th>Code</th>
                        <th>Student</th>
                        <th>Staff</th>
                        <th>Date/Time</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($appointments as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['appointment_code']) ?></td>
                        <td><?= htmlspecialchars($a['student_name']) ?></td>
                        <td><?= htmlspecialchars($a['staff_name']) ?></td>
                        <td><?= htmlspecialchars($a['available_date']) ?> <?= htmlspecialchars($a['start_time']) ?></td>
                        <td><?= htmlspecialchars($a['status']) ?></td>
                        <td><?= htmlspecialchars($a['created_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

