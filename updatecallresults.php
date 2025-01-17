<?php
include('config.php');
require_once 'class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $db = new db_class();

    // Check if Call_results_id and Call_result are set
    if (isset($_POST['Call_results_id'], $_POST['Call_result'])) {
        // Get values from the POST request
        $Call_results_id = $_POST['Call_results_id'];
        $Call_result = $_POST['Call_result'];

        // Use prepared statements to prevent SQL injection
        $sql = "UPDATE call_results SET Call_result = ? WHERE Call_results_id = ?";

        // Prepare the statement
        if ($stmt = $db->conn->prepare($sql)) {
            // Bind parameters: "si" means string (for Call_result) and integer (for Call_results_id)
            $stmt->bind_param("si", $Call_result, $Call_results_id);

            // Execute the statement and check for success
            if ($stmt->execute()) {
                // Display success message and redirect
                echo "<script>alert('Call result updated successfully.');</script>";
                echo "<script>window.location='addcallresults.php';</script>"; // Redirect to the call results page (adjust the filename as needed)
            } else {
                // Display an error if the query failed
                echo "Error: " . $stmt->error;
            }

            // Close the prepared statement
            $stmt->close();
        } else {
            echo "Error preparing the SQL query.";
        }
    } else {
        echo "Error: Missing required fields.";
    }

    // Close the database connection
    $db->conn->close();
}
