<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo "User not logged in. Redirecting to login page...<br>";
    session_write_close();
    header('Location: index.php');
    exit;
}

// Fetch user's first name based on role
require_once 'db.php';
if ($_SESSION['role'] === 'Customer') {
    $query = 'SELECT FirstName FROM Customers WHERE CustomerID = :user_id';
} else {
    $query = 'SELECT FirstName FROM Employees WHERE EmployeeID = :user_id';
}
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$firstName = $stmt->fetchColumn();

$pageTitle = 'Dashboard';
include 'templates/header.php';
include 'templates/nav.php';
?>
<div class="container">
    <h1>MoveMeNow Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($firstName); ?>!</p>

    <div class="dashboard-panels">
        <?php if ($_SESSION['role'] === 'Manager' || $_SESSION['role'] === 'Dispatcher'): ?>
            <!-- Bookings Panel -->
            <div class="panel" id="bookings-panel">
                <?php include 'panels/bookings_panel.php'; ?>
            </div>

            <?php if ($_SESSION['role'] === 'Manager'): ?>
                <!-- Employees Panel -->
                <div class="panel" id="employees-panel">
                    <?php include 'panels/employees_panel.php'; ?>
                </div>
            <?php endif; ?>

            <!-- Movers Panel -->
            <div class="panel" id="movers-panel">
                <?php include 'panels/movers_panel.php'; ?>
            </div>
            
            <!-- Trucks Panel -->
            <div class="panel" id="trucks-panel">
                <?php include 'panels/trucks_panel.php'; ?>
            </div>
            
        <?php elseif ($_SESSION['role'] === 'Mover'): ?>
            <!-- Assigned Bookings Panel for Mover -->
            <div class="panel" id="assigned-bookings-panel">
                <h2>My Assigned Bookings</h2>
                <?php include 'panels/assigned_bookings_panel.php'; ?>
            </div>
        <?php elseif ($_SESSION['role'] === 'Customer'): ?>
            <!-- Customer Panel -->
            <div class="panel" id="customer-panel">
                <?php include 'panels/customer_panel.php'; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include 'templates/footer.php'; ?>