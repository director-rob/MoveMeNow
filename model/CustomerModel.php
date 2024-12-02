<?php
class CustomerModel {
    private $db;

    public function __construct() {
        require 'db.php';
        $this->db = $db;
    }

    // Fetch customer-specific data
    public function getCustomerData($userID) {
        $query = 'SELECT FirstName, LastName, DateJoined FROM Customers WHERE CustomerID = :userID';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
