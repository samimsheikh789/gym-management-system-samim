<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}
require 'includes/db.php';
$msg = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM trainers WHERE id = $id");
    $msg = "<div class='alert alert-success'>Trainer deleted successfully.</div>";
}

// Handle Add/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['trainer_name']);
    $hours = trim($_POST['working_hours']);
    
    if (isset($_POST['trainer_id']) && !empty($_POST['trainer_id'])) {
        // Update
        $id = intval($_POST['trainer_id']);
        $stmt = $conn->prepare("UPDATE trainers SET trainer_name = ?, working_hours = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $hours, $id);
        if ($stmt->execute()) {
            $msg = "<div class='alert alert-success'>Trainer updated successfully.</div>";
        }
        $stmt->close();
    } else {
        // Add
        $stmt = $conn->prepare("INSERT INTO trainers (trainer_name, working_hours) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $hours);
        if ($stmt->execute()) {
            $msg = "<div class='alert alert-success'>Trainer added successfully.</div>";
        }
        $stmt->close();
    }
}

// Fetch all trainers
$trainers_res = $conn->query("SELECT * FROM trainers ORDER BY id DESC");

// Check if Edit Mode
$edit_trainer = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM trainers WHERE id = $edit_id");
    if ($res->num_rows > 0) {
        $edit_trainer = $res->fetch_assoc();
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <h1>Trainer Management (Admin)</h1>
        <p style="color: var(--text-secondary);">Manage gym trainers and their working hours.</p>
    </div>
    
    <?php echo $msg; ?>

    <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
        <!-- Form Section -->
        <div class="form-container" style="flex: 1; margin: 0; min-width: 300px;">
            <h3 style="margin-bottom: 1rem; color: var(--gold);">
                <?php echo $edit_trainer ? 'Edit Trainer' : 'Add New Trainer'; ?>
            </h3>
            <form method="POST" action="trainers.php">
                <?php if($edit_trainer): ?>
                    <input type="hidden" name="trainer_id" value="<?php echo $edit_trainer['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Trainer Name</label>
                    <input type="text" name="trainer_name" class="form-control" required 
                           value="<?php echo $edit_trainer ? htmlspecialchars($edit_trainer['trainer_name']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Working Hours</label>
                    <input type="text" name="working_hours" class="form-control" required placeholder="e.g. 06:00 AM - 02:00 PM"
                           value="<?php echo $edit_trainer ? htmlspecialchars($edit_trainer['working_hours']) : ''; ?>">
                </div>
                <button type="submit" class="btn-primary">
                    <?php echo $edit_trainer ? 'Update Trainer' : 'Add Trainer'; ?>
                </button>
                <?php if($edit_trainer): ?>
                    <a href="trainers.php" style="display:block; text-align:center; margin-top:10px; color:var(--text-secondary);">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Table Section -->
        <div class="table-container" style="flex: 2; min-width: 400px;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Working Hours</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $trainers_res->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td style="font-weight: bold; color: var(--gold);"><?php echo htmlspecialchars($row['trainer_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['working_hours']); ?></td>
                        <td>
                            <a href="trainers.php?edit=<?php echo $row['id']; ?>" class="btn-small" style="color:var(--text-primary); background:#444; padding:5px 10px; border-radius:3px;">Edit</a>
                            <a href="trainers.php?delete=<?php echo $row['id']; ?>" class="btn-small btn-danger" style="padding:5px 10px; border-radius:3px; color:#fff;" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
