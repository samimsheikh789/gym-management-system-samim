<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'includes/db.php';
$user_id = $_SESSION['user_id'];


$mem_stmt = $conn->prepare("SELECT m.plan_name, um.start_date, um.status FROM user_memberships um JOIN membership_plans m ON um.plan_id = m.id WHERE um.user_id = ? AND um.status = 'active' ORDER BY um.id DESC LIMIT 1");
$mem_stmt->bind_param("i", $user_id);
$mem_stmt->execute();
$mem_result = $mem_stmt->get_result();
$membership = $mem_result->fetch_assoc();
$mem_stmt->close();

// Dashboard Stats for Admin
$total_users = 0;
$total_classes = 0;
if ($_SESSION['role'] === 'admin') {
    $res = $conn->query("SELECT COUNT(*) as c FROM users");
    $total_users = $res->fetch_assoc()['c'];
    $res = $conn->query("SELECT COUNT(*) as c FROM classes");
    $total_classes = $res->fetch_assoc()['c'];
}

// Upcoming Classes
$classes_res = $conn->query("SELECT * FROM classes LIMIT 3");

include 'includes/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p style="color: var(--text-secondary);">Here is your gym overview.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Membership Status</h3>
            <?php if ($membership): ?>
                <div class="value" style="font-size: 1.5rem;"><?php echo htmlspecialchars($membership['plan_name']); ?>
                </div>
                <p style="color: var(--success); font-size: 0.9rem;">Active since <?php echo $membership['start_date']; ?>
                </p>
            <?php else: ?>
                <div class="value" style="font-size: 1.2rem; color: var(--danger);">No Active Plan</div>
                <a href="membership.php" class="btn-small btn-primary" style="display:inline-block; margin-top:10px;">Get a
                    Plan</a>
            <?php endif; ?>
        </div>

        <?php if ($_SESSION['role'] === 'admin'): ?>
            <div class="stat-card">
                <h3>Total Members</h3>
                <div class="value"><?php echo $total_users; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Classes</h3>
                <div class="value"><?php echo $total_classes; ?></div>
            </div>
        <?php else: ?>
            <div class="stat-card">
                <h3>Next Goal</h3>
                <div class="value" style="font-size: 1.2rem;">Stay Consistent!</div>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">Hit the gym 3 times this week.</p>
            </div>
        <?php endif; ?>
    </div>

    <h2 style="color: var(--gold); margin-bottom: 1rem;">Upcoming Classes</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Class Name</th>
                    <th>Schedule</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $classes_res->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['schedule']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>