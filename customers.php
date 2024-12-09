<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: index.php');
    exit;
}

// Only allow access for Manager or Dispatcher roles
if ($_SESSION['role'] !== 'Manager' && $_SESSION['role'] !== 'Dispatcher') {
    header('Location: dashboard.php');
    exit;
}

$pageTitle = 'Manage Customers';
include 'templates/header.php';
include 'templates/nav.php';
?>

<?php
require_once 'db.php'; // Database connection

$search = $_GET['search'] ?? '';

// Fetch customers with their booking IDs and new message count
$customersQuery = '
    SELECT 
        C.CustomerID,
        C.FirstName,
        C.LastName,
        C.Email,
        C.PhoneNumber,
        B.BookingID,
        (SELECT COUNT(*) FROM Messages M WHERE M.CustomerID = C.CustomerID AND M.IsRead = 0) AS NewMessages
    FROM Customers C
    LEFT JOIN Bookings B ON C.BookingID = B.BookingID
    WHERE 1=1
';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 

if ($search) {
    $customersQuery .= ' AND (C.FirstName LIKE :search OR C.LastName LIKE :search OR C.Email LIKE :search)';
}

$customersQuery .= ' ORDER BY C.LastName, C.FirstName';

$stmt = $db->prepare($customersQuery);

if ($search) {
    $searchParam = '%' . $search . '%';
    $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
}

$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Manage Customers</h2>

    <div class="panel"><div class="container">
            <div class="search-bar">
                <form method="GET" action="customers.php">
                    <input type="text" class="searchBox" name="search" placeholder="Search by Customer Name or Email" value="<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES); ?>">
                    <button class="action-button" type="submit">Search</button>
                </form>
            </div></div></div>

    <div id="currentCustomers">
        <div class="panel">
            <!-- Display Current Customers Table -->
            
            <table border="1">
                <thead>
                    <tr>
                        <th>Customer ID</th>
                        <th>Booking ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Messages</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($customer['CustomerID']); ?></td>
                            <td><?php echo htmlspecialchars($customer['BookingID'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($customer['FirstName']); ?></td>
                            <td><?php echo htmlspecialchars($customer['LastName']); ?></td>
                            <td><?php echo htmlspecialchars($customer['Email']); ?></td>
                            <td><?php echo htmlspecialchars($customer['PhoneNumber']); ?></td>
                            <td>
                                <?php if ($customer['NewMessages'] > 0): ?>
                                    <span class="new-message">!</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button onclick="window.location.href='customer_messages.php?id=<?php echo htmlspecialchars($customer['CustomerID']); ?>'" class="edit-button">View Messages</button>
                                <button onclick="openEditModal(<?php echo htmlspecialchars($customer['CustomerID']); ?>, '<?php echo htmlspecialchars($customer['FirstName']); ?>', '<?php echo htmlspecialchars($customer['LastName']); ?>', '<?php echo htmlspecialchars($customer['Email']); ?>', '<?php echo htmlspecialchars($customer['PhoneNumber']); ?>')" class="edit-button">Edit</button>
                                <form method="POST" action="delete_customer.php" style="display:inline;">
                                    <input type="hidden" name="customer_id" value="<?php echo htmlspecialchars($customer['CustomerID']); ?>">
                                    <button type="submit" class="delete-button" onclick="return confirm('Are you sure you want to delete this customer? This action cannot be undone.');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Customer Modal -->
<div id="editCustomerModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h3>Edit Customer</h3>
        <form method="POST" action="update_customer.php">
            <input type="hidden" id="edit_customer_id" name="customer_id">
            <div class="form-group">
                <label for="edit_first_name">First Name:</label>
                <input type="text" id="edit_first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="edit_last_name">Last Name:</label>
                <input type="text" id="edit_last_name" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="edit_email">Email:</label>
                <input type="email" id="edit_email" name="email" required>
            </div>
            <div class="form-group">
                <label for="edit_phone_number">Phone Number:</label>
                <input type="text" id="edit_phone_number" name="phone_number" required>
            </div>
            <div class="form-group">
                <label for="edit_password">Password:</label>
                <input type="password" id="edit_password" name="password">
            </div>
            <button type="submit" class="update-button">Update</button>
        </form>
    </div>
</div>

<?php include 'templates/footer.php'; ?>

<script>


function openEditModal(customerId, firstName, lastName, email, phoneNumber) {
    document.getElementById('edit_customer_id').value = customerId;
    document.getElementById('edit_first_name').value = firstName;
    document.getElementById('edit_last_name').value = lastName;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_phone_number').value = phoneNumber;
    document.getElementById('editCustomerModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editCustomerModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('editCustomerModal');
    if (event.target == modal) {
        closeEditModal();
    }
}
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the first tab as active
    document.querySelector('.tab-button').click();
});

function openTab(evt, tabName) {
    var i, tabcontent, tabbuttons;
    tabcontent = document.getElementsByClassName('tab-content');
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = 'none';
    }
    tabbuttons = document.getElementsByClassName('tab-button');
    for (i = 0; i < tabbuttons.length; i++) {
        tabbuttons[i].className = tabbuttons[i].className.replace(' active', '');
    }
    document.getElementById(tabName).style.display = 'block';
    evt.currentTarget.className += ' active';
}
</script>