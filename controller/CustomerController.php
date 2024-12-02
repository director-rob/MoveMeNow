<?php
require_once 'models/CustomerModel.php';

class CustomerController {
    private $customerModel;

    public function __construct() {
        $this->customerModel = new CustomerModel();
    }

    // Customer dashboard
    public function dashboard() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
            header('Location: /'); // Redirect to login
            exit;
        }

        // Fetch customer data
        $customerData = $this->customerModel->getCustomerData($_SESSION['user_id']);
        require 'views/customer_dashboard.php'; // Render view
    }
}
