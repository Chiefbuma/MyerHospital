<?php

include('config.php');
require_once 'class.php';  // Adjust the path if necessary

try {
    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Create a new instance of the db_class
        $db = new db_class();

        // Check if procedure name is provided in the form
        if (isset($_POST['procedure_name'])) {
            $procedure_name = $_POST['procedure_name'];

            // Validate form data
            if (empty($procedure_name)) {
                throw new Exception("Procedure name cannot be empty.");
            }

            // Prepare SQL to insert data into the database
            $sql = "INSERT INTO procedures (procedure_name) VALUES (?)"; // Adjust table name if needed

            // Prepare the statement and bind parameters
            if ($stmt = $db->conn->prepare($sql)) {
                $stmt->bind_param("s", $procedure_name); // "s" denotes a string parameter

                // Execute the query
                if ($stmt->execute()) {
                    // Redirect or display a success message
                    echo "<script>alert('New procedure added successfully.');</script>";
                    echo "<script>window.location='addprocedure.php';</script>";  // Redirect to the procedures list page
                } else {
                    throw new Exception("Error executing query: " . $stmt->error);
                }

                // Close the prepared statement
                $stmt->close();
            } else {
                throw new Exception("Error preparing the SQL query.");
            }
        } else {
            throw new Exception("Error: Procedure name is required.");
        }

        // Close the database connection
        $db->conn->close();
    }
} catch (Exception $e) {
    echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
}
