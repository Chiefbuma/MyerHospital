<?php
include('config.php');
require_once 'class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $db = new db_class();

    // Check if call_id, call_results, and call_date are set
    if (isset($_POST['call_id'], $_POST['call_results'], $_POST['call_date'])) {
        // Get values from the POST request
        $callId = $_POST['call_id'];
        $callResults = $_POST['call_results'];
        $callDate = $_POST['call_date'];

        // Format the date to YYYY-MM-DD
        $date = new DateTime($callDate);
        $formattedDate = $date->format('Y-m-d'); // Format to YYYY-MM-DD for database consistency

        // Use prepared statements to prevent SQL injection
        $sql = "UPDATE calls SET call_results = ?, call_date = ? WHERE call_id = ?";

        // Prepare the statement
        $stmt = $db->conn->prepare($sql);

        // Bind parameters: "si" means string (for call_results and call_date), integer (for call_id)
        $stmt->bind_param("ssi", $callResults, $formattedDate, $callId);

        // Execute the statement and check for success
        if ($stmt->execute()) {
            // Display success message and redirect
            echo "<script>alert('Call updated successfully.');</script>";
            echo "<script>window.location='addcalls.php';</script>"; // Redirect to the calls list page (adjust the filename as needed)
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
