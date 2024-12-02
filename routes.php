<?php
require 'controllers/UserController.php';
require 'controllers/CustomerController.php';
require 'controllers/EmployeeController.php';

$path = $_GET['route'] ?? '/';

if ($path === 'login') {
    $controller = new UserController();
    $controller->login();
} elseif ($path === 'logout') {
    $controller = new UserController();
    $controller->logout();
} elseif ($path === 'customer/dashboard') {
    $controller = new CustomerController();
    $controller->dashboard();
} elseif ($path === 'employee/dashboard') {
    $controller = new EmployeeController();
    $controller->dashboard();
} else {
    echo "404 Not Found";
}
