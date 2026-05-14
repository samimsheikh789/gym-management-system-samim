<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'includes/db.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['plan_id'])) {
    $plan_id = intval($_POST['plan_id']);
    $user_id = $_SESSION['user_id'];
    
    // Simple logic: deactivate old plans, insert new active plan
    $conn->query("UPDATE user_memberships SET status = 'inactive' WHERE user_id = $user_id");
    
    $stmt = $conn->prepare("INSERT INTO user_memberships (user_id, plan_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $plan_id);
    if ($stmt->execute()) {
        $msg = "<div class='alert alert-success'>Successfully subscribed to the plan!</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Subscription failed.</div>";
    }
    $stmt->close();
}

$plans_res = $conn->query("SELECT * FROM membership_plans");

include 'includes/header.php';
?>

<div class="container">
    <div class="dashboard-header" style="text-align: center;">
        <h1>Membership Plans</h1>
        <p style="color: var(--text-secondary);">Choose the plan that fits your goals.</p>
    </div>
    
    <?php echo $msg; ?>

    <div class="card-grid">
        <?php while($plan = $plans_res->fetch_assoc()): ?>
            <div class="card">
                <h3><?php echo htmlspecialchars($plan['plan_name']); ?></h3>
                <div class="price">$<?php echo htmlspecialchars($plan['price']); ?></div>
                <p style="color: var(--text-secondary); margin-bottom: 1rem;">Duration: <?php echo htmlspecialchars($plan['duration']); ?></p>
                
                <?php 
                    $features = explode(',', $plan['features']);
                ?>
                <ul>
                    <?php foreach($features as $feature): ?>
                        <li><?php echo htmlspecialchars(trim($feature)); ?></li>
                    <?php endforeach; ?>
                </ul>
                
                <p style="font-size: 0.9rem; margin-bottom: 1.5rem; text-align:left; color:#ccc;">
                    <?php echo htmlspecialchars($plan['benefits']); ?>
                </p>
                
                <form method="POST" action="">
                    <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>">
                    <button type="submit" class="btn-primary" onclick="return confirm('Are you sure you want to subscribe to this plan?');">Subscribe Now</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
