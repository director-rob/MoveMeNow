<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Manager') {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];

    // Check if the booking is already archived
    $query = 'SELECT * FROM ArchivedBookings WHERE BookingID = :booking_id';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
    $stmt->execute();
    $archivedBooking = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($archivedBooking) {
        // Unarchive the booking
        $query = 'DELETE FROM ArchivedBookings WHERE BookingID = :booking_id';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        // Archive the booking
        $query = 'INSERT INTO ArchivedBookings (BookingID, DateArchived) VALUES (:booking_id, NOW())';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    header('Location: booking_details.php?id=' . $booking_id);
    exit;
}
?>