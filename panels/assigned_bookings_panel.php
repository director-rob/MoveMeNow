<?php
require_once 'db.php';
session_start();

// Ensure the user is a mover
if ($_SESSION['role'] !== 'Mover') {
    header('Location: index.php');
    exit;
}

// Get the mover ID from the session
$mover_id = $_SESSION['user_id'];

// Fetch assigned bookings for the mover
$query = '
    SELECT 
        B.BookingID,
        B.Date,
        B.PickupAddress,
        B.DeliveryAddress,
        B.Truck,
        B.PickedUp,
        B.Delivered
    FROM Bookings B
    JOIN BookingMovers BM ON B.BookingID = BM.BookingID
    WHERE BM.MoverID = :mover_id
    ORDER BY B.Date DESC
';
$stmt = $db->prepare($query);
$stmt->bindParam(':mover_id', $mover_id, PDO::PARAM_INT);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Display Assigned Bookings Table -->
<table border="1">
    <thead>
        <tr>
            <th>Booking ID</th>
            <th>Date</th>
            <th>Pickup Address</th>
            <th>Delivery Address</th>
            <th>Truck ID</th>
            <th>Picked Up</th>
            <th>Delivered</th>
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
                    <td><?php echo $booking['PickedUp'] ? 'Yes' : 'No'; ?></td>
                    <td><?php echo $booking['Delivered'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <form method="POST" action="update_booking_status.php" style="display:inline;">
                            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['BookingID']); ?>">
                            <button type="submit" name="action" value="picked_up" class="toggle-button picked-up <?php echo $booking['PickedUp'] ? 'undo' : ''; ?>">
                                <?php echo $booking['PickedUp'] ? 'Undo Picked Up' : 'Mark as Picked Up'; ?>
                            </button>
                            <button type="submit" name="action" value="delivered" class="toggle-button delivered <?php echo $booking['Delivered'] ? 'undo' : ''; ?>">
                                <?php echo $booking['Delivered'] ? 'Undo Delivered' : 'Mark as Delivered'; ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No assigned bookings found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>