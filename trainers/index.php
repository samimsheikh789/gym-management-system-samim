<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../dashboard.php");
    exit();
}
require '../includes/db.php';

$search = '';
$query = "SELECT * FROM trainers";
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = trim($_GET['search']);
    $query .= " WHERE trainer_name LIKE ?";
}
$query .= " ORDER BY id DESC";

$stmt = $conn->prepare($query);
if ($search) {
    $search_param = "%$search%";
    $stmt->bind_param("s", $search_param);
}
$stmt->execute();
$trainers_res = $stmt->get_result();

$msg = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'deleted') $msg = "<div class='alert alert-success'>Trainer deleted successfully.</div>";
    if ($_GET['msg'] == 'added') $msg = "<div class='alert alert-success'>Trainer added successfully.</div>";
    if ($_GET['msg'] == 'updated') $msg = "<div class='alert alert-success'>Trainer updated successfully.</div>";
}

include '../includes/header.php';
?>

<div class="container">
    <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Trainer Management</h1>
            <p style="color: var(--text-secondary);">Manage gym trainers and staff.</p>
        </div>
        <?php if($_SESSION['role'] === 'admin'): ?>
            <a href="add.php" class="btn-primary" style="width: auto; padding: 0.8rem 1.5rem;">+ Add New Trainer</a>
        <?php endif; ?>
    </div>
    
    <?php echo $msg; ?>

    <div style="margin-bottom: 2rem;">
        <form method="GET" action="index.php" style="display: flex; gap: 10px;">
            <input type="text" name="search" class="form-control" placeholder="Search trainer by name..." value="<?php echo htmlspecialchars($search); ?>" style="max-width: 300px;">
            <button type="submit" class="btn-primary" style="width: auto; padding: 0.8rem 1.5rem;">Search</button>
            <?php if($search): ?>
                <a href="index.php" class="btn-primary" style="width: auto; padding: 0.8rem 1.5rem; background:#444; color:#fff;">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Specialization</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Working Hours</th>
                    <th>Experience</th>
                    <?php if($_SESSION['role'] === 'admin'): ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if($trainers_res->num_rows > 0): ?>
                    <?php while($row = $trainers_res->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td style="font-weight: bold; color: var(--gold);"><?php echo htmlspecialchars($row['trainer_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['specialization']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['working_hours']); ?></td>
                        <td><?php echo htmlspecialchars($row['experience']); ?></td>
                        <?php if($_SESSION['role'] === 'admin'): ?>
                        <td style="white-space: nowrap;">
                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn-small" style="color:var(--text-primary); background:#444; padding:5px 10px; border-radius:3px;">Edit</a>
                            <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn-small btn-danger" style="padding:5px 10px; border-radius:3px; color:#fff;" onclick="return confirm('Are you sure you want to delete this trainer? This action cannot be undone.');">Delete</a>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8" style="text-align:center;">No trainers found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
