<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Customer') {
    http_response_code(403);
    exit;
}

if (isset($_GET['booking_id'])) {
    $query = '
        SELECT 
            BookingID,
            Date,
            PickedUp,
            TimePickedUp,
            Delivered,
            TimeDelivered,
            BookingCompleted,
            CreatedDate
        FROM Bookings
        WHERE BookingID = :booking_id
    ';
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':booking_id', $_GET['booking_id'], PDO::PARAM_INT);
    $stmt->execute();
    
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($booking);
}
?>