<?php
require_once 'db.php';
session_start();

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'Manager' && $_SESSION['role'] !== 'Dispatcher')) {
    header('Location: index.php');
    exit;
}

// Check if the booking ID is provided
if (!isset($_GET['id'])) {
    echo "Booking ID not provided.";
    exit;
}

$booking_id = $_GET['id'];

// Check if the booking is archived
$isArchived = false;
$query = 'SELECT * FROM ArchivedBookings WHERE BookingID = :booking_id';
$stmt = $db->prepare($query);
$stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
$stmt->execute();
if ($stmt->fetch(PDO::FETCH_ASSOC)) {
    $isArchived = true;
}

// Fetch booking details
$query = '
    SELECT 
        B.*,
        C.CustomerID,
        C.Email,
        C.PhoneNumber,
        C.FirstName,
        C.LastName
    FROM Bookings B
    LEFT JOIN Customers C ON B.BookingID = C.BookingID
    WHERE B.BookingID = :id
';
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $booking_id, PDO::PARAM_INT);
$stmt->execute();
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    echo "Booking not found.";
    exit;
}

// Fetch assigned movers
$moversQuery = '
    SELECT M.MoverID, M.Name, M.ContactInfo, M.OtherDetails
    FROM Movers M
    JOIN BookingMovers BM ON M.MoverID = BM.MoverID
    WHERE BM.BookingID = :id
';
$moversStmt = $db->prepare($moversQuery);
$moversStmt->bindParam(':id', $booking_id, PDO::PARAM_INT);
$moversStmt->execute();
$assignedMovers = $moversStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all movers for selection
$allMoversQuery = 'SELECT MoverID, Name FROM Movers';
$allMoversStmt = $db->prepare($allMoversQuery);
$allMoversStmt->execute();
$allMovers = $allMoversStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch assigned trucks
$trucksQuery = '
    SELECT T.TruckID, T.LicensePlate, T.Make, T.Model, T.SizeInFeet
    FROM Trucks T
    JOIN Bookings B ON T.TruckID = B.Truck
    WHERE B.BookingID = :id
';
$trucksStmt = $db->prepare($trucksQuery);
$trucksStmt->bindParam(':id', $booking_id, PDO::PARAM_INT);
$trucksStmt->execute();
$assignedTruck = $trucksStmt->fetch(PDO::FETCH_ASSOC);

// Fetch all trucks for selection
$allTrucksQuery = 'SELECT TruckID, LicensePlate, Make, Model, SizeInFeet FROM Trucks';
$allTrucksStmt = $db->prepare($allTrucksQuery);
$allTrucksStmt->execute();
$allTrucks = $allTrucksStmt->fetchAll(PDO::FETCH_ASSOC);

// Include header, nav, and footer
$pageTitle = 'Booking Details';
include 'templates/header.php';
include 'templates/nav.php';
?>


    <div class="container">
        <div class="booking-header">
            <h2>Booking <?php echo htmlspecialchars($booking['BookingID']); ?> - <?php echo $booking['BookingCompleted'] ? 'Inactive' : 'Active'; ?></h2><h2>Move Date: </h2><p><?php echo htmlspecialchars($booking['Date']); ?></p>
            <button type="button" class="edit-button" onclick="openModal('dateModal')">Edit</button>
        </div>

        <div id="dateModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('dateModal')">&times;</span>
        <h3>Edit Booking Date</h3>
        <form method="POST" action="update_booking.php">
            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['BookingID']); ?>">
            <input type="hidden" name="pickup_address" value="<?php echo htmlspecialchars($booking['PickupAddress']); ?>">
            <input type="hidden" name="dropoff_address" value="<?php echo htmlspecialchars($booking['DeliveryAddress']); ?>">
            <input type="hidden" name="instructions" value="<?php echo htmlspecialchars($booking['Instructions']); ?>">
            <input type="hidden" name="move_size" value="<?php echo htmlspecialchars($booking['MoveSize']); ?>">
            <input type="hidden" name="move_weight" value="<?php echo htmlspecialchars($booking['MoveWeight']); ?>">
            <input type="hidden" name="assigned_truck" value="<?php echo htmlspecialchars($assignedTruck['TruckID']); ?>">
            <input type="hidden" name="total_cost" value="<?php echo htmlspecialchars($booking['TotalCost']); ?>">
            <input type="hidden" name="paid" value="<?php echo htmlspecialchars($booking['Paid']); ?>">
            <label for="edit_date">Date:</label>
            <input type="date" id="edit_date" name="date" value="<?php echo htmlspecialchars($booking['Date']); ?>" required>
            <button type="submit" class="update-button">Update</button>
        </form>
    </div>
</div>

        <div class="section">
            <div class="panel">
                <h3>Customer Info</h3>
                <p>Name: <?php echo htmlspecialchars($booking['FirstName'] . ' ' . $booking['LastName']); ?></p>
                <p>Email: <?php echo htmlspecialchars($booking['Email']); ?></p>
                <p>Phone: <?php echo htmlspecialchars($booking['PhoneNumber']); ?></p>
                <button type="button" class="edit-button" onclick="openModal('customerInfoModal')">Edit</button>
            </div>
            <div class="panel">
                <h3>Pickup Address</h3>
                <p><?php echo htmlspecialchars($booking['PickupAddress']); ?></p>
                <button type="button" class="edit-button" onclick="openModal('pickupAddressModal')">Edit</button>
            </div>
            <div class="panel">
                <h3>Dropoff Address</h3>
                <p><?php echo htmlspecialchars($booking['DeliveryAddress']); ?></p>
                <button type="button" class="edit-button" onclick="openModal('dropoffAddressModal')">Edit</button>
            </div>
        </div>

        <div class="section">
            <div class="panel">
                <h3>Instructions</h3>
                <p><?php echo htmlspecialchars($booking['Instructions']); ?></p>
                <button type="button" class="edit-button" onclick="openModal('instructionsModal')">Edit</button>
            </div>
            <div class="panel">
                <h3>Move Details</h3>
                <p>Size: <?php echo htmlspecialchars($booking['MoveSize']); ?></p>
                <p>Weight: <?php echo htmlspecialchars($booking['MoveWeight']); ?></p>
                <button type="button" class="edit-button" onclick="openModal('moveDetailsModal')">Edit</button>
            </div>
        </div>

        <div class="section">
            <div class="panel">
                <h3>Assigned Movers</h3>
                <?php if (!empty($assignedMovers)): ?>
                    <ul>
                        <?php foreach ($assignedMovers as $mover): ?>
                            <li><?php echo htmlspecialchars($mover['Name']); ?> (<?php echo htmlspecialchars($mover['ContactInfo']); ?>)</li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No movers assigned.</p>
                <?php endif; ?>
                <button type="button" class="edit-button" onclick="openModal('assignedMoversModal')">Edit</button>
            </div>
            <div class="panel">
                <h3>Assigned Truck</h3>
                <?php if ($assignedTruck): ?>
                    <p>ID: <?php echo htmlspecialchars($assignedTruck['TruckID']); ?></p>
                    <p>License Plate: <?php echo htmlspecialchars($assignedTruck['LicensePlate']); ?></p>
                    <p>Make: <?php echo htmlspecialchars($assignedTruck['Make']); ?></p>
                    <p>Model: <?php echo htmlspecialchars($assignedTruck['Model']); ?></p>
                    <p>Size: <?php echo htmlspecialchars($assignedTruck['SizeInFeet']); ?> ft</p>
                <?php else: ?>
                    <p>No truck assigned.</p>
                <?php endif; ?>
                <button type="button" class="edit-button" onclick="openModal('assignedTruckModal')">Edit</button>
            </div>
        </div>
        <div class="section">
        <div class="panel">
            <h3>Activity Log</h3>
            <ul>
                <li>Booking created: <?php echo htmlspecialchars($booking['Date']); ?></li>
                <?php if ($booking['PickedUp']): ?>
                    <li>Marked as picked up: <?php echo htmlspecialchars($booking['PickedUpDate']); ?></li>
                <?php endif; ?>
                <?php if ($booking['Delivered']): ?>
                    <li>Marked as delivered: <?php echo htmlspecialchars($booking['DeliveredDate']); ?></li>
                <?php endif; ?>
                <?php if ($booking['BookingCompleted']): ?>
                    <li>Completed</li>
                <?php endif; ?>
                
        </div></div>

        <div class="section">
            <div class="panel">
                <h3>Cost</h3>
                <p>$ <?php echo htmlspecialchars($booking['TotalCost']); ?></p>
                <button type="button" class="edit-button" onclick="openModal('costModal')">Edit</button>
            </div>
            <div class="panel">
                <h3>Paid</h3>
                <p><?php echo $booking['Paid'] ? 'Yes' : 'No'; ?></p>
                <button type="button" class="edit-button" onclick="openModal('paidModal')">Edit</button>
            </div>
        </div>


        <div class="action-bar">
            <form method="POST" action="update_booking_status.php" style="display:inline;">
                <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['BookingID']); ?>">
                <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                <button type="submit" name="action" class="action-button
                " value="<?php echo $booking['BookingCompleted'] ? 'incomplete' : 'complete'; ?>" class="status-toggle <?php echo $booking['BookingCompleted'] ? 'incomplete' : 'completed'; ?>">
                    <?php echo $booking['BookingCompleted'] ? 'Mark as Incomplete' : 'Mark as Completed'; ?>
                </button>
            </form>
            <form method="POST" action="archive_booking.php" style="display:inline;">
                <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['BookingID']); ?>">
                <button type="submit" class="good-button">
                    <?php echo $isArchived ? 'Unarchive' : 'Archive'; ?>
                </button>
            </form>
            <form method="POST" action="delete_booking.php" style="display:inline;">
                <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['BookingID']); ?>">
                <button type="submit" class="delete-button" onclick="return confirm('Are you sure you want to delete this booking? This action cannot be undone.');">Delete</button>
            </form>
        </div>

    <!-- Modals -->
<div id="customerInfoModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('customerInfoModal')">&times;</span>
        <h3>Edit Customer Info</h3>
        <form method="POST" action="update_booking.php">
            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['BookingID']); ?>">
            <input type="hidden" name="date" value="<?php echo htmlspecialchars($booking['Date']); ?>">
            <input type="hidden" name="pickup_address" value="<?php echo htmlspecialchars($booking['PickupAddress']); ?>">
            <input type="hidden" name="dropoff_address" value="<?php echo htmlspecialchars($booking['DeliveryAddress']); ?>">
            <input type="hidden" name="instructions" value="<?php echo htmlspecialchars($booking['Instructions']); ?>">
            <input type="hidden" name="move_size" value="<?php echo htmlspecialchars($booking['MoveSize']); ?>">
            <input type="hidden" name="move_weight" value="<?php echo htmlspecialchars($booking['MoveWeight']); ?>">
            <input type="hidden" name="assigned_truck" value="<?php echo htmlspecialchars($assignedTruck['TruckID']); ?>">
            <input type="hidden" name="total_cost" value="<?php echo htmlspecialchars($booking['TotalCost']); ?>">
            <input type="hidden" name="paid" value="<?php echo htmlspecialchars($booking['Paid']); ?>">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($booking['FirstName']); ?>" required>
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($booking['LastName']); ?>" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($booking['Email']); ?>" required>
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($booking['PhoneNumber']); ?>" required>
            <button type="submit" class="update-button">Update</button>
        </form>
    </div>
</div>

<div id="pickupAddressModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('pickupAddressModal')">&times;</span>
        <h3>Edit Pickup Address</h3>
        <form method="POST" action="update_booking.php">
            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['BookingID']); ?>">
            <input type="hidden" name="date" value="<?php echo htmlspecialchars($booking['Date']); ?>">
            <input type="hidden" name="dropoff_address" value="<?php echo htmlspecialchars($booking['DeliveryAddress']); ?>">
            <input type="hidden" name="instructions" value="<?php echo htmlspecialchars($booking['Instructions']); ?>">
            <input type="hidden" name="move_size" value="<?php echo htmlspecialchars($booking['MoveSize']); ?>">
            <input type="hidden" name="move_weight" value="<?php echo htmlspecialchars($booking['MoveWeight']); ?>">
            <input type="hidden" name="assigned_truck" value="<?php echo htmlspecialchars($assignedTruck['TruckID']); ?>">
            <input type="hidden" name="total_cost" value="<?php echo htmlspecialchars($booking['TotalCost']); ?>">
            <input type="hidden" name="paid" value="<?php echo htmlspecialchars($booking['Paid']); ?>">
            <label for="pickup_address">Pickup Address:</label>
            <input type="text" id="pickup_address" name="pickup_address" value="<?php echo htmlspecialchars($booking['PickupAddress']); ?>" required>
            <button type="submit" class="update-button">Update</button>
        </form>
    </div>
</div>

<div id="dropoffAddressModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('dropoffAddressModal')">&times;</span>
        <h3>Edit Dropoff Address</h3>
        <form method="POST" action="update_booking.php">
            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['BookingID']); ?>">
            <input type="hidden" name="date" value="<?php echo htmlspecialchars($booking['Date']); ?>">
            <input type="hidden" name="pickup_address" value="<?php echo htmlspecialchars($booking['PickupAddress']); ?>">
            <input type="hidden" name="instructions" value="<?php echo htmlspecialchars($booking['Instructions']); ?>">
            <input type="hidden" name="move_size" value="<?php echo htmlspecialchars($booking['MoveSize']); ?>">
            <input type="hidden" name="move_weight" value="<?php echo htmlspecialchars($booking['MoveWeight']); ?>">
            <input type="hidden" name="assigned_truck" value="<?php echo htmlspecialchars($assignedTruck['TruckID']); ?>">
            <input type="hidden" name="total_cost" value="<?php echo htmlspecialchars($booking['TotalCost']); ?>">
            <input type="hidden" name="paid" value="<?php echo htmlspecialchars($booking['Paid']); ?>">
            <label for="dropoff_address">Dropoff Address:</label>
            <input type="text" id="dropoff_address" name="dropoff_address" value="<?php echo htmlspecialchars($booking['DeliveryAddress']); ?>" required>
            <button type="submit" class="update-button">Update</button>
        </form>
    </div>
</div>

<div id="instructionsModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('instructionsModal')">&times;</span>
        <h3>Edit Instructions</h3>
        <form method="POST" action="update_booking.php">
            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['BookingID']); ?>">
            <input type="hidden" name="date" value="<?php echo htmlspecialchars($booking['Date']); ?>">
            <input type="hidden" name="pickup_address" value="<?php echo htmlspecialchars($booking['PickupAddress']); ?>">
            <input type="hidden" name="dropoff_address" value="<?php echo htmlspecialchars($booking['DeliveryAddress']); ?>">
            <input type="hidden" name="move_size" value="<?php echo htmlspecialchars($booking['MoveSize']); ?>">
            <input type="hidden" name="move_weight" value="<?php echo htmlspecialchars($booking['MoveWeight']); ?>">
            <input type="hidden" name="assigned_truck" value="<?php echo htmlspecialchars($assignedTruck['TruckID']); ?>">
            <input type="hidden" name="total_cost" value="<?php echo htmlspecialchars($booking['TotalCost']); ?>">
            <input type="hidden" name="paid" value="<?php echo htmlspecialchars($booking['Paid']); ?>">
            <label for="instructions">Instructions:</label>
            <textarea id="instructions" name="instructions" required><?php echo htmlspecialchars($booking['Instructions']); ?></textarea>
            <button type="submit" class="update-button">Update</button>
        </form>
    </div>
</div>

<div id="moveDetailsModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('moveDetailsModal')">&times;</span>
        <h3>Edit Move Details</h3>
        <form method="POST" action="update_booking.php">
            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['BookingID']); ?>">
            <input type="hidden" name="date" value="<?php echo htmlspecialchars($booking['Date']); ?>">
            <input type="hidden" name="pickup_address" value="<?php echo htmlspecialchars($booking['PickupAddress']); ?>">
            <input type="hidden" name="dropoff_address" value="<?php echo htmlspecialchars($booking['DeliveryAddress']); ?>">
            <input type="hidden" name="instructions" value="<?php echo htmlspecialchars($booking['Instructions']); ?>">
            <input type="hidden" name="assigned_truck" value="<?php echo htmlspecialchars($assignedTruck['TruckID']); ?>">
            <input type="hidden" name="total_cost" value="<?php echo htmlspecialchars($booking['TotalCost']); ?>">
            <input type="hidden" name="paid" value="<?php echo htmlspecialchars($booking['Paid']); ?>">
            <label for="move_size">Size:</label>
            <input type="text" id="move_size" name="move_size" value="<?php echo htmlspecialchars($booking['MoveSize']); ?>" required>
            <label for="move_weight">Weight:</label>
            <input type="number" id="move_weight" name="move_weight" value="<?php echo htmlspecialchars($booking['MoveWeight']); ?>" required>
            <button type="submit" class="update-button">Update</button>
        </form>
    </div>
</div>

<div id="assignedMoversModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('assignedMoversModal')">&times;</span>
        <h3>Edit Assigned Movers</h3>
        <form method="POST" action="update_booking.php">
            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['BookingID']); ?>">
            <input type="hidden" name="date" value="<?php echo htmlspecialchars($booking['Date']); ?>">
            <input type="hidden" name="pickup_address" value="<?php echo htmlspecialchars($booking['PickupAddress']); ?>">
            <input type="hidden" name="dropoff_address" value="<?php echo htmlspecialchars($booking['DeliveryAddress']); ?>">
            <input type="hidden" name="instructions" value="<?php echo htmlspecialchars($booking['Instructions']); ?>">
            <input type="hidden" name="move_size" value="<?php echo htmlspecialchars($booking['MoveSize']); ?>">
            <input type="hidden" name="move_weight" value="<?php echo htmlspecialchars($booking['MoveWeight']); ?>">
            <input type="hidden" name="assigned_truck" value="<?php echo htmlspecialchars($assignedTruck['TruckID']); ?>">
            <input type="hidden" name="total_cost" value="<?php echo htmlspecialchars($booking['TotalCost']); ?>">
            <input type="hidden" name="paid" value="<?php echo htmlspecialchars($booking['Paid']); ?>">
            <div class="movers-selection">
                <?php 
                // Create an array of assigned mover IDs for easy checking
                $assignedMoverIDs = array_column($assignedMovers, 'MoverID');
                foreach ($allMovers as $mover): ?>
                    <div class="mover-option">
                        <input type="checkbox" 
                               name="assigned_movers[]" 
                               value="<?php echo htmlspecialchars($mover['MoverID']); ?>"
                               id="mover_<?php echo htmlspecialchars($mover['MoverID']); ?>"
                               <?php echo in_array($mover['MoverID'], $assignedMoverIDs) ? 'checked' : ''; ?>>
                        <label for="mover_<?php echo htmlspecialchars($mover['MoverID']); ?>">
                            <?php echo htmlspecialchars($mover['Name']); ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="update-button">Update</button>
        </form>
    </div>
</div>

<div id="assignedTruckModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('assignedTruckModal')">&times;</span>
        <h3>Edit Assigned Truck</h3>
        <form method="POST" action="update_booking.php">
            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['BookingID']); ?>">
            <input type="hidden" name="date" value="<?php echo htmlspecialchars($booking['Date']); ?>">
            <input type="hidden" name="pickup_address" value="<?php echo htmlspecialchars($booking['PickupAddress']); ?>">
            <input type="hidden" name="dropoff_address" value="<?php echo htmlspecialchars($booking['DeliveryAddress']); ?>">
            <input type="hidden" name="instructions" value="<?php echo htmlspecialchars($booking['Instructions']); ?>">
            <input type="hidden" name="move_size" value="<?php echo htmlspecialchars($booking['MoveSize']); ?>">
            <input type="hidden" name="move_weight" value="<?php echo htmlspecialchars($booking['MoveWeight']); ?>">
            <input type="hidden" name="total_cost" value="<?php echo htmlspecialchars($booking['TotalCost']); ?>">
            <input type="hidden" name="paid" value="<?php echo htmlspecialchars($booking['Paid']); ?>">
            <select name="assigned_truck" id="assigned_truck" required>
                <option value="">Select a truck</option>
                <?php foreach ($allTrucks as $truck): ?>
                    <option value="<?php echo htmlspecialchars($truck['TruckID']); ?>" <?php echo $assignedTruck && $assignedTruck['TruckID'] == $truck['TruckID'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($truck['Make'] . ' ' . $truck['Model'] . ' (' . $truck['LicensePlate'] . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="update-button">Update</button>
        </form>
    </div>
</div>

    <div id="costModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('costModal')">&times;</span>
            <h3>Edit Cost</h3>
            <form method="POST" action="update_booking.php">
                <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['BookingID']); ?>">
                <label for="total_cost">Cost:</label>
                <input type="decimal" id="total_cost" name="total_cost" value="<?php echo htmlspecialchars($booking['TotalCost']); ?>" required>
                <button type="submit" class="update-button">Update</button>
            </form>
        </div>
    </div>

    <div id="paidModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('paidModal')">&times;</span>
            <h3>Edit Paid Status</h3>
            <form method="POST" action="update_booking.php">
                <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['BookingID']); ?>">
                <label for="paid">Paid:</label>
                <select id="paid" name="paid" required>
                    <option value="0" <?php echo $booking['Paid'] == 0 ? 'selected' : ''; ?>>No</option>
                    <option value="1" <?php echo $booking['Paid'] == 1 ? 'selected' : ''; ?>>Yes</option>
                </select>
                <button type="submit" class="update-button">Update</button>
            </form>
        </div>
    </div>

    <?php
    include 'templates/footer.php';

?>

<script>

function fetchAvailableTrucks(date) {
    fetch(`get_available_trucks.php?date=${date}`)
        .then(response => response.json())
        .then(trucks => {
            const select = document.getElementById('assigned_truck');
            select.innerHTML = '<option value="">Select a truck</option>';
            trucks.forEach(truck => {
                const option = document.createElement('option');
                option.value = truck.TruckID;
                option.textContent = `${truck.Make} ${truck.Model} (${truck.LicensePlate})`;
                select.appendChild(option);
            });
        });
}

document.getElementById('edit_date').addEventListener('change', function() {
    fetchAvailableTrucks(this.value);
});

function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modals = document.getElementsByClassName('modal');
    for (let i = 0; i < modals.length; i++) {
        if (event.target == modals[i]) {
            modals[i].style.display = 'none';
        }
    }
}
</script>