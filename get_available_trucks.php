<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Manager') {
    http_response_code(403);
    exit;
}

if (isset($_GET['date'])) {
    $date = $_GET['date'];

    // Fetch trucks that are not booked on the selected date
    $query = '
        SELECT T.TruckID, T.LicensePlate, T.Make, T.Model, T.SizeInFeet
        FROM Trucks T
        LEFT JOIN Bookings B ON T.TruckID = B.Truck AND B.Date = :date
        WHERE B.Truck IS NULL
    ';
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    $stmt->execute();
    
    $trucks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($trucks);
}