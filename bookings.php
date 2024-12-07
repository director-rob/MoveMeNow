<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: index.php');
    exit;
}

// Only allow access for Manager or Dispatcher roles
if ($_SESSION['role'] !== 'Manager' && $_SESSION['role'] !== 'Dispatcher') {
    header('Location: dashboard.php');
    exit;
}

$pageTitle = 'Manage Bookings';
include 'templates/header.php';
include 'templates/nav.php';
?>

<div class="container">
    <!--<h1>Manage Bookings</h1>-->
    <div class="panel" id="bookings-panel">
        <?php include 'panels/bookings_panel.php'; ?>
    </div>
</div>

<?php include 'templates/footer.php'; ?>