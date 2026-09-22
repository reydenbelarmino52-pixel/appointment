<?php
require_once '../auth/check_auth.php';
require_role('student');
require_once '../config/database.php';
require_once '../includes/header.php';

$student_id = $_SESSION['user_id'];

// Get pending appointments
$stmt = $pdo->prepare("
    SELECT a.appointment_code, a.purpose, a.status, s.available_date, s.start_time, s.end_time 
    FROM appointments a
    JOIN staff_schedules s ON a.schedule_id = s.id
    WHERE a.student_id = ? AND a.status IN ('Pending', 'Approved')
    ORDER BY s.available_date ASC
");
$stmt->execute([$student_id]);
$appointments = $stmt->fetchAll();

// Get active requests
$req_stmt = $pdo->prepare("
    SELECT r.tracking_code, r.status, r.created_at, c.category_name
    FROM student_requests r
    JOIN request_categories c ON r.category_id = c.id
    WHERE r.student_id = ? AND r.status NOT IN ('Completed', 'Declined')
    ORDER BY r.created_at DESC
");
$req_stmt->execute([$student_id]);
$requests = $req_stmt->fetchAll();
?>

<div class="row mb-4">
    <div class="col">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?>!</h2>
        <p class="text-muted">Student Dashboard</p>
    </div>
</div>

<div class="row">
    <!-- Appointments -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Upcoming Appointments</h5>
                <a href="book_appoinment.php" class="btn btn-sm btn-light">Book New</a>
            </div>
            <div class="card-body">
                <?php if (count($appointments) > 0): ?>
                    <ul class="list-group">
                        <?php foreach ($appointments as $apt): ?>
                            <li class="list-group-item">
                                <strong>Code:</strong> <?= htmlspecialchars($apt['appointment_code']) ?><br>
                                <strong>Date/Time:</strong> <?= htmlspecialchars($apt['available_date']) ?> (<?= htmlspecialchars($apt['start_time']) ?> - <?= htmlspecialchars($apt['end_time']) ?>)<br>
                                <strong>Status:</strong> <span class="badge bg-<?= $apt['status'] == 'Approved' ? 'success' : 'warning' ?>"><?= htmlspecialchars($apt['status']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">No upcoming appointments.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Requests -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Active Requests</h5>
                <a href="submit_request.php" class="btn btn-sm btn-light">Submit Request</a>
            </div>
            <div class="card-body">
                <?php if (count($requests) > 0): ?>
                    <ul class="list-group">
                        <?php foreach ($requests as $req): ?>
                            <li class="list-group-item">
                                <strong>Track Code:</strong> <?= htmlspecialchars($req['tracking_code']) ?><br>
                                <strong>Request:</strong> <?= htmlspecialchars($req['category_name']) ?><br>
                                <strong>Status:</strong> <span class="badge bg-secondary"><?= htmlspecialchars($req['status']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">No active requests.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

