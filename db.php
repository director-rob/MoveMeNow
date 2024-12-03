<?php
$dsn = 'mysql:host=localhost;dbname=MoveMeNow';
$username = 'root';
$password = ''; // Add your MySQL password here, or leave it as an empty string if no password is set

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $error = "Database Error: " . $e->getMessage();
    include('templates/error.php');
    exit();
}
?>
