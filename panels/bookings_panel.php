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
    GROUP BY B.BookingID
    ORDER BY B.Date DESC
';
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
                        <input type="hidden" name="current_status" value="<?php echo htmlspecialchars($booking['BookingCompleted']); ?>">
                        <button type="submit" class="status-toggle <?php echo $booking['BookingCompleted'] == '1' ? 'completed' : 'pending'; ?>">
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

<!-- Add Booking Form -->
<div class="collapsible-section">
    <button type="button" class="collapse-toggle" onclick="toggleSection('add-booking')">
        <span class="toggle-icon">▼</span> Add New Booking
    </button>
    <div id="add-booking" class="collapsible-content">
        <form method="POST" action="create_booking.php">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required onchange="fetchAvailableTrucks(this.value, 'truck')">
            <br>
            <label for="pickup">Pickup Address:</label>
            <input type="text" id="pickup" name="pickup" required>
            <br>
            <label for="delivery">Delivery Address:</label>
            <input type="text" id="delivery" name="delivery" required>
            <br>
            <label for="truck">Truck ID:</label>
            <select id="truck" name="truck" required>
                <option value="">Select a truck</option>
            </select>
            <br>
            <label for="completed">Completed:</label>
            <input type="checkbox" id="completed" name="completed" value="0">
            <br>
            <label>Assign Movers:</label>
            <div class="movers-selection">
                <?php foreach ($movers as $mover): ?>
                    <div class="mover-option">
                        <input type="checkbox" 
                               name="assigned_movers[]" 
                               value="<?php echo htmlspecialchars($mover['MoverID']); ?>"
                               id="mover_<?php echo htmlspecialchars($mover['MoverID']); ?>">
                        <label for="mover_<?php echo htmlspecialchars($mover['MoverID']); ?>">
                            <?php echo htmlspecialchars($mover['Name']); ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="submit">Add Booking</button>
        </form>
    </div>
</div>

<!-- Edit form modal/popup -->
<div id="editBookingModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeEditForm()">&times;</span>
        <h3>Edit Booking</h3>
        <form method="POST" action="update_booking.php" id="editBookingForm">
            <input type="hidden" name="booking_id" id="edit_booking_id">
            
            <div class="form-group">
                <label for="edit_date">Date:</label>
                <input type="date" id="edit_date" name="date" required onchange="fetchAvailableTrucks(this.value, 'edit_truck', document.getElementById('edit_truck').value)">
            </div>
            
            <div class="form-group">
                <label for="edit_pickup">Pickup Address:</label>
                <input type="text" id="edit_pickup" name="pickup" required>
            </div>
            
            <div class="form-group">
                <label for="edit_delivery">Delivery Address:</label>
                <input type="text" id="edit_delivery" name="delivery" required>
            </div>
            
            <div class="form-group">
                <label for="edit_truck">Truck ID:</label>
                <select id="edit_truck" name="truck" required>
                    <option value="">Select a truck</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Assigned Movers:</label>
                <div class="movers-selection">
                    <?php foreach ($movers as $mover): ?>
                        <div class="mover-option">
                            <input type="checkbox" 
                                   name="assigned_movers[]" 
                                   value="<?php echo htmlspecialchars($mover['MoverID']); ?>"
                                   id="edit_mover_<?php echo htmlspecialchars($mover['MoverID']); ?>">
                            <label for="edit_mover_<?php echo htmlspecialchars($mover['MoverID']); ?>">
                                <?php echo htmlspecialchars($mover['Name']); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="button-group">
                <button type="submit" class="update-button">Update Booking</button>
                <button type="button" class="delete-button" onclick="deleteBooking()">Delete Booking</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleSection(id) {
    const section = document.getElementById(id).parentElement;
    section.classList.toggle('collapsed');
}

// Initialize sections as collapsed
document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('.collapsible-section');
    sections.forEach(section => {
        section.classList.add('collapsed');
    });
});

function showEditForm(bookingId) {
    // Fetch booking details via AJAX
    fetch(`get_booking.php?id=${bookingId}`)
        .then(response => response.json())
        .then(booking => {
            document.getElementById('edit_booking_id').value = booking.BookingID;
            document.getElementById('edit_date').value = booking.Date;
            document.getElementById('edit_pickup').value = booking.PickupAddress;
            document.getElementById('edit_delivery').value = booking.DeliveryAddress;
            
            // Fetch available trucks for the booking date and include the originally selected truck
            fetchAvailableTrucks(booking.Date, 'edit_truck', booking.Truck);
            
            // Reset and set mover checkboxes
            const movers = booking.AssignedMovers?.split(',') || [];
            document.querySelectorAll('[name="assigned_movers[]"]').forEach(checkbox => {
                checkbox.checked = movers.includes(checkbox.value);
            });
            
            document.getElementById('editBookingModal').style.display = 'block';
        });
}

function closeEditForm() {
    document.getElementById('editBookingModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target == document.getElementById('editBookingModal')) {
        closeEditForm();
    }
}

function fetchAvailableTrucks(date, elementId, selectedTruckId = null) {
    fetch(`get_available_trucks.php?date=${date}`)
        .then(response => response.json())
        .then(trucks => {
            const select = document.getElementById(elementId);
            select.innerHTML = '<option value="">Select a truck</option>';

            // Fetch the originally selected truck details if provided
            let originalTruckIncluded = false;
            if (selectedTruckId) {
                trucks.forEach(truck => {
                    if (truck.TruckID == selectedTruckId) {
                        originalTruckIncluded = true;
                    }
                });
            }

            // Add the originally selected truck if it's not in the available trucks list
            if (selectedTruckId && !originalTruckIncluded) {
                fetch(`get_truck_details.php?truck_id=${selectedTruckId}`)
                    .then(response => response.json())
                    .then(truck => {
                        const originalOption = document.createElement('option');
                        originalOption.value = truck.TruckID;
                        originalOption.textContent = `${truck.TruckID} - ${truck.Make} ${truck.Model}, ${truck.SizeInFeet} ft, ${truck.LicensePlate} (current)`;
                        select.appendChild(originalOption);
                    });
            }

            trucks.forEach(truck => {
                const option = document.createElement('option');
                option.value = truck.TruckID;
                option.textContent = `${truck.TruckID} - ${truck.Make} ${truck.Model}, ${truck.SizeInFeet} ft, ${truck.LicensePlate}`;
                select.appendChild(option);
            });

            // Set the selected truck as the default selected option
            if (selectedTruckId) {
                select.value = selectedTruckId;
            }
        });
}

document.write(`
    <form id="deleteBookingForm" method="POST" action="delete_booking.php" style="display: none;">
        <input type="hidden" name="booking_id" id="delete_booking_id">
    </form>
`);

function deleteBooking() {
    if (confirm('Are you sure you want to delete this booking? This action cannot be undone.')) {
        const bookingId = document.getElementById('edit_booking_id').value;
        document.getElementById('delete_booking_id').value = bookingId;
        document.getElementById('deleteBookingForm').submit();
    }
}
</script>