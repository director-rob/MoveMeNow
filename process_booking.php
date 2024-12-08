<?php
require_once 'db.php';
session_start();

// Ensure the user is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Customer') {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_SESSION['user_id'];
    $date = $_POST['date'];
    $pickup_address = $_POST['pickup_street'] . ', ' . $_POST['pickup_city'] . ', ' . $_POST['pickup_state'] . ', ' . $_POST['pickup_postal_code'];
    $delivery_address = $_POST['delivery_street'] . ', ' . $_POST['delivery_city'] . ', ' . $_POST['delivery_state'] . ', ' . $_POST['delivery_postal_code'];
    $move_size = $_POST['move_size'];
    $move_weight = $_POST['move_weight'] === 'custom' ? $_POST['custom_move_weight'] : $_POST['move_weight'];
    $instructions = $_POST['instructions'];
    $credit_card_number = $_POST['credit_card_number'];
    $created_date = date('Y-m-d H:i:s'); // Set the current date and time

    // Calculate base cost
    $base_costs = [
        '1-Bed Apartment' => 200,
        '2-Bed Apartment' => 400,
        '3-Bed House' => 600,
        '4-Bed House' => 800
    ];
    $base_cost = $base_costs[$move_size];
    $weight_cost = $move_weight * 0.50;
    $total_cost = $base_cost + $weight_cost;

    // Determine truck size
    $truck_size = $move_weight <= 2000 ? 20 : 26; // 20-foot or 26-foot truck

    // Determine number of movers
    if ($move_weight <= 2000) {
        $num_movers = 2;
    } elseif ($move_weight <= 3000) {
        $num_movers = 3;
    } else {
        $num_movers = 4;
    }

    // Check for surcharge
    $move_sizes = [
        '1-Bed Apartment' => 1000,
        '2-Bed Apartment' => 2000,
        '3-Bed House' => 3000,
        '4-Bed House' => 5000
    ];
    $max_weight = $move_sizes[$move_size];
    $surcharge = 0;
    if ($move_weight > $max_weight) {
        $surcharge = 0.10 * $total_cost;
        $total_cost += $surcharge;
    }

    // Calculate mover cost
    $mover_cost = $num_movers * 50 * 4; // 4 hours
    $total_cost += $mover_cost;

    try {
        $db->beginTransaction();

        // Insert booking
        $query = 'INSERT INTO Bookings (CustomerID, Date, PickupAddress, DeliveryAddress, MoveSize, MoveWeight, TotalCost, Truck, CreatedDate) 
                 VALUES (:customer_id, :date, :pickup_address, :delivery_address, :move_size, :move_weight, :total_cost, :truck, :created_date)';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':pickup_address', $pickup_address, PDO::PARAM_STR);
        $stmt->bindParam(':delivery_address', $delivery_address, PDO::PARAM_STR);
        $stmt->bindParam(':move_size', $move_size, PDO::PARAM_STR);
        $stmt->bindParam(':move_weight', $move_weight, PDO::PARAM_INT);
        $stmt->bindParam(':total_cost', $total_cost, PDO::PARAM_STR);
        $stmt->bindParam(':truck', $truck_size, PDO::PARAM_INT);
        $stmt->bindParam(':created_date', $created_date, PDO::PARAM_STR);
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert booking.");
        }

        $booking_id = $db->lastInsertId();

        // Update customer's BookingID
        $updateCustomerQuery = "
            UPDATE Customers
            SET BookingID = :booking_id
            WHERE CustomerID = :customer_id
        ";
        $updateCustomerStmt = $db->prepare($updateCustomerQuery);
        $updateCustomerStmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
        $updateCustomerStmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        if (!$updateCustomerStmt->execute()) {
            throw new Exception("Failed to update customer's BookingID.");
        }

        // Assign movers
        $moversQuery = "
            SELECT MoverID
            FROM Movers
            WHERE MoverID NOT IN (
                SELECT MoverID
                FROM BookingMovers BM
                JOIN Bookings B ON BM.BookingID = B.BookingID
                WHERE B.Date = :date
            )
            LIMIT :num_movers
        ";
        $moversStmt = $db->prepare($moversQuery);
        $moversStmt->bindParam(':date', $date, PDO::PARAM_STR);
        $moversStmt->bindParam(':num_movers', $num_movers, PDO::PARAM_INT);
        $moversStmt->execute();
        $available_movers = $moversStmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($available_movers as $mover_id) {
            $assignMoverQuery = "
                INSERT INTO BookingMovers (BookingID, MoverID)
                VALUES (:booking_id, :mover_id)
            ";
            $assignMoverStmt = $db->prepare($assignMoverQuery);
            $assignMoverStmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
            $assignMoverStmt->bindParam(':mover_id', $mover_id, PDO::PARAM_INT);
            if (!$assignMoverStmt->execute()) {
                throw new Exception("Failed to assign mover to booking.");
            }
        }

        $db->commit();
        echo "<script>alert('Booking successfully created!'); window.location.href='dashboard.php';</script>";
    } catch (Exception $e) {
        $db->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>