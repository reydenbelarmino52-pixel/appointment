<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Appointment & Request System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-top: 70px; background-color: #f8f9fa; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
  <div class="container">
    <a class="navbar-brand" href="/flutter/appointment/index.php">SARS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] === 'student'): ?>
                <li class="nav-item"><a class="nav-link" href="/flutter/appointment/student/dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="/flutter/appointment/student/book_appoinment.php">Book Appointment</a></li>
                <li class="nav-item"><a class="nav-link" href="/flutter/appointment/student/my_appointments.php">My Appointments</a></li>
                <li class="nav-item"><a class="nav-link" href="/flutter/appointment/student/submit_request.php">Submit Request</a></li>
                <li class="nav-item"><a class="nav-link" href="/flutter/appointment/student/my_requests.php">My Requests</a></li>
            <?php elseif ($_SESSION['role'] === 'staff'): ?>
                <li class="nav-item"><a class="nav-link" href="/flutter/appointment/staff/dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="/flutter/appointment/staff/manage_schedules.php">Schedules</a></li>
                <li class="nav-item"><a class="nav-link" href="/flutter/appointment/staff/appointments.php">Appointments</a></li>
                <li class="nav-item"><a class="nav-link" href="/flutter/appointment/staff/requests.php">Requests</a></li>
            <?php elseif ($_SESSION['role'] === 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="/flutter/appointment/admin/dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="/flutter/appointment/admin/manage_users.php">Users</a></li>
                <li class="nav-item"><a class="nav-link" href="/flutter/appointment/admin/manage_categories.php">Categories</a></li>
                <li class="nav-item"><a class="nav-link" href="/flutter/appointment/admin/all_appointments.php">All Appointments</a></li>
                <li class="nav-item"><a class="nav-link" href="/flutter/appointment/admin/all_requests.php">All Requests</a></li>
            <?php endif; ?>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav">
        <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                    <?= htmlspecialchars($_SESSION['full_name'] ?? 'User') ?> (<?= ucfirst($_SESSION['role']) ?>)
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="/flutter/appointment/auth/logout.php">Logout</a></li>
                </ul>
            </li>
        <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="/flutter/appointment/auth/login.php">Login</a></li>
            <li class="nav-item"><a class="nav-link" href="/flutter/appointment/auth/register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container">

