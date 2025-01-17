<?php

include('config.php');
require_once 'class.php';  // Adjust the path if necessary

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Create a new instance of the db_class
    $db = new db_class();

    // Check if the required fields are present in the POST request
    if (isset($_POST['icd_10'], $_POST['diagnosis_name'])) {
        $icd_10 = $_POST['icd_10'];
        $diagnosis_name = $_POST['diagnosis_name'];

        // Prepare SQL to insert the diagnosis into the database
        $sql = "INSERT INTO diagnosis (ICD_10, diagnosis_name) VALUES (?, ?)";

        // Prepare the statement and bind parameters
        $stmt = $db->conn->prepare($sql);
        $stmt->bind_param("ss", $icd_10, $diagnosis_name);  // "ss" for two string parameters

        // Execute the query
        if ($stmt->execute()) {
            // Redirect or display a success message
            echo "<script>alert('New diagnosis successfully added.');</script>";
            echo "<script>window.location='adddiagnosis.php';</script>";  // Redirect to the diagnosis list page (adjust the filename as needed)
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
