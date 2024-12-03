<?php
require_once 'db.php';
session_start();

// Ensure the user is a mover
if ($_SESSION['role'] !== 'Mover') {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['action'])) {
    $booking_id = $_POST['booking_id'];
    $action = $_POST['action'];

    if ($action === 'picked_up') {
        $query = 'UPDATE Bookings SET PickedUp = 1 WHERE BookingID = :booking_id';
    } elseif ($action === 'delivered') {
        $query = 'UPDATE Bookings SET Delivered = 1 WHERE BookingID = :booking_id';
    }

    $stmt = $db->prepare($query);
    $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: dashboard.php');
    exit;
}
?>
