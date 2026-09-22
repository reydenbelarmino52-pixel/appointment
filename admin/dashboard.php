<?php
require_once '../auth/check_auth.php';
require_role('admin');
require_once '../config/database.php';

// Quick stats
$stats = [
    'students' => $pdo->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn(),
    'staff' => $pdo->query("SELECT COUNT(*) FROM users WHERE role='staff'")->fetchColumn(),
    'appointments' => $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn(),
    'requests' => $pdo->query("SELECT COUNT(*) FROM student_requests")->fetchColumn()
];

require_once '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col">
        <h2>Administrator Dashboard</h2>
        <p class="text-muted">System Overview</p>
    </div>
</div>

<div class="row text-center">
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body py-5">
                <h1 class="display-4"><?= $stats['students'] ?></h1>
                <h5>Registered Students</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body py-5">
                <h1 class="display-4"><?= $stats['staff'] ?></h1>
                <h5>Staff Members</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white h-100">
            <div class="card-body py-5">
                <h1 class="display-4"><?= $stats['appointments'] ?></h1>
                <h5>Total Appointments</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body py-5">
                <h1 class="display-4"><?= $stats['requests'] ?></h1>
                <h5>Total Requests</h5>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Quick Actions</h5>
                <a href="manage_users.php" class="btn btn-outline-primary m-1">Manage Users</a>
                <a href="manage_categories.php" class="btn btn-outline-info m-1">Manage Categories</a>
                <a href="all_appointments.php" class="btn btn-outline-secondary m-1">View All Appointments</a>
                <a href="all_requests.php" class="btn btn-outline-secondary m-1">View All Requests</a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

