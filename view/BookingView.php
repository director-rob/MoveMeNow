<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details</title>
</head>
<body>
    <h1>Booking Details</h1>
    <?php if (!empty($booking)): ?>
        <p><strong>Booking ID:</strong> <?php echo htmlspecialchars($booking[0]['BookingID']); ?></p>
        <p><strong>Date:</strong> <?php echo htmlspecialchars($booking[0]['Date']); ?></p>
        <p><strong>Pickup Address:</strong> <?php echo htmlspecialchars($booking[0]['PickupAddress']); ?></p>
        <p><strong>Delivery Address:</strong> <?php echo htmlspecialchars($booking[0]['DeliveryAddress']); ?></p>
        <p><strong>Truck ID:</strong> <?php echo htmlspecialchars($booking[0]['Truck']); ?></p>
        <h3>Movers Assigned:</h3>
        <ul>
            <?php foreach ($booking as $mover): ?>
                <li><?php echo htmlspecialchars($mover['MoverName']); ?> (Mover ID: <?php echo htmlspecialchars($mover['MoverID']); ?>)</li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No booking found.</p>
    <?php endif; ?>
    <a href="/bookings">Back to All Bookings</a>
</body>
</html>
