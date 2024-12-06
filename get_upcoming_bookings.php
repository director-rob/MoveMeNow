<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Manager') {
    http_response_code(403);
    exit;
}

if (isset($_GET['truck_id'])) {
    $query = '
        SELECT BookingID, Date, PickupAddress, DeliveryAddress 
        FROM Bookings 
        WHERE Truck = :truck_id AND Date >= CURDATE()
        ORDER BY Date ASC
    ';
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':truck_id', $_GET['truck_id'], PDO::PARAM_INT);
    $stmt->execute();
    
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($bookings);
}