<?php

require_once 'config.php';
require_once 'class.php';  // Assuming this contains the db_class definition

try {
	if (isset($_GET['id']) && is_numeric($_GET['id'])) {

		// Create a new instance of the db_class
		$db = new db_class();

		$callId = intval($_GET['id']);  // Get the call ID from the URL parameter and ensure it's an integer

		// Use prepared statements to prevent SQL injection
		$sql = "DELETE FROM calls WHERE call_id = ?";
		$stmt = $db->conn->prepare($sql);
		$stmt->bind_param("i", $callId);

		if ($stmt->execute()) {
			// Redirect or display a success message
			echo "<script>alert('Call successfully deleted.');</script>";
			echo "<script>window.location='addcalls.php';</script>";  // Redirect to the calls management page
		} else {
			throw new Exception("Error deleting from calls table: " . $stmt->error);
		}

		// Close the prepared statement
		$stmt->close();
	} else {
		throw new Exception("Error: Invalid or missing call ID.");
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
