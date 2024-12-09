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

$pageTitle = 'Manage Bookings';
include 'templates/header.php';
include 'templates/nav.php';
?>

<?php
require_once 'db.php'; // Database connection

$search = $_GET['search'] ?? '';

// Fetch current bookings with customer details
$currentBookingsQuery = '
    SELECT 
        B.BookingID,
        C.FirstName,
        C.LastName,
        B.MoveWeight,
        B.MoveSize,
        B.Date,
        B.PickedUp,
        B.Delivered,
        B.Paid,
        B.BookingCompleted
    FROM Bookings B
    LEFT JOIN Customers C ON B.BookingID = C.BookingID
    LEFT JOIN ArchivedBookings A ON B.BookingID = A.BookingID
    WHERE A.BookingID IS NULL
';

if ($search) {
    $currentBookingsQuery .= ' AND (B.BookingID LIKE :search OR C.FirstName LIKE :search OR C.LastName LIKE :search)';
}

$currentBookingsQuery .= ' ORDER BY B.Date DESC';

$stmt = $db->prepare($currentBookingsQuery);

if ($search) {
    $searchParam = '%' . $search . '%';
    $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
}

$stmt->execute();
$currentBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch archived bookings with customer details

$archivedBookingsQuery = '
    SELECT 
        B.BookingID,
        C.FirstName,
        C.LastName,
        B.MoveWeight,
        B.MoveSize,
        B.Date,
        B.PickedUp,
        B.Delivered,
        B.Paid,
        B.BookingCompleted,
        A.DateArchived
    FROM Bookings B
    LEFT JOIN Customers C ON B.BookingID = C.BookingID
    LEFT JOIN ArchivedBookings A ON B.BookingID = A.BookingID
    WHERE A.BookingID IS NOT NULL
';

if ($search) {
    $archivedBookingsQuery .= ' AND (B.BookingID LIKE :search OR C.FirstName LIKE :search OR C.LastName LIKE :search)';
}

$archivedBookingsQuery .= ' ORDER BY A.DateArchived DESC';

$stmt = $db->prepare($archivedBookingsQuery);

if ($search) {
    $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
}

$stmt->execute();
$archivedBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="tabs">
        <button class="tab-button" onclick="openTab(event, 'currentBookings')">Current Bookings</button>
        <button class="tab-button" onclick="openTab(event, 'archivedBookings')">Archived Bookings</button>
    </div>

    <div class="panel"><div class="container">
            <div class="search-bar">
                <form method="GET" action="bookings.php">
                    <input type="text" class="searchBox" name="search" placeholder="Search by Customer Name or Booking ID" value="<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES); ?>">
                    <button class= "action-button" type="submit">Search</button>
                </form>
            </div></div></div>

    <div id="currentBookings" class="tab-content">
        <div class="panel">
            <!-- Display Current Bookings Table -->
            <h2>Current Bookings</h2>
            <table border="1">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Customer Name</th>
                        <th>Move Weight (Kg)</th>
                        <th>Move Size</th>
                        <th>Move Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($currentBookings as $booking): ?>
                        <tr onclick="window.location.href='booking_details.php?id=<?php echo htmlspecialchars($booking['BookingID']); ?>'">
                            <td><?php echo htmlspecialchars($booking['BookingID']); ?></td>
                            <td><?php echo htmlspecialchars($booking['FirstName'] . ' ' . $booking['LastName']); ?></td>
                            <td><?php echo htmlspecialchars($booking['MoveWeight']); ?></td>
                            <td><?php echo htmlspecialchars($booking['MoveSize']); ?></td>
                            <td><?php echo htmlspecialchars($booking['Date']); ?></td>
                            <td>
                                <?php
                                if ($booking['BookingCompleted']) {
                                    echo 'Completed';
                                } elseif ($booking['PickedUp'] && $booking['Delivered']) {
                                    echo 'Delivered';
                                } elseif ($booking['PickedUp']) {
                                    echo 'Picked Up';
                                } elseif ($booking['Paid']) {
                                    echo 'Paid';
                                } else {
                                    echo 'Pending';
                                }
                                ?>
                            </td>
                            <td>
                                <form method="POST" action="update_booking_status.php">
                                    <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['BookingID']); ?>">
                                    <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                                    <button type="submit" name="action" value="<?php echo $booking['BookingCompleted'] == '1' ? 'in_progress' : 'complete'; ?>" class="status-toggle <?php echo $booking['BookingCompleted'] == '1' ? 'completed' : 'pending'; ?>">
                                        <?php echo $booking['BookingCompleted'] == '1' ? 'Completed' : 'Pending'; ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="archivedBookings" class="tab-content">
        <div class="panel">
            <!-- Display Archived Bookings Table -->
            <h2>Archived Bookings</h2>
            <table border="1">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Customer Name</th>
                        <th>Move Weight</th>
                        <th>Move Size</th>
                        <th>Move Date</th>
                        <th>Status</th>
                        <th>Date Archived</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($archivedBookings as $booking): ?>
                        <tr onclick="window.location.href='booking_details.php?id=<?php echo htmlspecialchars($booking['BookingID']); ?>'">
                            <td><?php echo htmlspecialchars($booking['BookingID']); ?></td>
                            <td><?php echo htmlspecialchars($booking['FirstName'] . ' ' . $booking['LastName']); ?></td>
                            <td><?php echo htmlspecialchars($booking['MoveWeight']); ?></td>
                            <td><?php echo htmlspecialchars($booking['MoveSize']); ?></td>
                            <td><?php echo htmlspecialchars($booking['Date']); ?></td>
                            <td>
                                <?php
                                if ($booking['BookingCompleted']) {
                                    echo 'Completed';
                                } elseif ($booking['PickedUp'] && $booking['Delivered']) {
                                    echo 'Delivered';
                                } elseif ($booking['PickedUp']) {
                                    echo 'Picked Up';
                                } elseif ($booking['Paid']) {
                                    echo 'Paid';
                                } else {
                                    echo 'Pending';
                                }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($booking['DateArchived']); ?></td>
                            <td>
                                <form method="POST" action="update_booking_status.php">
                                    <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['BookingID']); ?>">
                                    <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                                    <button type="submit" name="action" value="<?php echo $booking['BookingCompleted'] == '1' ? 'in_progress' : 'complete'; ?>" class="status-toggle <?php echo $booking['BookingCompleted'] == '1' ? 'completed' : 'pending'; ?>">
                                        <?php echo $booking['BookingCompleted'] == '1' ? 'Completed' : 'Pending'; ?>
                                    </button>
                                </form>
                                <button onclick="showEditForm('<?php echo htmlspecialchars($booking['BookingID']); ?>')" class="edit-button">Edit</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const activeTab = localStorage.getItem('activeTab') || 'currentBookings';
    document.getElementById(activeTab).style.display = 'block';
    document.querySelector(`button[onclick="openTab(event, '${activeTab}')"]`).classList.add('active');
});

function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tab-button");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
    localStorage.setItem('activeTab', tabName);
}
</script>