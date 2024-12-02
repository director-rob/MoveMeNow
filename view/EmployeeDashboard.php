<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'Mover' && $_SESSION['role'] !== 'Manager')) {
    header('Location: index.php'); // Redirect to login if not logged in or invalid role
    exit;
}

// Fetch user data from session
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
</head>
<body>
    <h1>Welcome to the Employee Dashboard!</h1>
    <p>Your Role: <?php echo htmlspecialchars($role); ?></p>
    <a href="../logout.php">Logout</a>
</body>
</html>
