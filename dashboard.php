<?php
session_start();

// DEBUG: Output current session state at the start of dashboard.php
echo 'Current Session State at dashboard.php:<br>';
echo 'Session ID: ' . session_id() . '<br>';
echo 'User ID: ' . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Not Set') . '<br>';
echo 'Role: ' . (isset($_SESSION['role']) ? $_SESSION['role'] : 'Not Set') . '<br>';

// Redirect if not logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo "User not logged in. Redirecting to login page...<br>";
    session_write_close();
    header('Location: index.php');
    exit;
}

// Fetch user's first name
require_once 'db.php';
$query = 'SELECT FirstName FROM Employees WHERE EmployeeID = :user_id';
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$firstName = $stmt->fetchColumn();

$pageTitle = 'Admin Dashboard';
include 'templates/header.php';
include 'templates/nav.php';
?>
<div class="container">
    <h1>MoveMeNow Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($firstName); ?>!</p>

    <div class="dashboard-panels">
        <?php if ($_SESSION['role'] === 'Manager'): ?>
            <!-- Bookings Panel -->
            <div class="panel" id="bookings-panel">
                <?php include 'panels/bookings_panel.php'; ?>
            </div>

            <!-- Employees Panel -->
            <div class="panel" id="employees-panel">
                <?php include 'panels/employees_panel.php'; ?>
            </div>

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
        <?php endif; ?>
    </div>
</div>
<?php include 'templates/footer.php'; // Include shared footer ?>
