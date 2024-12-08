<?php
require_once 'db.php';
session_start();

// Only allow logged-in users with proper role to delete customers
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Manager') {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customer_id'])) {
    $customer_id = $_POST['customer_id'];

    try {
        $db->beginTransaction();

        // Delete customer
        $query = 'DELETE FROM Customers WHERE CustomerID = :customer_id';
        $stmt = $db->prepare($query);
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