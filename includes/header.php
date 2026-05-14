<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Management System</title>
    <link rel="stylesheet" href="/gym_system/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-brand">GYM <span style="color:white;">PRO</span></div>
    <div class="nav-links">
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="/gym_system/dashboard.php">Dashboard</a>
            <a href="/gym_system/membership.php">Memberships</a>
            <a href="/gym_system/classes.php">Classes</a>
            <a href="/gym_system/trainers/index.php">Trainers</a>
            <a href="/gym_system/logout.php" class="btn-logout">Logout</a>
        <?php else: ?>
            <a href="/gym_system/login.php">Login</a>
            <a href="/gym_system/register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>
