<?php
// Include database connection file
include('config.php');
require_once 'class.php';



try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Create a new instance of db_class for DB connection
        $db = new db_class();

        // Ensure the required fields are set from the form
        if (
            isset(
                $_POST['medication_use_id'],
                $_POST['days_supplied'],
                $_POST['no_pills_dispensed'],
                $_POST['frequency'],
                $_POST['visit_date']
            )
        ) {
            // Get values from POST data
            $medication_use_id = $_POST['medication_use_id'];
            $days_supplied = $_POST['days_supplied'];
            $no_pills_dispensed = $_POST['no_pills_dispensed'];
            $frequency = $_POST['frequency'];
            $visit_date = $_POST['visit_date'];



            // Prepare the SQL query to update medication use details
            $sql = "UPDATE medication_use 
                    SET days_supplied = ?, no_pills_dispensed = ?, frequency = ?, visit_date = ? 
                    WHERE medication_use_id = ?";

            // Prepare the statement to prevent SQL injection
            $stmt = $db->conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Error preparing the statement: " . $db->conn->error);
            }

            // Bind the parameters to the prepared statement
            $stmt->bind_param("iissi", $days_supplied, $no_pills_dispensed, $frequency, $visit_date, $medication_use_id);

            // Execute the update query
            if ($stmt->execute()) {
                // Success message and redirect
                echo "<script>alert('Medication use updated successfully.');</script>";
                echo "<script>window.location='assesmeds.php';</script>";  // Redirect to medication use list page
            } else {
                throw new Exception("Error updating medication use: " . $stmt->error);
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
