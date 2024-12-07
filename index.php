<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Start session at the very beginning
require_once 'db.php'; // Database connection



// Redirect if already logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    // DEBUG: Show message if user is already logged in
    echo "Already logged in, redirecting to dashboard...<br>";
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        // Query Employees table for matching credentials
        $query = 'SELECT EmployeeID, Password, Role FROM Employees WHERE Username = :username';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // DEBUG: Check if user is fetched
        if ($user) {
            echo 'User found in database: UserID = ' . $user['EmployeeID'] . ', Role = ' . $user['Role'] . '<br>';
        } else {
            echo 'No user found with the provided username.<br>';
        }

        // Verify password and handle role-based redirection
        if ($user && password_verify($password, $user['Password'])) {
            // Regenerate session ID after successful login to prevent session fixation attacks
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['user_id'] = $user['EmployeeID'];
            $_SESSION['role'] = $user['Role'];

            // DEBUG: Output session values after login success
            echo 'Login successful. Setting session values:<br>';
            echo 'User ID: ' . $_SESSION['user_id'] . '<br>';
            echo 'Role: ' . $_SESSION['role'] . '<br>';

            // Close the session write to avoid issues before redirecting
            session_write_close();

            // Redirect to dashboard after successful login
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Set page title for the header
$pageTitle = 'Login';
include 'templates/header.php'; // Include shared header
?>
<h1><em>Move Me Now</em> Moving Company</h1>
<div class="login-container">
    <h2>Login</h2>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST" action="index.php">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>
        <button type="submit">Login</button>
    </form>
</div>
