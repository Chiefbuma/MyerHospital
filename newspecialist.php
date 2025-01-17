<?php

include('config.php');
require_once 'class.php';  // Adjust the path if necessary

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Create a new instance of the db_class
    $db = new db_class();

    if (isset($_POST['specialist_name'], $_POST['specialty'])) {
        $specialist_name = $_POST['specialist_name'];
        $specialty = $_POST['specialty'];

        // Prepare SQL to insert data into the database
        $sql = "INSERT INTO specialist (specialist_name, specialty) VALUES (?, ?)";

        // Prepare the statement and bind parameters
        $stmt = $db->conn->prepare($sql);
        $stmt->bind_param("ss", $specialist_name, $specialty);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect or display a success message
            echo "<script>alert('New specialist added successfully.');</script>";
            echo "<script>window.location='addspecialist.php';</script>";  // Redirect to the specialist list page
        } else {
            // Display an error if the query failed
            echo "Error: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        echo "Error: Missing required fields.";
    }

    // Close the database connection
    $db->conn->close();
}
