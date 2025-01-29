<?php
include('config.php');
require_once 'class.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $db = new db_class();  // Initialize your database class

        if (isset($_POST['scheme_id'], $_POST['scheme_name'], $_POST['payment_method'])) {
            $scheme_id = $_POST['scheme_id'];
            $scheme_name = $_POST['scheme_name'];
            $payment_method = $_POST['payment_method'];

            // Validate form data
            if (empty($scheme_id) || empty($scheme_name) || empty($payment_method)) {
                throw new Exception("Scheme ID, Scheme Name, and Payment Method are required.");
            }

            // Use prepared statements to prevent SQL injection
            $sql = "UPDATE scheme SET scheme_name = ?, payment_method = ? WHERE scheme_id = ?";
            $stmt = $db->conn->prepare($sql);  // Prepare the query

            if ($stmt === false) {
                throw new Exception("Error preparing the SQL query: " . $db->conn->error);
            } else {
                $stmt->bind_param("ssi", $scheme_name, $payment_method, $scheme_id); // Bind parameters (string, string, integer)

                // Execute the query
                if ($stmt->execute()) {
                    // Redirect or display a success message
                    echo "<script>alert('Scheme updated successfully.');</script>";
                    echo "<script>window.location='addscheme.php';</script>";  // Redirect to the branches list page (adjust the filename as needed)
                } else {
                    throw new Exception("Error executing query: " . $stmt->error);
                }

                // Close the prepared statement
                $stmt->close();
            }
        } else {
            throw new Exception("Error: Missing required fields.");
        }

        // Close the database connection
        $db->conn->close();
    }
} catch (Exception $e) {
    echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
}
