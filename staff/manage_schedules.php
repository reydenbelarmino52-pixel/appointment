<?php
require_once '../auth/check_auth.php';
require_role('staff');
require_once '../config/database.php';

$staff_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Handle add schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $date = $_POST['available_date'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];
    $max = (int)$_POST['max_slots'];

    if (empty($date) || empty($start) || empty($end) || $max < 1) {
        $error = "Invalid input.";
    } else {
        $insert = $pdo->prepare("INSERT INTO staff_schedules (staff_id, available_date, start_time, end_time, max_slots) VALUES (?, ?, ?, ?, ?)");
        if ($insert->execute([$staff_id, $date, $start, $end, $max])) {
            $message = "Schedule added successfully.";
        } else {
            $error = "Failed to add schedule.";
        }
    }
}

// Handle cancel schedule
if (isset($_GET['cancel'])) {
    $id_to_cancel = (int)$_GET['cancel'];
    // Verify it belongs to staff
    $check = $pdo->prepare("SELECT id FROM staff_schedules WHERE id = ? AND staff_id = ?");
    $check->execute([$id_to_cancel, $staff_id]);
    if ($check->rowCount() > 0) {
        $update = $pdo->prepare("UPDATE staff_schedules SET status = 'cancelled' WHERE id = ?");
        $update->execute([$id_to_cancel]);
        header("Location: manage_schedules.php?msg=cancelled");
        exit();
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'cancelled') {
    $message = "Schedule cancelled successfully.";
}

// Fetch all future schedules for this staff
$stmt = $pdo->prepare("SELECT * FROM staff_schedules WHERE staff_id = ? AND available_date >= CURDATE() ORDER BY available_date ASC, start_time ASC");
$stmt->execute([$staff_id]);
$schedules = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">Add Schedule Slot</div>
            <div class="card-body">
                <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-2">
                        <label>Date</label>
                        <input type="date" name="available_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="mb-2">
                        <label>Start Time</label>
                        <input type="time" name="start_time" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>End Time</label>
                        <input type="time" name="end_time" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Max Slots</label>
                        <input type="number" name="max_slots" class="form-control" required min="1" value="1">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add Slot</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-secondary text-white">My Upcoming Schedules</div>
            <div class="card-body p-0">
                <?php if ($message): ?><div class="alert alert-success m-2"><?= htmlspecialchars($message) ?></div><?php endif; ?>
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Slots</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($schedules as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['available_date']) ?></td>
                            <td><?= htmlspecialchars($s['start_time']) ?> - <?= htmlspecialchars($s['end_time']) ?></td>
                            <td><?= $s['max_slots'] ?></td>
                            <td><?= ucfirst($s['status']) ?></td>
                            <td>
                                <?php if($s['status'] === 'available'): ?>
                                    <a href="?cancel=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Cancel</a>
                                <?php endif; ?>
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

