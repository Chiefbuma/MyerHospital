<?php

include('config.php');
require_once 'class.php';  // Adjust the path if necessary

try {
    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Create a new instance of the db_class
        $db = new db_class();

        // Check if the required fields are present in the POST request
        if (isset($_POST['icd_10'], $_POST['diagnosis_name'])) {
            $icd_10 = $_POST['icd_10'];
            $diagnosis_name = $_POST['diagnosis_name'];

            // Validate form data
            if (empty($icd_10) || empty($diagnosis_name)) {
                throw new Exception("ICD-10 and Diagnosis Name are required.");
            }

            // Prepare SQL to insert the diagnosis into the database
            $sql = "INSERT INTO diagnosis (ICD_10, diagnosis_name) VALUES (?, ?)";

            // Prepare the statement and bind parameters
            $stmt = $db->conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Error preparing the SQL query: " . $db->conn->error);
            }

            $stmt->bind_param("ss", $icd_10, $diagnosis_name);  // "ss" for two string parameters

            // Execute the query
            if ($stmt->execute()) {
                // Redirect or display a success message
                echo "<script>alert('New diagnosis successfully added.');</script>";
                echo "<script>window.location='adddiagnosis.php';</script>";  // Redirect to the diagnosis list page (adjust the filename as needed)
            } else {
                throw new Exception("Error executing query: " . $stmt->error);
            }

            // Close the prepared statement
            $stmt->close();
        } else {
            throw new Exception("Error: Missing required fields.");
        }

        // Close the database connection
        $db->conn->close();
    }
} catch (Exception $e) {
    echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
}
