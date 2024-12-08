<?php
session_start();
?>

<nav class="navbar">
    <div class="nav-left">
        <a href="dashboard.php" class="nav-button">Dashboard</a>
        <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'Dispatcher' || $_SESSION['role'] === 'Manager')): ?>
            <a href="bookings.php" class="nav-button">Manage Bookings</a>
            <a href="customers.php" class="nav-button">Manage Customers</a>
        <?php endif; ?>
    </div>
    <div class="nav-right">
        <a href="my_account.php" class="nav-button">My Account</a>
        <a href="logout.php" class="nav-button">Logout</a>
    </div>
</nav>