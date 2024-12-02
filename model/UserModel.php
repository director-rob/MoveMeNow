<?php
class UserModel {
    private $db;

    public function __construct() {
        require 'db.php'; // Database connection
        $this->db = $db;
    }

    // Authenticate user by username and password
    public function authenticate($username, $password) {
        $query = 'SELECT UserID, Role, Password FROM Users WHERE Username = :username';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password
        if ($user && password_verify($password, $user['Password'])) {
            return $user; // Return user data if authenticated
        }
        return false; // Authentication failed
    }
}
