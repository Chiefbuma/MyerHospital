<?php
include('config.php');
require_once 'class.php';  // Adjust the path if necessary

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $db = new db_class();  // Initialize your database class

    if (isset($_POST['diagnosis_id'], $_POST['icd_10'], $_POST['diagnosis_name'])) {
        $diagnosis_id = $_POST['diagnosis_id'];
        $icd_10 = $_POST['icd_10'];
        $diagnosis_name = $_POST['diagnosis_name'];

        // Use prepared statements to prevent SQL injection
        $sql = "UPDATE diagnosis SET ICD_10 = ?, diagnosis_name = ? WHERE diagnosis_id = ?";
        $stmt = $db->conn->prepare($sql);  // Prepare the query

        if ($stmt === false) {
            echo "Error preparing the SQL query: " . $db->conn->error;
        } else {
            $stmt->bind_param("ssi", $icd_10, $diagnosis_name, $diagnosis_id); // Bind parameters (string, string, integer)

            if ($stmt->execute()) {
                // Redirect or display a success message
                echo "<script>alert('Diagnosis updated successfully.');</script>";
                echo "<script>window.location='adddiagnosis.php';</script>";  // Redirect to the diagnosis list page (adjust the filename as needed)
            } else {
                // Display an error if the query failed
                echo "Error executing query: " . $stmt->error;
            }

            // Close the prepared statement
            $stmt->close();
        }
    } else {
        echo "Error: Missing form data.";
    }

    // Close the database connection
    $db->conn->close();
}
