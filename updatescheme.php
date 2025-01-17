<?php
include('config.php');
require_once 'class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $db = new db_class();  // Initialize your database class

    if (isset($_POST['scheme_id'], $_POST['scheme_name'], $_POST['payment_method'])) {
        $scheme_id = $_POST['scheme_id'];
        $scheme_name = $_POST['scheme_name'];
        $payment_method = $_POST['payment_method'];

        // Use prepared statements to prevent SQL injection
        $sql = "UPDATE scheme SET scheme_name = ?, payment_method = ? WHERE scheme_id = ?";
        $stmt = $db->conn->prepare($sql);  // Prepare the query
        $stmt->bind_param("ssi", $scheme_name, $payment_method, $scheme_id); // Bind parameters (string, string, integer)


        if ($stmt->execute()) {
            // Redirect or display a success message
            echo "<script>alert('Scheme updated succesfully.');</script>";
            echo "<script>window.location='addscheme.php';</script>";  // Redirect to the branches list page (adjust the filename as needed)
        } else {
            // Display an error if the query failed
            echo "Error: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        echo "Error preparing the SQL query.";
    }

    // Close the database connection
    $db->conn->close();
}
