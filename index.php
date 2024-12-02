<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db.php'; // Database connection

// Redirect if already logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'Mover' || $_SESSION['role'] === 'Manager') {
        header('Location: view/EmployeeDashboard.php'); // Redirect employees
        exit;
    }
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

        // Verify password and handle role-based redirection
        if ($user && password_verify($password, $user['Password'])) {
            $_SESSION['user_id'] = $user['EmployeeID'];
            $_SESSION['role'] = $user['Role'];

            if ($user['Role'] === 'Mover' || $user['Role'] === 'Manager') {
                header('Location: view/EmployeeDashboard.php');
                exit;
            }
        } else {
            $error = 'Invalid username or password.';
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Set page title for the header
$pageTitle = 'Login';
include 'view/templates/header.php'; // Include shared header
?>
<div class="container">
    <h1>Login</h1>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST" action="index.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" placeholder="Enter your username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>
        <br>
        <button type="submit">Login</button>
    </form>
</div>
<?php include 'view/templates/footer.php'; // Include shared footer ?>
