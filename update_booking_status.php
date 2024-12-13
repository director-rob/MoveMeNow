<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'] ?? null;
    $redirect_url = $_POST['redirect_url'] ?? 'bookings.php';
    
    // Validate the redirect URL
    $allowed_urls = ['bookings.php', 'dashboard.php', 'booking_details.php']; // Add allowed URLs here
    $parsed_url = parse_url($redirect_url);
    $redirect_path = $parsed_url['path'] ?? '';

    if (!in_array($redirect_path, $allowed_urls)) {
        $redirect_url = 'bookings.php'; // Default to a safe URL
    }

    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'complete') {
            // Mark booking as completed and override all other statuses
            $query = 'UPDATE Bookings SET BookingCompleted = 1, PickedUp = 0, Delivered = 0, Paid = 0 WHERE BookingID = :booking_id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
            $stmt->execute();
        } elseif ($action === 'picked_up') {
            // Mark booking as picked up
            $query = 'UPDATE Bookings SET PickedUp = 1 WHERE BookingID = :booking_id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
            $stmt->execute();
        } elseif ($action === 'delivered') {
            // Mark booking as delivered
            $query = 'UPDATE Bookings SET Delivered = 1 WHERE BookingID = :booking_id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
            $stmt->execute();
        } elseif ($action === 'in_progress' || $action === 'pending') {
            // Mark booking as in progress
            $query = 'UPDATE Bookings SET BookingCompleted = 0 WHERE BookingID = :booking_id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
            $stmt->execute();
        } elseif ($action === 'incomplete') {
            // Mark booking as incomplete
            $query = 'UPDATE Bookings SET BookingCompleted = 0 WHERE BookingID = :booking_id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }
    
    header("Location: $redirect_url");
    exit;
}
?>