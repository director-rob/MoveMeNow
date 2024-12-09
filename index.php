<?php
session_start();

// Redirect to dashboard if already logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    header('Location: dashboard.php');
    exit;
}

$pageTitle = 'Welcome';
include 'templates/header.php';
?>

<h1>Welcome to Move Me Now</h1>

    <div class="login-container">
        <p>Please choose an option:</p>
        <div class="button-group-home">
            <a href="get_in_touch.php" class="action-button">Get in Touch</a>
            <a href="login.php" class="action-button">Already have an account? Login</a>
        </div>
    </div>


<?php include 'templates/footer.php'; ?>