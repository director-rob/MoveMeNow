<?php
require_once 'db.php';
session_start();

// Only allow logged-in users with proper role to update customers
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Manager') {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customer_id'])) {
    $customer_id = $_POST['customer_id'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $password = trim($_POST['password']);

    try {
        $db->beginTransaction();

        // Update customer details
        $query = 'UPDATE Customers SET FirstName = :first_name, LastName = :last_name, Email = :email, PhoneNumber = :phone_number';
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query .= ', Password = :password';
        }
        $query .= ' WHERE CustomerID = :customer_id';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
        $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':phone_number', $phone_number, PDO::PARAM_STR);
        if (!empty($password)) {
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        }
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $stmt->execute();

        $db->commit();
        header('Location: customers.php');
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>