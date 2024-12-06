<?php
require_once 'db.php';
session_start();

// Only managers can remove employees
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Manager') {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['employee_id'])) {
    $employeeId = $_POST['employee_id'];

    // Check if the employee being removed is the current user
    if ($employeeId == $_SESSION['user_id']) {
        echo "Error: You cannot remove yourself.";
        exit;
    }

    try {
        $db->beginTransaction();

        // Remove employee from Movers table if they are a mover
        $query = 'DELETE FROM Movers WHERE MoverID = :employee_id';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_INT);
        $stmt->execute();

        // Remove employee from Employees table
        $query = 'DELETE FROM Employees WHERE EmployeeID = :employee_id';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_INT);
        $stmt->execute();

        $db->commit();
        header('Location: dashboard.php');
        exit;
    } catch (PDOException $e) {
        $db->rollBack();
        echo "Error removing employee: " . $e->getMessage();
    }
}
?>