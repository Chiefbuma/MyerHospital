<?php

include('config.php');
require_once 'class.php';  // Adjust the path if necessary

try {
    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Create a new instance of the db_class
        $db = new db_class();

        if (isset($_POST['scheme_name'], $_POST['payment_method'])) {
            $scheme_name = $_POST['scheme_name'];
            $payment_method = $_POST['payment_method'];

            // Validate form data
            if (empty($scheme_name) || empty($payment_method)) {
                throw new Exception("Scheme name and payment method cannot be empty.");
            }

            // Prepare SQL to insert data into the database
            $sql = "INSERT INTO scheme (scheme_name, payment_method) VALUES (?, ?)";

            // Prepare the statement and bind parameters
            if ($stmt = $db->conn->prepare($sql)) {
                $stmt->bind_param("ss", $scheme_name, $payment_method);

                // Execute the query
                if ($stmt->execute()) {
                    // Redirect or display a success message
                    echo "<script>alert('New scheme added successfully.');</script>";
                    echo "<script>window.location='addscheme.php';</script>";  // Redirect to the branches list page (adjust the filename as needed)
                } else {
                    throw new Exception("Error executing query: " . $stmt->error);
                }

                // Close the prepared statement
                $stmt->close();
            } else {
                throw new Exception("Error preparing the SQL query.");
            }

            // Close the database connection
            $db->conn->close();
        } else {
            throw new Exception("Error: Missing required fields.");
        }
    }
} catch (Exception $e) {
    echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
}
