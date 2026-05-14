<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'includes/db.php';

$classes_res = $conn->query("SELECT * FROM classes ORDER BY class_name ASC");

include 'includes/header.php';
?>

<div class="container">
    <div class="dashboard-header" style="text-align: center;">
        <h1>Gym Classes Schedule</h1>
        <p style="color: var(--text-secondary);">Find the perfect class for your fitness journey.</p>
    </div>

    <div class="table-container" style="max-width: 800px; margin: 0 auto;">
        <table>
            <thead>
                <tr>
                    <th>Class Name</th>
                    <th>Schedule Overview</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $classes_res->fetch_assoc()): ?>
                <tr>
                    <td style="font-weight: bold; color: var(--gold);"><?php echo htmlspecialchars($row['class_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['schedule']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
