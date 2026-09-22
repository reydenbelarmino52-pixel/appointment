<?php
require_once '../auth/check_auth.php';
require_role('student');
require_once '../config/database.php';

$student_id = $_SESSION['user_id'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = (int)$_POST['category_id'];
    $details     = trim($_POST['details']);
    
    if (empty($category_id) || empty($details)) {
        $error = "Please fill in all required fields.";
    } else {
        $tracking_code = "REQ-" . strtoupper(bin2hex(random_bytes(4)));
        $insert = $pdo->prepare("
            INSERT INTO student_requests (tracking_code, student_id, category_id, details, status) 
            VALUES (?, ?, ?, ?, 'Pending')
        ");
        
        if ($insert->execute([$tracking_code, $student_id, $category_id, $details])) {
            $message = "Request submitted successfully! Tracking Code: " . htmlspecialchars($tracking_code);
        } else {
            $error = "Failed to submit request.";
        }
    }
}

// Fetch categories
$cat_stmt = $pdo->query("SELECT * FROM request_categories ORDER BY category_name ASC");
$categories = $cat_stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">Submit a Service Request</h4>
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
                        <label for="category_id" class="form-label">Request Type</label>
                        <select name="category_id" id="category_id" class="form-select" required>
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['category_name']) ?> (<?= htmlspecialchars($cat['department']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="details" class="form-label">Request Details / Reason</label>
                        <textarea name="details" id="details" class="form-control" rows="4" required placeholder="Provide any specific details or requirements..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-info text-white">Submit Request</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

