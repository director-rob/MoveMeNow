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

<?php

// Fetch the most upcoming booking for the mover
$upcomingBookingQuery = '
    SELECT 
        B.BookingID,
        B.Date,
        B.PickupAddress,
        B.DeliveryAddress,
        B.Instructions,
        B.PickedUp,
        B.Delivered,
        B.MoveWeight,
        B.MoveSize,
        B.BookingCompleted,
        T.TruckID,
        T.LicensePlate,
        T.Make,
        T.Model,
        T.SizeInFeet
    FROM Bookings B
    JOIN BookingMovers BM ON B.BookingID = BM.BookingID
    LEFT JOIN Trucks T ON B.Truck = T.TruckID
    WHERE BM.MoverID = :mover_id AND B.Date >= CURDATE()
    ORDER BY B.Date ASC
    LIMIT 1
';
$upcomingStmt = $db->prepare($upcomingBookingQuery);
$upcomingStmt->bindParam(':mover_id', $mover_id, PDO::PARAM_INT);
$upcomingStmt->execute();
$upcomingBooking = $upcomingStmt->fetch(PDO::FETCH_ASSOC);
?>


<?php if ($upcomingBooking): ?>
    <h2>My Day View</h2>
    <div class="collapsible-section">
        <button type="button" class="collapse-toggle" onclick="toggleSection('my-day-view')">
            <span class="toggle-icon">▼</span> Today's Booking
        </button>
        <div id="my-day-view" class="collapsible-content">
            <div class="panel">
            <div class="three-column-container">
                <div class="booking-section">
                <h4>Booking ID: <?php echo htmlspecialchars($upcomingBooking['BookingID']); ?></h4>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($upcomingBooking['Date']); ?></p>
                <?php
                // Escape the addresses
                $pickupAddress = htmlspecialchars($upcomingBooking['PickupAddress']);
                $deliveryAddress = htmlspecialchars($upcomingBooking['DeliveryAddress']);

                // Create Google Maps URLs
                $pickupMapUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($upcomingBooking['PickupAddress']);
                $deliveryMapUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($upcomingBooking['DeliveryAddress']);
                ?>
                
                
                <p><strong>Pickup Address:</strong> <a href="<?php echo $pickupMapUrl; ?>" target="_blank"><?php echo $pickupAddress; ?></a></p>
                <p><strong>Delivery Address:</strong> <a href="<?php echo $deliveryMapUrl; ?>" target="_blank"><?php echo $deliveryAddress; ?></a></p> <p><strong>Instructions:</strong> <?php echo htmlspecialchars($upcomingBooking['Instructions']); ?></p>
                </div>
                <div class="move-details">
                <h4>Move Details:</h4>
                    <p><strong>Move Weight:</strong> <?php echo htmlspecialchars($upcomingBooking['MoveWeight']); ?> lbs</p>
                    <p><strong>Move Size:</strong> <?php echo htmlspecialchars($upcomingBooking['MoveSize']); ?></p>
                    <p><strong>Booking Completed:</strong> <?php echo $upcomingBooking['BookingCompleted'] ? 'Yes' : 'No'; ?></p>
                    </div>
                <div class="truck-details-section">              
                    <h4>Truck Details:</h4>
                <?php if ($upcomingBooking['TruckID']): ?>
                    <p><strong>Truck ID:</strong> <?php echo htmlspecialchars($upcomingBooking['TruckID']); ?></p>
                    <p><strong>License Plate:</strong> <?php echo htmlspecialchars($upcomingBooking['LicensePlate']); ?></p>
                    <p><strong>Make:</strong> <?php echo htmlspecialchars($upcomingBooking['Make']); ?></p>
                    <p><strong>Model:</strong> <?php echo htmlspecialchars($upcomingBooking['Model']); ?></p>
                    <p><strong>Size:</strong> <?php echo htmlspecialchars($upcomingBooking['SizeInFeet']); ?> ft</p>
                <?php else: ?>
                    <p>No truck assigned.</p>
                <?php endif; ?>
                </div>
                </div>  
                <!-- Actions -->
                <form method="POST" action="update_booking_status.php" style="margin-top: 1em;">
                    <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($upcomingBooking['BookingID']); ?>">
                    <button type="submit" name="action" value="<?php echo $upcomingBooking['PickedUp'] ? 'undo_picked_up' : 'picked_up'; ?>" class="toggle-button picked-up <?php echo $upcomingBooking['PickedUp'] ? 'undo' : ''; ?>">
                        <?php echo $upcomingBooking['PickedUp'] ? 'Undo Picked Up' : 'Mark as Picked Up'; ?>
                    </button>
                    <button type="submit" name="action" value="<?php echo $upcomingBooking['Delivered'] ? 'undo_delivered' : 'delivered'; ?>" class="toggle-button delivered <?php echo $upcomingBooking['Delivered'] ? 'undo' : ''; ?>">
                        <?php echo $upcomingBooking['Delivered'] ? 'Undo Delivered' : 'Mark as Delivered'; ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
    
<?php else: ?>
    <p>No upcoming bookings found.</p>
<?php endif; ?>


<!-- Display Assigned Bookings Table -->
<h3>My Assigned Bookings</h3>
<div class="collapsible-section">
    <button type="button" class="collapse-toggle" onclick="toggleSection('assigned-bookings')">
        <span class="toggle-icon">▼</span> View Bookings
    </button>
    <div id="assigned-bookings" class="collapsible-content">
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
    </div>
</div>

<script>
function toggleSection(id) {
    const section = document.getElementById(id).parentElement;
    section.classList.toggle('collapsed');
}


</script>