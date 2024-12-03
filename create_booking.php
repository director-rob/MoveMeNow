<?php
require_once 'db.php';
session_start();

// Only allow logged-in users with proper role to add bookings
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Manager') {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $pickup = $_POST['pickup'];
    $delivery = $_POST['delivery'];
    $truck = $_POST['truck'];
    $completed = $_POST['completed'];

    $query = 'INSERT INTO Bookings (Date, PickupAddress, DeliveryAddress, Truck, BookingCompleted) VALUES (:date, :pickup, :delivery, :truck, :completed)';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    $stmt->bindParam(':pickup', $pickup, PDO::PARAM_STR);
    $stmt->bindParam(':delivery', $delivery, PDO::PARAM_STR);
    $stmt->bindParam(':truck', $truck, PDO::PARAM_INT);
    $stmt->bindParam(':completed', $completed, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: dashboard.php');
    exit;
}
?>
