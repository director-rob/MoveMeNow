<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phoneNumber = trim($_POST['phone_number']);
    $comment = trim($_POST['comment']);
    $defaultPassword = password_hash('password', PASSWORD_DEFAULT);

    try {
        $db->beginTransaction();

        // Insert into Customers table
        $customerQuery = 'INSERT INTO Customers (FirstName, LastName, Email, Password, PhoneNumber) VALUES (:first_name, :last_name, :email, :password, :phone_number)';
        $customerStmt = $db->prepare($customerQuery);
        $customerStmt->bindParam(':first_name', $firstName, PDO::PARAM_STR);
        $customerStmt->bindParam(':last_name', $lastName, PDO::PARAM_STR);
        $customerStmt->bindParam(':email', $email, PDO::PARAM_STR);
        $customerStmt->bindParam(':password', $defaultPassword, PDO::PARAM_STR);
        $customerStmt->bindParam(':phone_number', $phoneNumber, PDO::PARAM_STR);
        $customerStmt->execute();

        // Get the last inserted customer ID
        $customerId = $db->lastInsertId();

        // Insert into MoveRequests table
        $moveRequestQuery = 'INSERT INTO MoveRequests (CustomerID, Message) VALUES (:customer_id, :message)';
        $moveRequestStmt = $db->prepare($moveRequestQuery);
        $moveRequestStmt->bindParam(':customer_id', $customerId, PDO::PARAM_INT);
        $moveRequestStmt->bindParam(':message', $comment, PDO::PARAM_STR);
        $moveRequestStmt->execute();

        $db->commit();
        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>

<?php include 'templates/header.php';?>
    <div class="container">
        <h2>Get in Touch</h2>
        <form method="POST" action="get_in_touch.php">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input type="text" id="phone_number" name="phone_number" required>
            </div>
            <div class="form-group">
                <label for="comment">Comment:</label>
                <textarea id="comment" name="comment" rows="4" required></textarea>
            </div>
            <button type="submit" class="submit-button">Submit</button>
        </form>
    </div>
    <?php include 'templates/footer.php';?>