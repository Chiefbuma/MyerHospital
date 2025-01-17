<?php
// Include database connection file
include('config.php');
require_once 'class.php';

if (isset($_POST['update'])) {

    // Create a new instance of db_class for DB connection
    $db = new db_class();

    // Ensure the required fields are set from the form
    if (
        isset(
            $_POST['medication_id'],
            $_POST['medication_name'],
            $_POST['type_of_brand'],
            $_POST['formulation']
        )
    ) {
        // Get values from POST data
        $medication_id = $_POST['medication_id'];
        $medication_name = $_POST['medication_name'];
        $type_of_brand = $_POST['type_of_brand'];
        $formulation = $_POST['formulation'];

        // Prepare the SQL query to update medication details
        $sql = "UPDATE medication 
                SET medication_name = ?, type_of_brand = ?, formulation = ? 
                WHERE medication_id = ?";

        // Prepare the statement to prevent SQL injection
        $stmt = $db->conn->prepare($sql);
        if ($stmt === false) {
            echo "Error preparing the statement: " . $db->conn->error;
            exit;
        }

        // Bind the parameters to the prepared statement
        $stmt->bind_param("sssi", $medication_name, $type_of_brand, $formulation, $medication_id);

        // Execute the update query
        if ($stmt->execute()) {
            // Success message and redirect
            echo "<script>alert('Medication updated successfully.');</script>";
            echo "<script>window.location='addmedication.php';</script>";  // Redirect to medication list page
        } else {
            // Error message
            echo "Error updating medication: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        // Handle missing fields
        echo "Error: Missing required fields.";
    }

    // Close the database connection
    $db->conn->close();
}
