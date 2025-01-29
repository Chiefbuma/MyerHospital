<?php
include('config.php');
require_once 'class.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $db = new db_class();  // Initialize your database class

        // Check if the procedure ID and procedure name are provided
        if (isset($_POST['procedure_id'], $_POST['procedure_name'])) {
            $procedure_id = $_POST['procedure_id'];
            $procedure_name = $_POST['procedure_name'];

            // Validate form data
            if (empty($procedure_id) || empty($procedure_name)) {
                throw new Exception("Procedure ID and Procedure Name are required.");
            }

            // Use prepared statements to prevent SQL injection
            $sql = "UPDATE procedures SET procedure_name = ? WHERE procedure_id = ?"; // Adjust table name if needed
            $stmt = $db->conn->prepare($sql);  // Prepare the query

            if ($stmt === false) {
                throw new Exception("Error preparing the SQL query: " . $db->conn->error);
            } else {
                $stmt->bind_param("si", $procedure_name, $procedure_id); // Bind parameters (string, integer)

                // Execute the query
                if ($stmt->execute()) {
                    // Redirect or display a success message
                    echo "<script>alert('Procedure updated successfully.');</script>";
                    echo "<script>window.location='addprocedure.php';</script>";  // Redirect to the procedures list page
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
