<?php
session_start();
require_once 'db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
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

        if ($user && password_verify($password, $user['Password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['role'] = $user['Role'];
            session_write_close();
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

$pageTitle = 'Login';
include 'templates/header.php';
?>
<div class="login-panel">
<h1><em>Move Me Now</em> Moving Company</h1>
<div class="login-container">
    <h2>Login</h2>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST" action="login.php">
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
</div>
<?php include 'templates/footer.php'; ?>