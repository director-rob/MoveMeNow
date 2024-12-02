<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Bookings</title>
</head>
<body>
    <h1>All Bookings</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Date</th>
                <th>Pickup Address</th>
                <th>Delivery Address</th>
                <th>Truck ID</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($bookings)): ?>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['BookingID']); ?></td>
                        <td><?php echo htmlspecialchars($booking['Date']); ?></td>
                        <td><?php echo htmlspecialchars($booking['PickupAddress']); ?></td>
                        <td><?php echo htmlspecialchars($booking['DeliveryAddress']); ?></td>
                        <td><?php echo htmlspecialchars($booking['Truck']); ?></td>
                        <td><a href="/bookings/<?php echo htmlspecialchars($booking['BookingID']); ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No bookings found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
