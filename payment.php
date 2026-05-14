<?php
require_once 'includes/db.php';
include 'includes/header.php';

if (!isset($_GET['plan_id'])) {
    header("Location: membership.php");
    exit;
}

$plan_id = $_GET['plan_id'];
$user_id = $_SESSION['user_id'];

// Get plan details
$stmt = $pdo->prepare("SELECT * FROM membership_plans WHERE id = ?");
$stmt->execute([$plan_id]);
$plan = $stmt->fetch();

if (!$plan) {
    header("Location: membership.php");
    exit;
}

$message = '';
$payment_success = false;

if (isset($_POST['process_payment'])) {
    // Simulate payment processing delay (optional)
    // sleep(1); 

    // Update membership in database
    $stmt = $pdo->prepare("SELECT id FROM user_memberships WHERE user_id = ?");
    $stmt->execute([$user_id]);
    
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("UPDATE user_memberships SET plan_id = ? WHERE user_id = ?");
        $stmt->execute([$plan_id, $user_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO user_memberships (user_id, plan_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $plan_id]);
    }

    $payment_success = true;
    header("Location: dashboard.php?payment_success=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Payment | Gym System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-card" style="margin-top: 2rem;">
            <h2>Secure Checkout</h2>
            <p style="text-align: center; color: var(--text-muted); margin-bottom: 2rem;">Demo Payment for: <strong><?php echo $plan['plan_name']; ?></strong></p>
            
            <?php echo $message; ?>

            <?php if (!$payment_success): ?>
                <div style="background: rgba(255,255,255,0.03); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>Plan Price:</span>
                        <span style="color: var(--primary); font-weight: 700;">€<?php echo $plan['price']; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: var(--text-muted);">
                        <span>Transaction Fee:</span>
                        <span>€0.00</span>
                    </div>
                    <hr style="border: 0; border-top: 1px solid var(--glass-border); margin: 1rem 0;">
                    <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.2rem;">
                        <span>Total:</span>
                        <span>€<?php echo $plan['price']; ?></span>
                    </div>
                </div>

                <form method="POST">
                    <div class="form-group">
                        <label>Card Number</label>
                        <input type="text" placeholder="4242 4242 4242 4242" required maxlength="19">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Expiry Date</label>
                            <input type="text" placeholder="MM / YY" required maxlength="5">
                        </div>
                        <div class="form-group">
                            <label>CVV</label>
                            <input type="password" placeholder="•••" required maxlength="3">
                        </div>
                    </div>
                    <button type="submit" name="process_payment">Pay Now €<?php echo $plan['price']; ?></button>
                </form>
            <?php else: ?>
                <div style="text-align: center; margin-top: 1rem;">
                    <a href="dashboard.php"><button>Go to Dashboard</button></a>
                </div>
            <?php endif; ?>
            
            <p style="text-align: center; margin-top: 1.5rem; font-size: 0.8rem; color: var(--text-muted);">
                <span style="vertical-align: middle;">🔒</span> SSL Encrypted Demo Payment
            </p>
        </div>
    </div>
</body>
</html>
