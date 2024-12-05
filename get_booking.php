<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Manager') {
    http_response_code(403);
    exit;
}

if (isset($_GET['id'])) {
    $query = '
        SELECT 
            B.*,
            GROUP_CONCAT(BM.MoverID) as AssignedMovers
        FROM Bookings B
        LEFT JOIN BookingMovers BM ON B.BookingID = BM.BookingID
        WHERE B.BookingID = :id
        GROUP BY B.BookingID
    ';
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();
    
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($booking);
}