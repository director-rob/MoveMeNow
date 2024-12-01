<?php 
    function get_booking_by_id($booking_id) {
        global $db;
    
        if ($booking_id) {
            // Query to fetch booking details and movers associated with a specific BookingID
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
                WHERE B.BookingID = :booking_id;
            ';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
        } else {
            // Query to fetch all bookings without filtering by BookingID
            $query = '
                SELECT 
                    B.BookingID,
                    B.Date,
                    B.PickupAddress,
                    B.Truck,
                    B.DeliveryAddress
                FROM Bookings B;
            ';
            $stmt = $db->prepare($query);
        }
    
        // Execute the query
        $stmt->execute();
    
        // Fetch and return the results
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    