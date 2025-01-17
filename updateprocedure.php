<?php
include('config.php');
require_once 'class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $db = new db_class();  // Initialize your database class

    // Check if the procedure ID and procedure name are provided
    if (isset($_POST['procedure_id'], $_POST['procedure_name'])) {
        $procedure_id = $_POST['procedure_id'];
        $procedure_name = $_POST['procedure_name'];

        // Use prepared statements to prevent SQL injection
        $sql = "UPDATE procedures SET procedure_name = ? WHERE procedure_id = ?"; // Adjust table name if needed
        $stmt = $db->conn->prepare($sql);  // Prepare the query
        $stmt->bind_param("si", $procedure_name, $procedure_id); // Bind parameters (string, integer)

        // Execute the query
        if ($stmt->execute()) {
            // Redirect or display a success message
            echo "<script>alert('Procedure updated successfully.');</script>";
            echo "<script>window.location='addprocedure.php';</script>";  // Redirect to the procedures list page
        } else {
            // Display an error if the query failed
            echo "Error: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        echo "Error: Procedure ID and Procedure Name are required.";
    }

    // Close the database connection
    $db->conn->close();
}
