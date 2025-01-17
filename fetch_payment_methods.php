<?php
include 'config.php';
include 'class.php';  // Include your database connection

// Assuming $db is your database connection object
if (isset($_POST['scheme_id'])) {
    $scheme_id = $_POST['scheme_id'];

    // Fetch payment methods based on the selected scheme_id
    $payment_methods = $db->display_payment_methods($scheme_id);

    if ($payment_methods !== false) {
        echo json_encode(['success' => true, 'payment_methods' => $payment_methods]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error fetching payment methods']);
    }
}
