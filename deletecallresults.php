<?php

require_once 'config.php';
require_once 'class.php';  // Assuming this contains the db_class definition

try {
	if (isset($_GET['id']) && is_numeric($_GET['id'])) {

		// Create a new instance of the db_class
		$db = new db_class();

		$callResultsId = intval($_GET['id']);  // Get the Call_results_id from the URL parameter and ensure it's an integer

		// Use prepared statements to prevent SQL injection
		$sql = "DELETE FROM call_results WHERE Call_results_id = ?";
		$stmt = $db->conn->prepare($sql);

		// Bind the Call_results_id as an integer
		$stmt->bind_param("i", $callResultsId);

		if ($stmt->execute()) {
			// Redirect or display a success message
			echo "<script>alert('Call result successfully deleted.');</script>";
			echo "<script>window.location='addcallresults.php';</script>";  // Redirect to the call results management page
		} else {
			throw new Exception("Error deleting from call_results table: " . $stmt->error);
		}

		// Close the prepared statement
		$stmt->close();
	} else {
		throw new Exception("Error: Invalid or missing call result ID.");
	}
} catch (Exception $e) {
	// Display the error message
	echo "Exception: " . $e->getMessage();
} finally {
	// Close the database connection
	if (isset($db->conn)) {
		$db->conn->close();
	}
}
