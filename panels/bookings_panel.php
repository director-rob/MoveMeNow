<?php
require_once 'db.php'; // Database connection

// Fetch all bookings
$query = 'SELECT * FROM Bookings ORDER BY Date DESC';
$stmt = $db->prepare($query);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Display Bookings Table -->
<h2>Bookings</h2>
<table border="1">
    <thead>
        <tr>
            <th>Booking ID</th>
            <th>Date</th>
            <th>Pickup Address</th>
            <th>Delivery Address</th>
            <th>Truck ID</th>
            <th>Completed</th>
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
                    <td><?php echo htmlspecialchars($booking['BookingCompleted'] == '1' ? 'True' : 'False'); ?></td>
                    <!-- <td><?php #echo htmlspecialchars($booking)[Truck]?></td> -->
                    <td>
                        <form method="POST" action="update_booking_status.php" style="display:inline;">
                            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['BookingID']); ?>">
                            <input type="hidden" name="current_status" value="<?php echo htmlspecialchars($booking['BookingCompleted']); ?>">
                            <button type="submit" class="status-toggle <?php echo $booking['BookingCompleted'] == '1' ? 'completed' : 'pending'; ?>">
                                <?php echo $booking['BookingCompleted'] == '1' ? 'Completed' : 'Pending'; ?>
                            </button>
                        </form>
                    </td>   
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No bookings found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Form to Add a Booking -->
<h3>Add New Booking</h3>
<form method="POST" action="create_booking.php">
    <label for="date">Date:</label>
    <input type="date" id="date" name="date" required>
    <br>
    <label for="pickup">Pickup Address:</label>
    <input type="text" id="pickup" name="pickup" required>
    <br>
    <label for="delivery">Delivery Address:</label>
    <input type="text" id="delivery" name="delivery" required>
    <br>
    <label for="truck">Truck ID:</label>
    <input type="number" id="truck" name="truck" required>
    <br>
    <label for="completed"> Completed:</label>
    <input type="checkbox" id="completed" name="completed" value="0">
    <br>
    <button type="submit">Add Booking</button>
</form>
