<?php

require_once 'config.php';
require_once 'class.php';  // Assuming this contains the db_class definition

if (isset($_GET['id'])) {

	// Create a new instance of the db_class
	$db = new db_class();

	$callResultsId = $_GET['id'];  // Get the Call_results_id from the URL parameter

	// Use prepared statements to prevent SQL injection
	$sql = "DELETE FROM call_results WHERE Call_results_id = ?";  // Adjusted to the correct table name
	$stmt = $db->conn->prepare($sql);

	// Bind the Call_results_id as an integer
	$stmt->bind_param("i", $callResultsId);

	if ($stmt->execute()) {
		// Redirect or display a success message
		echo "<script>alert('Call result successfully deleted.');</script>";
		echo "<script>window.location='addcallresults.php';</script>";  // Redirect to the call results management page
	} else {
		// Display an error if the query failed
		echo "Error: " . $stmt->error;
	}

	// Close the prepared statement
	$stmt->close();
} else {
	echo "Error: Call result ID not provided.";
}

// Close the database connection
$db->conn->close();
