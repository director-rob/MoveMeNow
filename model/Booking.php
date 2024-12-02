<?php
class BookingModel {
    private $db;

    public function __construct() {
        require 'db.php'; // Database connection
        $this->db = $db;
    }

    // Fetch a single booking by ID
    public function get_booking_by_id($booking_id) {
        $query = '
            SELECT 
                B.BookingID,
                B.Date,
                B.PickupAddress,
                B.Truck,
                B.DeliveryAddress,
                M.MoverID,
                M.Name AS MoverName
            FROM Bookings B
            JOIN BookingMovers BM ON B.BookingID = BM.BookingID
            JOIN Movers M ON BM.MoverID = M.MoverID
            WHERE B.BookingID = :booking_id
            ORDER BY B.Date DESC;
        ';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch all bookings
    public function get_all_bookings() {
        $query = '
            SELECT 
                B.BookingID,
                B.Date,
                B.PickupAddress,
                B.Truck,
                B.DeliveryAddress
            FROM Bookings B
            ORDER BY B.Date DESC;
        ';
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
