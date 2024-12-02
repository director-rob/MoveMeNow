<?php
class EmployeeModel {
    private $db;

    public function __construct() {
        require 'db.php';
        $this->db = $db;
    }

    // Fetch employee-specific data
    public function getEmployeeData($userID) {
        $query = 'SELECT FirstName, LastName, Role, DateJoined FROM Employees WHERE EmployeeID = :userID';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
