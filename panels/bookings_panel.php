<?php
require_once 'db.php'; // Database connection

// Fetch all movers
$moversQuery = 'SELECT MoverID, Name FROM Movers';
$stmt = $db->prepare($moversQuery);
$stmt->execute();
$movers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all bookings
$query = '
    SELECT 
        B.*,
        GROUP_CONCAT(M.Name) as AssignedMovers
    FROM Bookings B
    LEFT JOIN BookingMovers BM ON B.BookingID = BM.BookingID
    LEFT JOIN Movers M ON BM.MoverID = M.MoverID
    LEFT JOIN ArchivedBookings A ON B.BookingID = A.BookingID
    WHERE A.BookingID IS NULL
    GROUP BY B.BookingID
    ORDER BY B.Date DESC
';
$stmt = $db->prepare($query);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Display Bookings Table -->
<h2>Upcoming Bookings</h2>
<div class="collapsible-section">
    <button type="button" class="collapse-toggle" onclick="toggleSection('upcoming-bookings')">
        <span class="toggle-icon">▼</span> View Upcoming Bookings
    </button>
    <div id="upcoming-bookings" class="collapsible-content">
        <table border="1">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Date</th>
                    <th>Pickup Address</th>
                    <th>Delivery Address</th>
                    <th>Truck ID</th>
                    <th>Completed</th>
                    <th>Assigned Movers</th>
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
                            <td><?php echo htmlspecialchars($booking['AssignedMovers'] ?? 'None'); ?></td>
                            <td>
                                <div class="button-group">
                                    <form method="POST" action="update_booking_status.php" style="display:inline;">
                                        <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['BookingID']); ?>">
                                        <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                                        <button type="submit" name="action" value="<?php echo $booking['BookingCompleted'] == '1' ? 'in_progress' : 'complete'; ?>" class="status-toggle <?php echo $booking['BookingCompleted'] == '1' ? 'completed' : 'pending'; ?>">
                                            <?php echo $booking['BookingCompleted'] == '1' ? 'Completed' : 'Pending'; ?>
                                        </button>
                                    </form>
                                    <button onclick="showEditForm('<?php echo htmlspecialchars($booking['BookingID']); ?>')" class="edit-button">Edit</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No bookings found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleSection(id) {
    const section = document.getElementById(id);
    section.classList.toggle('collapsed');
}

// Initialize sections as collapsed
document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('.collapsible-content');
    sections.forEach(section => {
        section.classList.add('collapsed');
    });
});

function showEditForm(bookingId) {
    window.location.href = `booking_details.php?id=${bookingId}`;
}

function closeEditForm() {
    document.getElementById('editBookingModal').style.display = 'none';
}
</script>