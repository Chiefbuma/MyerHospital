<?php
// Include database connection file
include('config.php');
require_once 'class.php';

$db = new db_class();

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Ensure the required fields are set from the form
        if (isset($_POST['medication_id'])) {
            // Get values from POST data
            $medication_id = $_POST['medication_id'];
            $patient_id = $_POST['patient_id']; // Example patient_id, replace with dynamic value
            $days_supplied = ''; // Default blank value
            $no_pills_dispensed = ''; // Default blank value
            $frequency = ''; // Default blank value
            $visit_date = '';  // Current date

            // Validate form data
            if (empty($medication_id)) {
                throw new Exception("Medication ID is required.");
            }

            // Prepare the SQL query to insert medication use details
            $sql = "INSERT INTO medication_use (medication_id, patient_id, days_supplied, no_pills_dispensed, frequency, visit_date) 
                    VALUES (?, ?, ?, ?, ?, ?)";

            // Prepare the statement to prevent SQL injection
            $stmt = $db->conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Error preparing the statement: " . $db->conn->error);
            }

            // Bind the parameters to the prepared statement
            $stmt->bind_param("iissss", $medication_id, $patient_id, $days_supplied, $no_pills_dispensed, $frequency, $visit_date);

            // Execute the insert query
            $stmt->execute();

            // Close the prepared statement
            $stmt->close();
        } else {
            throw new Exception("Error: Missing required fields.");
        }

        // Close the database connection
        $db->conn->close();
    }
} catch (Exception $e) {
    // Handle exception silently
}
