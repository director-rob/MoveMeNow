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
    session_write_close(); // Close session write before redirect
    header('Location: index.php');
    exit;
}

$pageTitle = 'Admin Dashboard';
include 'templates/header.php'; // Include shared header
include 'templates/nav.php'; // Include navigation (optional)
?>
<div class="container">
    <h1>MoveMeNow Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['role']); ?>!</p>

    <div class="dashboard-panels">
        <?php if ($_SESSION['role'] === 'Manager'): ?>
            <!-- Bookings Panel -->
            <div class="panel" id="bookings-panel">
                <h2>Bookings</h2>
                <?php include 'panels/bookings_panel.php'; ?>
            </div>

            <!-- Employees Panel -->
            <div class="panel" id="employees-panel">
                <h2>Employees</h2>
                <?php include 'panels/employees_panel.php'; ?>
            </div>

            <!-- Movers Panel -->
            <div class="panel" id="movers-panel">
                <h2>Movers</h2>
                <?php include 'panels/movers_panel.php'; ?>
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
