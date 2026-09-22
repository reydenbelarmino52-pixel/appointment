<?php
require_once 'database.php';

echo "<h2>Database Setup & Seeding</h2>";

try {
    // 1. Create Default Admin
    $admin_id = 'admin01';
    $email = 'admin@school.edu';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE role = 'admin'");
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        $insert = $pdo->prepare("INSERT INTO users (student_id_or_emp_id, full_name, email, password_hash, role) VALUES (?, ?, ?, ?, ?)");
        $insert->execute([$admin_id, 'System Administrator', $email, $password, 'admin']);
        echo "<p>Admin user created. Login with email: <b>admin@school.edu</b> and password: <b>admin123</b></p>";
    } else {
        echo "<p>Admin user already exists.</p>";
    }

    // 2. Default Request Categories
    $categories = [
        ['Transcript of Records', 'Registrar', 'Official academic transcript.'],
        ['Good Moral Certificate', 'Guidance Office', 'Certificate of good moral character.'],
        ['ID Replacement', 'Student Affairs', 'Request for a new ID card.'],
        ['Academic Consultation', 'Department', 'Consultation with academic staff.']
    ];

    foreach ($categories as $cat) {
        $stmt = $pdo->prepare("SELECT id FROM request_categories WHERE category_name = ?");
        $stmt->execute([$cat[0]]);
        if ($stmt->rowCount() == 0) {
            $insert = $pdo->prepare("INSERT INTO request_categories (category_name, department, description) VALUES (?, ?, ?)");
            $insert->execute($cat);
            echo "<p>Added category: {$cat[0]}</p>";
        }
    }
    
    echo "<p>Seeding complete.</p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}
?>

