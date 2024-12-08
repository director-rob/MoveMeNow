<?php
require_once 'db.php';
session_start();

// Ensure the user is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Customer') {
    header('Location: index.php');
    exit;
}

// Get the customer ID from the session
$customer_id = $_SESSION['user_id'];

// Prepare the SQL query
$query = "
 SELECT 
    b.BookingID,
    b.Date,
    b.PickupAddress,
    b.DeliveryAddress,
    b.TotalCost,
    b.MoveSize,
    b.MoveWeight,
    b.BookingCompleted,
    b.Paid,
    b.PickedUp,
    b.Delivered,
    b.TimePickedUp,
    b.TimeDelivered,
    b.Truck
FROM 
    Bookings b
WHERE 
    b.BookingID IN (
        SELECT BookingID
        FROM Customers
        WHERE CustomerID = :customer_id
    );

";

// Prepare and execute the query
$stmt = $db->prepare($query);
$stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
$stmt->execute();

// Fetch all results
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <h3>My Bookings</h3>
    <?php if ($results): ?>
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Date</th>
                    <th>Pickup Address</th>
                    <th>Delivery Address</th>
                    <th>Total Cost</th>
                    <th>Move Size</th>
                    <th>Move Weight</th>
                    <th>Paid</th>
                    <th>Delivered</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['BookingID']); ?></td>
                        <td><?php echo htmlspecialchars($row['Date']); ?></td>
                        <td><?php echo htmlspecialchars($row['PickupAddress']); ?></td>
                        <td><?php echo htmlspecialchars($row['DeliveryAddress']); ?></td>
                        <td>$<?php echo number_format($row['TotalCost'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['MoveSize']); ?></td>
                        <td><?php echo htmlspecialchars($row['MoveWeight']); ?> kg</td>
                        <td><?php echo $row['Paid'] ? 'Yes' : 'No'; ?></td>
                        <td><?php echo $row['Delivered'] ? 'Yes' : 'No'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No bookings found for CustomerID <?php echo htmlspecialchars($customer_id); ?>.</p>
    <?php endif; ?>
</body>
</html>