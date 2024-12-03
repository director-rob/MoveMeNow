<?php
require_once 'db.php';
session_start();

// Only allow logged-in users with proper role to delete bookings
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Manager') {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];

    $query = 'DELETE FROM Bookings WHERE BookingID = :booking_id';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: dashboard.php');
    exit;
}
?>
