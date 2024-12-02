<?php
require_once 'models/EmployeeModel.php';

class EmployeeController {
    private $employeeModel;
    public function __construct() {
        $this->employeeModel = new EmployeeModel();
    }

    // Employee dashboard
    public function dashboard() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
            header('Location: /'); // Redirect to login
            exit;
        }

        // Fetch employee data
        $employeeData = $this->employeeModel->getEmployeeData($_SESSION['user_id']);
        require 'views/employee_dashboard.php'; // Render view
    }
}