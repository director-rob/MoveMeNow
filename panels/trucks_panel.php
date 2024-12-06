<?php
require_once 'db.php';

// Fetch all trucks
$query = 'SELECT * FROM Trucks';
$stmt = $db->prepare($query);
$stmt->execute();
$trucks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch bookings for the current date
$currentDate = date('Y-m-d');
$bookingQuery = '
    SELECT BookingID, Truck, Date 
    FROM Bookings 
    WHERE Date = :currentDate
';
$bookingStmt = $db->prepare($bookingQuery);
$bookingStmt->bindParam(':currentDate', $currentDate, PDO::PARAM_STR);
$bookingStmt->execute();
$bookingsToday = $bookingStmt->fetchAll(PDO::FETCH_ASSOC);

// Map truck IDs to booking IDs for today
$truckBookingsToday = [];
foreach ($bookingsToday as $booking) {
    $truckBookingsToday[$booking['Truck']] = $booking['BookingID'];
}
?>

<h2>Trucks</h2>
<table border="1">
    <thead>
        <tr>
            <th>Truck ID</th>
            <th>License Plate</th>
            <th>Make</th>
            <th>Model</th>
            <th>Size (Feet)</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($trucks as $truck): ?>
            <tr style="background-color: <?php echo isset($truckBookingsToday[$truck['TruckID']]) ? '#ffcccc' : '#ffffff'; ?>">
                <td><?php echo htmlspecialchars($truck['TruckID']); ?></td>
                <td><?php echo htmlspecialchars($truck['LicensePlate']); ?></td>
                <td><?php echo htmlspecialchars($truck['Make']); ?></td>
                <td><?php echo htmlspecialchars($truck['Model']); ?></td>
                <td><?php echo htmlspecialchars($truck['SizeInFeet']); ?></td>
                <td>
                    <?php if (isset($truckBookingsToday[$truck['TruckID']])): ?>
                        Assigned to Booking ID: <?php echo htmlspecialchars($truckBookingsToday[$truck['TruckID']]); ?>
                    <?php else: ?>
                        Available
                    <?php endif; ?>
                </td>
                <td>
                    <button class='action-button' onclick="showUpcomingBookings(<?php echo htmlspecialchars($truck['TruckID']); ?>)">View Upcoming Bookings</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Modal for upcoming bookings -->
<div id="upcomingBookingsModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeUpcomingBookingsModal()">&times;</span>
        <h3>Upcoming Bookings for Truck ID: <span id="truckId"></span></h3>
        <div id="upcomingBookingsContent"></div>
    </div>
</div>

<script>
function showUpcomingBookings(truckId) {
    fetch(`get_upcoming_bookings.php?truck_id=${truckId}`)
        .then(response => response.json())
        .then(bookings => {
            document.getElementById('truckId').textContent = truckId;
            const content = document.getElementById('upcomingBookingsContent');
            content.innerHTML = '';
            if (bookings.length > 0) {
                const table = document.createElement('table');
                table.border = '1';
                const thead = document.createElement('thead');
                const headerRow = document.createElement('tr');
                ['Booking ID', 'Date', 'Pickup Address', 'Delivery Address'].forEach(text => {
                    const th = document.createElement('th');
                    th.textContent = text;
                    headerRow.appendChild(th);
                });
                thead.appendChild(headerRow);
                table.appendChild(thead);
                const tbody = document.createElement('tbody');
                bookings.forEach(booking => {
                    const row = document.createElement('tr');
                    ['BookingID', 'Date', 'PickupAddress', 'DeliveryAddress'].forEach(key => {
                        const td = document.createElement('td');
                        td.textContent = booking[key];
                        row.appendChild(td);
                    });
                    tbody.appendChild(row);
                });
                table.appendChild(tbody);
                content.appendChild(table);
            } else {
                content.textContent = 'No upcoming bookings.';
            }
            document.getElementById('upcomingBookingsModal').style.display = 'block';
        });
}

function closeUpcomingBookingsModal() {
    document.getElementById('upcomingBookingsModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target == document.getElementById('upcomingBookingsModal')) {
        closeUpcomingBookingsModal();
    }
}
</script>