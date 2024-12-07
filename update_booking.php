<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Manager') {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db->beginTransaction();
        
        // Update booking details
        $query = '
            UPDATE Bookings 
            SET Date = :date,
                PickupAddress = :pickup,
                DeliveryAddress = :delivery,
                Truck = :truck
            WHERE BookingID = :booking_id
        ';
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':date', $_POST['date'], PDO::PARAM_STR);
        $stmt->bindParam(':pickup', $_POST['pickup'], PDO::PARAM_STR);
        $stmt->bindParam(':delivery', $_POST['delivery'], PDO::PARAM_STR);
        $stmt->bindParam(':truck', $_POST['truck'], PDO::PARAM_INT);
        $stmt->bindParam(':booking_id', $_POST['booking_id'], PDO::PARAM_INT);
        $stmt->execute();
        
        // Update mover assignments
        $query = 'DELETE FROM BookingMovers WHERE BookingID = :booking_id';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':booking_id', $_POST['booking_id'], PDO::PARAM_INT);
        $stmt->execute();
        
        if (isset($_POST['assigned_movers']) && is_array($_POST['assigned_movers'])) {
            $query = 'INSERT INTO BookingMovers (BookingID, MoverID) VALUES (:booking_id, :mover_id)';
            $stmt = $db->prepare($query);
            
            foreach ($_POST['assigned_movers'] as $moverId) {
                $stmt->bindParam(':booking_id', $_POST['booking_id'], PDO::PARAM_INT);
                $stmt->bindParam(':mover_id', $moverId, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
        
        $db->commit();
        header('Location: dashboard.php');
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>