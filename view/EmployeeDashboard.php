<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'Mover' && $_SESSION['role'] !== 'Manager')) {
    header('Location: ../index.php'); // Redirect to login
    exit;
}

$pageTitle = 'Employee Dashboard';
include '../header.php'; // Shared header

$role = $_SESSION['role'];
?>

<div class="container">
    <h1>Employee Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($role); ?>!</p>
    <p><a href="../controllers/logout.php" class="button">Logout</a></p>

    <div class="dashboard-panels">
        <?php if ($role === 'Manager'): ?>
            <div class="panel">
                <?php
                // Include BookingList for managers
                require_once '../controllers/BookingController.php';
                $bookingController = new BookingController();
                $bookingController->list_bookings();
                ?>
            </div>
        <?php elseif ($role === 'Mover'): ?>
            <div class="panel">
                <h2>My Assigned Bookings</h2>
                <?php
                // Fetch and display assigned bookings for movers
                require_once '../controllers/BookingController.php';
                $bookingController = new BookingController();
                $bookingController->list_bookings(); // You can modify this to filter assigned bookings for movers
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../footer.php'; // Shared footer ?>
