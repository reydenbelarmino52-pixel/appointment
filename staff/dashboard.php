<?php
require_once '../auth/check_auth.php';
require_role('staff');
require_once '../config/database.php';

$staff_id = $_SESSION['user_id'];

// Get today's schedules
$stmt = $pdo->prepare("
    SELECT id, start_time, end_time, max_slots 
    FROM staff_schedules 
    WHERE staff_id = ? AND available_date = CURDATE() AND status = 'available'
");
$stmt->execute([$staff_id]);
$todays_schedules = $stmt->fetchAll();

// Get pending appointments for this staff
$apt_stmt = $pdo->prepare("
    SELECT COUNT(*) as count 
    FROM appointments a
    JOIN staff_schedules s ON a.schedule_id = s.id
    WHERE s.staff_id = ? AND a.status = 'Pending'
");
$apt_stmt->execute([$staff_id]);
$pending_apts = $apt_stmt->fetch()['count'];

require_once '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?>!</h2>
        <p class="text-muted">Staff Dashboard</p>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Today's Schedule Overview</h5>
            </div>
            <div class="card-body">
                <?php if (count($todays_schedules) > 0): ?>
                    <ul class="list-group">
                        <?php foreach ($todays_schedules as $sch): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= htmlspecialchars($sch['start_time']) ?> - <?= htmlspecialchars($sch['end_time']) ?>
                                <span class="badge bg-info rounded-pill">Max Slots: <?= $sch['max_slots'] ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">No schedules set for today.</p>
                <?php endif; ?>
                <a href="manage_schedules.php" class="btn btn-sm btn-outline-primary mt-3">Manage Schedules</a>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card h-100 text-center">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Action Required</h5>
            </div>
            <div class="card-body d-flex flex-column justify-content-center">
                <h1 class="display-1 text-warning"><?= $pending_apts ?></h1>
                <p class="lead">Pending Appointments</p>
                <a href="appointments.php" class="btn btn-warning">Review Appointments</a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

