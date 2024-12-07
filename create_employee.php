<?php
require_once 'db.php';
session_start();

// Only managers can create employees
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Manager') {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']); 
    $role = $_POST['role'];
    $dateJoined = $_POST['dateJoined'];
    $contactInfo = $_POST['contactInfo'] ?? null;
    $otherDetails = $_POST['otherDetails'] ?? null;
    
    // Generate username by concatenating first and last name
    $username = strtolower($firstName . $lastName);
    // Remove special characters and spaces from username
    $username = preg_replace('/[^a-z0-9]/', '', $username);
    
    // Set default password and hash it
    $password = password_hash('password', PASSWORD_DEFAULT);

    try {
        $db->beginTransaction();

        // Insert into Employees table
        $query = 'INSERT INTO Employees (FirstName, LastName, Username, Password, Role, DateJoined) 
                 VALUES (:firstName, :lastName, :username, :password, :role, :dateJoined)';
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':firstName', $firstName, PDO::PARAM_STR);
        $stmt->bindParam(':lastName', $lastName, PDO::PARAM_STR);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        $stmt->bindParam(':dateJoined', $dateJoined, PDO::PARAM_STR);
        $stmt->execute();
        
        $employeeId = $db->lastInsertId();

        // If role is Mover, also insert into Movers table
        if ($role === 'Mover') {
            $fullName = $firstName . ' ' . $lastName;
            $query = 'INSERT INTO Movers (MoverID, Name, ContactInfo, OtherDetails) VALUES (:moverId, :name, :contactInfo, :otherDetails)';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':moverId', $employeeId, PDO::PARAM_INT);
            $stmt->bindParam(':name', $fullName, PDO::PARAM_STR);
            $stmt->bindParam(':contactInfo', $contactInfo, PDO::PARAM_STR);
            $stmt->bindParam(':otherDetails', $otherDetails, PDO::PARAM_STR);
            $stmt->execute();
        }

        $db->commit();
        header('Location: dashboard.php');
        exit;
    } catch (PDOException $e) {
        $db->rollBack();
        if ($e->getCode() == 23000) {
            echo "Username already exists. Please try a different name combination.";
        } else {
            echo "Error creating employee: " . $e->getMessage();
        }
    }
}
?>