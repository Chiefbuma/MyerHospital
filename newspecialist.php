<?php

include('config.php');
require_once 'class.php';  // Adjust the path if necessary

try {
    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Create a new instance of the db_class
        $db = new db_class();

        if (isset($_POST['specialist_name'], $_POST['specialty'])) {
            $specialist_name = $_POST['specialist_name'];
            $specialty = $_POST['specialty'];

            // Validate form data
            if (empty($specialist_name) || empty($specialty)) {
                throw new Exception("Specialist name and specialty cannot be empty.");
            }

            // Prepare SQL to insert data into the database
            $sql = "INSERT INTO specialist (specialist_name, specialty) VALUES (?, ?)";

            // Prepare the statement and bind parameters
            if ($stmt = $db->conn->prepare($sql)) {
                $stmt->bind_param("ss", $specialist_name, $specialty);

                // Execute the query
                if ($stmt->execute()) {
                    // Redirect or display a success message
                    echo "<script>alert('New specialist added successfully.');</script>";
                    echo "<script>window.location='addspecialist.php';</script>";  // Redirect to the specialist list page
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
