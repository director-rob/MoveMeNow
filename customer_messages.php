<?php
require_once 'db.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Customer', 'Manager', 'Dispatcher'])) {
    header('Location: index.php');
    exit;
}

$customer_id = $_SESSION['role'] === 'Customer' ? $_SESSION['user_id'] : ($_GET['id'] ?? null);

if (!$customer_id) {
    header('Location: customers.php');
    exit;
}

// Fetch customer details
$query = 'SELECT * FROM Customers WHERE CustomerID = :customer_id';
$stmt = $db->prepare($query);
$stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
$stmt->execute();
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    header('Location: customers.php');
    exit;
}

// Fetch messages
$messagesQuery = 'SELECT * FROM Messages WHERE CustomerID = :customer_id ORDER BY SentAt ASC';
$messagesStmt = $db->prepare($messagesQuery);
$messagesStmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
$messagesStmt->execute();
$messages = $messagesStmt->fetchAll(PDO::FETCH_ASSOC);

// Mark messages as read
$updateQuery = 'UPDATE Messages SET IsRead = 1 WHERE CustomerID = :customer_id AND IsRead = 0';
$updateStmt = $db->prepare($updateQuery);
$updateStmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
$updateStmt->execute();

function sendEmailNotification($to, $subject, $message) {
    $headers = 'From: no-reply@movemenow.com' . "\r\n" .
               'Reply-To: no-reply@movemenow.com' . "\r\n" .
               'X-Mailer: PHP/' . phpversion();
    mail($to, $subject, $message, $headers);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if ($message) {
        $sender = $_SESSION['role'] === 'Customer' ? 'Customer' : 'Employee';
        $insertQuery = 'INSERT INTO Messages (CustomerID, Message, Sender) VALUES (:customer_id, :message, :sender)';
        $insertStmt = $db->prepare($insertQuery);
        $insertStmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $insertStmt->bindParam(':message', $message, PDO::PARAM_STR);
        $insertStmt->bindParam(':sender', $sender, PDO::PARAM_STR);
        $insertStmt->execute();

        // Send email notification if the sender is an employee
        if ($sender === 'Employee') {
            $customerEmail = $customer['Email'];
            $subject = 'New Message from MoveMeNow';
            $emailMessage = "You have received a new message from MoveMeNow:\n\n" . $message;
            sendEmailNotification($customerEmail, $subject, $emailMessage);
        }

        header('Location: customer_messages.php?id=' . $customer_id);
        exit;
    }
}

$pageTitle = 'Customer Messages';
include 'templates/header.php';
include 'templates/nav.php';
?>

<div class="container">
    <h2>Messages with <?php echo htmlspecialchars($customer['FirstName'] . ' ' . $customer['LastName']); ?></h2>
    <div class="messages">
        <?php foreach ($messages as $msg): ?>
            <div class="message <?php echo $msg['Sender'] === 'Customer' ? 'sent' : 'received'; ?>">
                <p><?php echo htmlspecialchars($msg['Message']); ?></p>
                <span class="timestamp"><?php echo htmlspecialchars($msg['SentAt']); ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <form method="POST" action="customer_messages.php?id=<?php echo htmlspecialchars($customer_id); ?>">
        <textarea name="message" rows="4" required></textarea>
        <button type="submit" class="send-button">Send</button>
    </form>
</div>

<?php include 'templates/footer.php'; ?>