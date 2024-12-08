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
    $usernameOrEmail = $_POST['username_or_email'] ?? '';
    $password = $_POST['password'] ?? '';
    $userType = $_POST['user_type'] ?? '';

    try {
        if ($userType === 'Employee') {
            // Query Employees table for matching credentials
            $query = 'SELECT EmployeeID AS UserID, Password, Role FROM Employees WHERE Username = :username';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $usernameOrEmail, PDO::PARAM_STR);
        } elseif ($userType === 'Customer') {
            // Query Customers table for matching credentials
            $query = 'SELECT CustomerID AS UserID, Password, "Customer" AS Role FROM Customers WHERE Email = :email';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':email', $usernameOrEmail, PDO::PARAM_STR);
        } else {
            throw new Exception('Invalid user type selected.');
        }

        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // DEBUG: Check if user is fetched
        if ($user) {
            echo 'User found in database: UserID = ' . $user['UserID'] . ', Role = ' . $user['Role'] . '<br>';
        } else {
            echo 'No user found with the provided username/email.<br>';
        }

        // Verify password and handle role-based redirection
        if ($user && password_verify($password, $user['Password'])) {
            // Regenerate session ID after successful login to prevent session fixation attacks
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['user_id'] = $user['UserID'];
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
            $error = 'Invalid username/email or password.';
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    } catch (Exception $e) {
        $error = $e->getMessage();
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
            <label for="username_or_email">Username or Email:</label>
            <input type="text" id="username_or_email" name="username_or_email" placeholder="Enter your username or email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>
        <div class="form-group">
            <label for="user_type">User Type:</label>
            <select id="user_type" name="user_type" required>
                <option value="">Select user type</option>
                <option value="Employee">Employee</option>
                <option value="Customer">Customer</option>
            </select>
        </div>
        <button type="submit">Login</button>
    </form>
</div>
<?php include 'templates/footer.php'; ?>