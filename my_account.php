<?php
require_once 'db.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// Fetch current user details based on role
if ($_SESSION['role'] === 'Customer') {
    $query = '
        SELECT 
            CustomerID AS UserID,
            FirstName,
            LastName,
            Email AS Username,
            "Customer" AS Role
        FROM Customers 
        WHERE CustomerID = :user_id
    ';
} else {
    $query = '
        SELECT 
            EmployeeID AS UserID,
            FirstName,
            LastName,
            Username,
            Role,
            DateJoined 
        FROM Employees 
        WHERE EmployeeID = :user_id
    ';
}
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Verify current password based on role
    if ($_SESSION['role'] === 'Customer') {
        $query = 'SELECT Password FROM Customers WHERE CustomerID = :user_id';
    } else {
        $query = 'SELECT Password FROM Employees WHERE EmployeeID = :user_id';
    }
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $storedHash = $stmt->fetchColumn();

    if (!password_verify($currentPassword, $storedHash)) {
        $error = 'Current password is incorrect.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'New passwords do not match.';
    } else {
        // Update password based on role
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        if ($_SESSION['role'] === 'Customer') {
            $query = 'UPDATE Customers SET Password = :password WHERE CustomerID = :user_id';
        } else {
            $query = 'UPDATE Employees SET Password = :password WHERE EmployeeID = :user_id';
        }
        $stmt = $db->prepare($query);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $success = 'Password updated successfully.';
        } else {
            $error = 'Failed to update password.';
        }
    }
}

$pageTitle = 'My Account';
include 'templates/header.php';
include 'templates/nav.php';
?>

<div class="container">
    <h2>My Account</h2>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <h3>Account Information</h3>
    <table>
        <tr>
            <td><strong>Name:</strong></td>
            <td><?php echo htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']); ?></td>
        </tr>
        <tr>
            <td><strong>Username:</strong></td>
            <td><?php echo htmlspecialchars($user['Username']); ?></td>
        </tr>
        <tr>
            <td><strong>Role:</strong></td>
            <td><?php echo htmlspecialchars($user['Role']); ?></td>
        </tr>
        <?php if ($user['Role'] !== 'Customer'): ?>
        <tr>
            <td><strong>Date Joined:</strong></td>
            <td><?php echo htmlspecialchars($user['DateJoined']); ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <div class="collapsible-section">
        <button type="button" class="collapse-toggle" onclick="toggleSection('change-password')">
            <span class="toggle-icon">▼</span> Change Password
        </button>
        <div id="change-password" class="collapsible-content">
            <form method="POST" action="my_account.php">
                <div class="form-group">
                    <label for="current_password">Current Password:</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" name="update_password">Update Password</button>
            </form>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>