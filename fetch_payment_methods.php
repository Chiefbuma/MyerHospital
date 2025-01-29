<?php
include 'config.php';
include 'class.php';  // Include your database connection

try {
    if (isset($_POST['scheme_id']) && is_numeric($_POST['scheme_id'])) {
        $scheme_id = intval($_POST['scheme_id']);  // Ensure the scheme_id is an integer

        // Fetch payment methods based on the selected scheme_id
        $payment_methods = $db->display_payment_methods($scheme_id);

        if ($payment_methods !== false) {
            echo json_encode(['success' => true, 'payment_methods' => $payment_methods]);
        } else {
            throw new Exception('Error fetching payment methods');
        }
    } else {
        throw new Exception('Invalid or missing scheme ID');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
}
