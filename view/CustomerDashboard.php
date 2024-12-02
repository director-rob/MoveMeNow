<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($customerData['FirstName']); ?>!</h1>
    <p>Date Joined: <?php echo htmlspecialchars($customerData['DateJoined']); ?></p>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>
