<?php
// Include database connection file
include('config.php');
require_once 'class.php';

try {
    if (isset($_POST['update'])) {
        // Create a new instance of db_class for DB connection
        $db = new db_class();

        // Ensure the required fields are set from the form
        if (
            isset(
                $_POST['medication_id'],
                $_POST['item_name'],
                $_POST['formulation'],
                $_POST['category'],
                $_POST['brand']
            )
        ) {
            // Get values from POST data
            $medication_id = $_POST['medication_id'];
            $item_name = $_POST['item_name'];
            $formulation = $_POST['formulation'];
            $category = $_POST['category'];
            $brand = $_POST['brand'];

            // Validate form data
            if (empty($item_name) || empty($formulation) || empty($category) || empty($brand)) {
                throw new Exception("All fields are required.");
            }

            // Prepare the SQL query to update medication details
            $sql = "UPDATE medication 
                    SET item_name = ?, formulation = ?, category = ?, brand = ? 
                    WHERE medication_id = ?";

            // Prepare the statement to prevent SQL injection
            $stmt = $db->conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Error preparing the statement: " . $db->conn->error);
            }

            // Bind the parameters to the prepared statement
            $stmt->bind_param("ssssi", $item_name, $formulation, $category, $brand, $medication_id);

            // Execute the update query
            if ($stmt->execute()) {
                // Success message and redirect
                echo "<script>alert('Medication updated successfully.');</script>";
                echo "<script>window.location='addmedication.php';</script>";  // Redirect to medication list page
            } else {
                throw new Exception("Error updating medication: " . $stmt->error);
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
