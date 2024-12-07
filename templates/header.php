<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'MoveMeNow'; ?></title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<?php  
//Set current time zone
date_default_timezone_set('America/Vancouver');
// DEBUG: Output current session state
echo 'Current Session State at index.php (before login logic):<br>';
echo 'Session ID: ' . session_id() . '<br>';
echo 'User ID: ' . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Not Set') . '<br>';
echo 'Role: ' . (isset($_SESSION['role']) ? $_SESSION['role'] : 'Not Set') . '<br>';
echo 'Date: ' . date("Y-m-d") . '<br>';
echo 'Time Zone: ' . date_default_timezone_get() . '<br>';

?>