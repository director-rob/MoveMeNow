<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Manager') {
    http_response_code(403);
    exit;
}

if (isset($_GET['truck_id'])) {
    $query = '
        SELECT TruckID, LicensePlate, Make, Model, SizeInFeet
        FROM Trucks
        WHERE TruckID = :truck_id
    ';
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':truck_id', $_GET['truck_id'], PDO::PARAM_INT);
    $stmt->execute();
    
    $truck = $stmt->fetch(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($truck);
}