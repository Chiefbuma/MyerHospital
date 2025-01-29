<?php
include('config.php');
require_once 'class.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $db = new db_class();

        // Check if call_id, call_results, and call_date are set
        if (isset($_POST['call_id'], $_POST['call_results'], $_POST['call_date'])) {
            // Get values from the POST request
            $callId = $_POST['call_id'];
            $callResults = $_POST['call_results'];
            $callDate = $_POST['call_date'];

            // Validate form data
            if (empty($callId) || empty($callResults) || empty($callDate)) {
                throw new Exception("Call ID, Call results, and Call date cannot be empty.");
            }

            // Format the date to YYYY-MM-DD
            $date = new DateTime($callDate);
            $formattedDate = $date->format('Y-m-d'); // Format to YYYY-MM-DD for database consistency

            // Use prepared statements to prevent SQL injection
            $sql = "UPDATE calls SET call_results = ?, call_date = ? WHERE call_id = ?";

            // Prepare the statement
            if ($stmt = $db->conn->prepare($sql)) {
                // Bind parameters: "ssi" means string (for call_results and call_date), integer (for call_id)
                $stmt->bind_param("ssi", $callResults, $formattedDate, $callId);

                // Execute the statement and check for success
                if ($stmt->execute()) {
                    // Display success message and redirect
                    echo "<script>alert('Call updated successfully.');</script>";
                    echo "<script>window.location='addcalls.php';</script>"; // Redirect to the calls list page (adjust the filename as needed)
                } else {
                    throw new Exception("Error executing query: " . $stmt->error);
                }

                // Close the prepared statement
                $stmt->close();
            } else {
                throw new Exception("Error preparing the SQL query.");
            }
        } else {
            throw new Exception("Error: Missing required fields.");
        }

        // Close the database connection
        $db->conn->close();
    }
} catch (Exception $e) {
    echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
}
