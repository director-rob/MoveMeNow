<?php
    // Database connection settings
    $host = 'localhost';
    $dbname = 'MoveMeNow';
    $username = 'root';
    $password = 'password'; 

    try {
        // Create a new PDO instance with charset for multilingual support
        $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        
        // Set PDO error mode to exception for better error handling
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Set the default fetch mode to associative array
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        // Catch database connection errors and display a friendly error message
        $error = "Database Error: " . $e->getMessage();
        include('view/error.php'); // Ensure error.php exists in the 'view' folder
        exit();
    }
?>
