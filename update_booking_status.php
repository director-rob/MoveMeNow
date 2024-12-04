<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'] ?? null;
    
    if (isset($_POST['current_status'])) {
        // Handle completion status toggle
        $current_status = $_POST['current_status'];
        $new_status = $current_status == '1' ? '0' : '1';
        
        $query = 'UPDATE Bookings SET BookingCompleted = :status WHERE BookingID = :booking_id';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':status', $new_status, PDO::PARAM_STR);
        $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
    } 
    elseif (isset($_POST['action'])) {
        // Handle pickup/delivery status
        $action = $_POST['action'];
        
        if ($action === 'picked_up') {
            $query = 'UPDATE Bookings SET PickedUp = 1 WHERE BookingID = :booking_id';
        } elseif ($action === 'delivered') {
            $query = 'UPDATE Bookings SET Delivered = 1 WHERE BookingID = :booking_id';
        }
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
    }

    $stmt->execute();
    header('Location: dashboard.php');
    exit;
}
?>