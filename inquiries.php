<?php
require_once 'db.php';
session_start();

// Only allow access for Manager or Dispatcher roles
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'Manager' && $_SESSION['role'] !== 'Dispatcher')) {
    header('Location: index.php');
    exit;
}

$error = '';

// Handle deletion of inquiries
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inquiry_id'])) {
    $inquiry_id = $_POST['inquiry_id'];

    try {
        $query = 'DELETE FROM MoveRequests WHERE RequestID = :inquiry_id';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':inquiry_id', $inquiry_id, PDO::PARAM_INT);
        $stmt->execute();
        header('Location: inquiries.php');
        exit;
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Fetch all inquiries
try {
    $query = '
        SELECT 
            MR.RequestID,
            MR.Message,
            C.FirstName,
            C.LastName,
            C.Email,
            C.PhoneNumber
        FROM MoveRequests MR
        JOIN Customers C ON MR.CustomerID = C.CustomerID
        ORDER BY MR.RequestID DESC
    ';
    $stmt = $db->prepare($query);
    $stmt->execute();
    $inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database Error: " . $e->getMessage();
}

$pageTitle = 'View Inquiries';
include 'templates/header.php';
include 'templates/nav.php';
?>

<div class="container">
    <h2>Inquiries</h2>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <input type="text" class="searchBox" id="searchBox" onkeyup="searchInquiries()" placeholder="Search for inquiries..">
    <table border="1" id="inquiriesTable">
        <thead>
            <tr>
                <th>Inquiry ID</th>
                <th>Customer Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Message</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inquiries as $inquiry): ?>
                <tr>
                    <td><?php echo htmlspecialchars($inquiry['RequestID']); ?></td>
                    <td><?php echo htmlspecialchars($inquiry['FirstName'] . ' ' . $inquiry['LastName']); ?></td>
                    <td><?php echo htmlspecialchars($inquiry['Email']); ?></td>
                    <td><?php echo htmlspecialchars($inquiry['PhoneNumber']); ?></td>
                    <td class="message-cell" data-message="<?php echo htmlspecialchars($inquiry['Message'], ENT_QUOTES); ?>">
                    <?php echo htmlspecialchars($inquiry['Message']); ?>
                    </td>
                    <td>
                        <form method="POST" action="inquiries.php" style="display:inline;">
                            <input type="hidden" name="inquiry_id" value="<?php echo htmlspecialchars($inquiry['RequestID']); ?>">
                            <button type="submit" class="delete-button" onclick="return confirm('Are you sure you want to delete this inquiry? This action cannot be undone.');">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Message Modal -->
<div id="messageModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeMessageModal()">&times;</span>
        <h3>Message</h3>
        <p id="fullMessage"></p>
    </div>
</div>

<?php include 'templates/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageCells = document.querySelectorAll('.message-cell');
    messageCells.forEach(function(cell) {
        cell.addEventListener('click', function() {
            const message = cell.getAttribute('data-message');
            openMessageModal(message);
        });
    });
});

function openMessageModal(message) {
    document.getElementById('fullMessage').textContent = message;
    document.getElementById('messageModal').style.display = 'block';
}

function closeMessageModal() {
    document.getElementById('messageModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('messageModal');
    if (event.target == modal) {
        closeMessageModal();
    }
}

function searchInquiries() {
    const input = document.getElementById('searchBox');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('inquiriesTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        const tdArray = tr[i].getElementsByTagName('td');
        let found = false;
        for (let j = 0; j < tdArray.length; j++) {
            if (tdArray[j]) {
                if (tdArray[j].innerHTML.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        tr[i].style.display = found ? '' : 'none';
    }
}
</script>