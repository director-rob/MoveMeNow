<?php
require_once 'models/BookingModel.php';

class BookingController {
    private $bookingModel;

    public function __construct() {
        $this->bookingModel = new BookingModel();
    }

    // Display a specific booking
    public function view_booking($booking_id) {
        session_start();

        // Check if the user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }

        // Fetch booking details
        $booking = $this->bookingModel->get_booking_by_id($booking_id);

        // Pass booking data to the view
        require 'view/BookingView.php';
    }

    // Display all bookings
    public function list_bookings() {
        session_start();

        // Check if the user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }

        // Fetch all bookings
        $bookings = $this->bookingModel->get_all_bookings();

        // Pass bookings data to the view
        require 'view/BookingList.php';
    }


public function list_assigned_bookings($user_id) {
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header('Location: /');
        exit;
    }

    // Fetch assigned bookings
    $bookings = $this->bookingModel->get_assigned_bookings($user_id);

    // Pass bookings data to the view
    require 'view/BookingList.php';
}

}