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
        
        // Check if a customer exists for the booking
        $query = 'SELECT CustomerID FROM Customers WHERE BookingID = :booking_id';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':booking_id', $_POST['booking_id'], PDO::PARAM_INT);
        $stmt->execute();
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if customer fields are provided
        $customerFieldsProvided = !empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['email']) && !empty($_POST['phone']);

        if (!$customer && $customerFieldsProvided) {
            // Create a new customer with the provided details and a default password
            $defaultPassword = password_hash('password', PASSWORD_DEFAULT);
            $query = 'INSERT INTO Customers (BookingID, FirstName, LastName, Email, Password, PhoneNumber) 
                      VALUES (:booking_id, :first_name, :last_name, :email, :password, :phone)';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':booking_id', $_POST['booking_id'], PDO::PARAM_INT);
            $stmt->bindParam(':first_name', $_POST['first_name'], PDO::PARAM_STR);
            $stmt->bindParam(':last_name', $_POST['last_name'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
            $stmt->bindParam(':password', $defaultPassword, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $_POST['phone'], PDO::PARAM_STR);
            $stmt->execute();
        } elseif ($customer) {
            // Update existing customer details
            $query = 'UPDATE Customers 
                      SET FirstName = :first_name, LastName = :last_name, Email = :email, PhoneNumber = :phone 
                      WHERE CustomerID = :customer_id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':first_name', $_POST['first_name'], PDO::PARAM_STR);
            $stmt->bindParam(':last_name', $_POST['last_name'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
            $stmt->bindParam(':phone', $_POST['phone'], PDO::PARAM_STR);
            $stmt->bindParam(':customer_id', $customer['CustomerID'], PDO::PARAM_INT);
            $stmt->execute();
        }

        // Update booking details
        $query = '
            UPDATE Bookings 
            SET 
                Date = :date,
                PickupAddress = :pickup,
                DeliveryAddress = :delivery,
                Instructions = :instructions,
                MoveSize = :move_size,
                MoveWeight = :move_weight,
                Truck = :truck,
                TotalCost = :total_cost,
                Paid = :paid
            WHERE BookingID = :booking_id
        ';
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':date', $_POST['date'], PDO::PARAM_STR);
        $stmt->bindParam(':pickup', $_POST['pickup_address'], PDO::PARAM_STR);
        $stmt->bindParam(':delivery', $_POST['dropoff_address'], PDO::PARAM_STR);
        $stmt->bindParam(':instructions', $_POST['instructions'], PDO::PARAM_STR);
        $stmt->bindParam(':move_size', $_POST['move_size'], PDO::PARAM_STR);
        $stmt->bindParam(':move_weight', $_POST['move_weight'], PDO::PARAM_INT);
        $stmt->bindParam(':truck', $_POST['assigned_truck'], PDO::PARAM_INT);
        $stmt->bindParam(':total_cost', $_POST['total_cost'], PDO::PARAM_STR);
        $stmt->bindParam(':paid', $_POST['paid'], PDO::PARAM_INT);
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
        header('Location: booking_details.php?id=' . $_POST['booking_id']);
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>