<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard.php");
    exit();
}
require '../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['trainer_name']);
    $spec = trim($_POST['specialization']);
    $contact = trim($_POST['contact_number']);
    $email = trim($_POST['email']);
    $hours = trim($_POST['working_hours']);
    $exp = trim($_POST['experience']);
    
    if (empty($name) || empty($spec) || empty($contact) || empty($email) || empty($hours) || empty($exp)) {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO trainers (trainer_name, specialization, contact_number, email, working_hours, experience) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $spec, $contact, $email, $hours, $exp);
        
        if ($stmt->execute()) {
            header("Location: index.php?msg=added");
            exit();
        } else {
            $error = "Error adding trainer: " . $conn->error;
        }
        $stmt->close();
    }
}

include '../includes/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <h1>Add New Trainer</h1>
        <a href="index.php" style="color: var(--text-secondary);">&larr; Back to Trainers</a>
    </div>

    <div class="form-container" style="max-width: 600px; margin: 0 auto;">
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Trainer Name</label>
                <input type="text" name="trainer_name" class="form-control" required placeholder="John Doe">
            </div>
            <div class="form-group">
                <label>Specialization</label>
                <input type="text" name="specialization" class="form-control" required placeholder="e.g. Weightlifting, Yoga">
            </div>
            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" name="contact_number" class="form-control" required placeholder="555-1234">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="john@gym.com">
            </div>
            <div class="form-group">
                <label>Working Hours</label>
                <input type="text" name="working_hours" class="form-control" required placeholder="06:00 AM - 02:00 PM">
            </div>
            <div class="form-group">
                <label>Experience</label>
                <input type="text" name="experience" class="form-control" required placeholder="e.g. 5 Years">
            </div>
            
            <button type="submit" class="btn-primary" style="margin-top: 1rem;">Add Trainer</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
