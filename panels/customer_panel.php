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
    b.CreatedDate,  -- Include CreatedDate
    b.Truck,
    (SELECT COUNT(*) FROM Messages m WHERE m.CustomerID = :customer_id AND m.IsRead = 0) AS NewMessages
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

// Fetch unavailable dates
$unavailableDatesQuery = "
    SELECT Date
    FROM Bookings
    GROUP BY Date
    HAVING COUNT(*) >= 3
";
$unavailableDatesStmt = $db->prepare($unavailableDatesQuery);
$unavailableDatesStmt->execute();
$unavailableDates = $unavailableDatesStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<h2>Submit New Move</h2>
<div class="collapsible-section">
    <button type="button" class="collapse-toggle" onclick="toggleSection('submit-move-form')">
        <span class="toggle-icon">▼</span> Submit New Move
    </button>
    <div id="submit-move-form" class="collapsible-content">
        <div class="panel">
            <form id="booking-form" method="POST" action="process_booking.php">
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" required>
                </div>
                <div class="address-section">
                    <div class="address-group">
                        <h3>Pickup Address</h3>
                        <div class="form-group">
                            <label for="pickup_street">Street:</label>
                            <input type="text" id="pickup_street" name="pickup_street" required>
                        </div>
                        <div class="form-group">
                            <label for="pickup_city">City:</label>
                            <input type="text" id="pickup_city" name="pickup_city" required>
                        </div>
                        <div class="form-group">
                            <label for="pickup_state">State:</label>
                            <input type="text" id="pickup_state" name="pickup_state" required>
                        </div>
                        <div class="form-group">
                            <label for="pickup_postal_code">Postal Code:</label>
                            <input type="text" id="pickup_postal_code" name="pickup_postal_code" required>
                        </div>
                    </div>
                    <div class="address-group">
                        <h3>Delivery Address</h3>
                        <div class="form-group">
                            <label for="delivery_street">Street:</label>
                            <input type="text" id="delivery_street" name="delivery_street" required>
                        </div>
                        <div class="form-group">
                            <label for="delivery_city">City:</label>
                            <input type="text" id="delivery_city" name="delivery_city" required>
                        </div>
                        <div class="form-group">
                            <label for="delivery_state">State:</label>
                            <input type="text" id="delivery_state" name="delivery_state" required>
                        </div>
                        <div class="form-group">
                            <label for="delivery_postal_code">Postal Code:</label>
                            <input type="text" id="delivery_postal_code" name="delivery_postal_code" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="move_size">Move Size:</label>
                    <select id="move_size" name="move_size" required>
                        <option value="1-Bed Apartment">1-Bed Apartment</option>
                        <option value="2-Bed Apartment">2-Bed Apartment</option>
                        <option value="3-Bed House">3-Bed House</option>
                        <option value="4-Bed House">4-Bed House</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="move_weight">Move Weight (kg):</label>
                    <select id="move_weight" name="move_weight" required>
                        <option value="500">500 kg</option>
                        <option value="1000">1000 kg</option>
                        <option value="1500">1500 kg</option>
                        <option value="2000">2000 kg</option>
                        <option value="2500">2500 kg</option>
                        <option value="3000">3000 kg</option>
                        <option value="3500">3500 kg</option>
                        <option value="4000">4000 kg</option>
                        <option value="4500">4500 kg</option>
                        <option value="5000">5000 kg</option>
                        <option value="custom">Custom</option>
                    </select>
                    <input type="number" id="custom_move_weight" name="custom_move_weight" min="500" style="display:none;" placeholder="Enter custom weight">
                </div>
                <div class="form-group">
                    <label for="instructions">Instructions:</label>
                    <textarea id="instructions" name="instructions" rows="3"></textarea>
                </div>
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>
</div>

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
    <th>Messages</th>
    <th>Status</th> <!-- New Status Column -->
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
            <td>
                <a href="customer_messages.php?booking_id=<?php echo htmlspecialchars($row['BookingID']); ?>" class="message-button <?php echo $row['NewMessages'] > 0 ? 'flash' : ''; ?>">
                    Message
                    <?php if ($row['NewMessages'] > 0): ?>
                        <span class="new-message">!</span>
                    <?php endif; ?>
                </a>
            </td>
            <td>
                <button onclick="showActivityLog(<?php echo htmlspecialchars($row['BookingID']); ?>)">View Status</button>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
    <p>No bookings found for CustomerID <?php echo htmlspecialchars($customer_id); ?>.</p>
<?php endif; ?>

<!-- Activity Log Modal -->
<div id="activityLogModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('activityLogModal')">&times;</span>
        <h3>Activity Log</h3>
        <ul id="activityLogList"></ul>
    </div>
</div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
            const unavailableDates = <?php echo json_encode($unavailableDates); ?>;
            const dateInput = document.getElementById('date');
            dateInput.addEventListener('input', function() {
                const selectedDate = this.value;
                if (unavailableDates.includes(selectedDate)) {
                    alert('Selected date is fully booked. Please choose another date.');
                    this.value = '';
                }
            });

            const moveWeightSelect = document.getElementById('move_weight');
            moveWeightSelect.addEventListener('change', function() {
                const customWeightInput = document.getElementById('custom_move_weight');
                if (this.value === 'custom') {
                    customWeightInput.style.display = 'block';
                    customWeightInput.required = true;
                } else {
                    customWeightInput.style.display = 'none';
                    customWeightInput.required = false;
                }
            });

            document.getElementById('calculate-cost').addEventListener('click', function(event) {
                event.preventDefault();
                calculateTotalCost();
            });

            document.getElementById('confirm-booking').addEventListener('click', function(event) {
                event.preventDefault();
                document.getElementById('booking-form').submit();
            });
        });

        function calculateTotalCost() {
            const moveSize = document.getElementById('move_size').value;
            const moveWeight = document.getElementById('move_weight').value === 'custom' ? document.getElementById('custom_move_weight').value : document.getElementById('move_weight').value;
            const baseCosts = {
                '1-Bed Apartment': 200,
                '2-Bed Apartment': 400,
                '3-Bed House': 600,
                '4-Bed House': 800
            };
            const weightCost = moveWeight * 0.50;
            let totalCost = baseCosts[moveSize] + weightCost;

            const maxWeights = {
                '1-Bed Apartment': 1000,
                '2-Bed Apartment': 2000,
                '3-Bed House': 3000,
                '4-Bed House': 5000
            };
            if (moveWeight > maxWeights[moveSize]) {
                totalCost += 0.10 * totalCost;
            }

            const numMovers = moveWeight <= 2000 ? 2 : moveWeight <= 3000 ? 3 : 4;
            const moverCost = numMovers * 50 * 4; // 4 hours
            totalCost += moverCost;

            document.getElementById('total-cost').innerText = 'Total Cost: $' + totalCost.toFixed(2);
            document.getElementById('credit-card-section').style.display = 'block';
        }
        function toggleSection(id) {
            const section = document.getElementById(id).parentElement;
            section.classList.toggle('collapsed');
        }
function showActivityLog(bookingId) {
    fetch(`get_booking_activity.php?booking_id=${bookingId}`)
        .then(response => response.json())
        .then(data => {
            const activityLogList = document.getElementById('activityLogList');
            activityLogList.innerHTML = '';

            const createdItem = document.createElement('li');
            createdItem.textContent = `Booking created: ${data.CreatedDate}`;
            activityLogList.appendChild(createdItem);

            if (data.PickedUp) {
                const pickedUpItem = document.createElement('li');
                pickedUpItem.textContent = `Picked up: ${data.TimePickedUp}`;
                activityLogList.appendChild(pickedUpItem);
            }

            if (data.Delivered) {
                const deliveredItem = document.createElement('li');
                deliveredItem.textContent = `Delivered: ${data.TimeDelivered}`;
                activityLogList.appendChild(deliveredItem);
            }

            if (data.BookingCompleted) {
                const completedItem = document.createElement('li');
                completedItem.textContent = 'Booking completed';
                activityLogList.appendChild(completedItem);
            }

            document.getElementById('activityLogModal').style.display = 'block';
        });
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('activityLogModal');
    if (event.target == modal) {
        closeModal('activityLogModal');
    }
}


// Initialize sections as collapsed
document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('.collapsible-section');
    sections.forEach(section => {
        section.classList.add('collapsed');
    });
});

        
    </script>
