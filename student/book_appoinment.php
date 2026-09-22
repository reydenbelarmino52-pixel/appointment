<?php
require_once '../auth/check_auth.php';
require_role('student');
require_once '../config/database.php';

$student_id = $_SESSION['user_id'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schedule_id = (int)$_POST['schedule_id'];
    $purpose     = trim($_POST['purpose']);
    
    // 1. Check if the schedule slot is still available
    $stmt = $pdo->prepare("
        SELECT s.max_slots, COUNT(a.id) AS booked_count 
        FROM staff_schedules s
        LEFT JOIN appointments a ON s.id = a.schedule_id AND a.status IN ('Pending', 'Approved')
        WHERE s.id = ? AND s.status = 'available'
        GROUP BY s.id
    ");
    $stmt->execute([$schedule_id]);
    $slot = $stmt->fetch();

    if (!$slot || $slot['booked_count'] >= $slot['max_slots']) {
        $error = "Selected slot is full or unavailable. Please pick another date/time.";
    } else {
        // 2. Prevent the same student from double-booking the same slot
        $checkDup = $pdo->prepare("SELECT id FROM appointments WHERE student_id = ? AND schedule_id = ? AND status != 'Cancelled'");
        $checkDup->execute([$student_id, $schedule_id]);

        if ($checkDup->rowCount() > 0) {
            $error = "You have already booked this slot.";
        } else {
            // 3. Create appointment
            $appointment_code = "APT-" . strtoupper(bin2hex(random_bytes(4)));
            $insert = $pdo->prepare("
                INSERT INTO appointments (appointment_code, student_id, schedule_id, purpose, status) 
                VALUES (?, ?, ?, ?, 'Pending')
            ");
            $insert->execute([$appointment_code, $student_id, $schedule_id, $purpose]);
            $message = "Appointment requested successfully! Tracking Code: " . htmlspecialchars($appointment_code);
        }
    }
}

// Fetch available schedules
$schedules_stmt = $pdo->query("
    SELECT s.id, s.available_date, s.start_time, s.end_time, s.max_slots, u.full_name as staff_name,
    (SELECT COUNT(*) FROM appointments a WHERE a.schedule_id = s.id AND a.status IN ('Pending', 'Approved')) as booked
    FROM staff_schedules s
    JOIN users u ON s.staff_id = u.id
    WHERE s.status = 'available' AND s.available_date >= CURDATE()
    HAVING booked < s.max_slots
    ORDER BY s.available_date ASC, s.start_time ASC
");
$schedules = $schedules_stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4>Book an Appointment</h4>
            </div>
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="schedule_id" class="form-label">Available Time Slots</label>
                        <select name="schedule_id" id="schedule_id" class="form-select" required>
                            <option value="">-- Select a slot --</option>
                            <?php foreach ($schedules as $sch): ?>
                                <option value="<?= $sch['id'] ?>">
                                    <?= htmlspecialchars($sch['available_date']) ?> 
                                    (<?= htmlspecialchars($sch['start_time']) ?> - <?= htmlspecialchars($sch['end_time']) ?>) 
                                    - <?= htmlspecialchars($sch['staff_name']) ?>
                                    [<?= $sch['max_slots'] - $sch['booked'] ?> slots left]
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="purpose" class="form-label">Purpose of Appointment</label>
                        <textarea name="purpose" id="purpose" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Booking</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>