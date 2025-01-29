<?php

require_once 'config.php';
require_once 'class.php';  // Assuming this contains the db_class definition

try {
	if (isset($_GET['id']) && is_numeric($_GET['id'])) {

		// Create a new instance of the db_class
		$db = new db_class();

		$chronicId = intval($_GET['id']);  // Get the chronic_id from the URL parameter and ensure it's an integer

		// Use prepared statements to prevent SQL injection
		$sql = "DELETE FROM chronic WHERE chronic_id = ?";
		$stmt = $db->conn->prepare($sql);
		$stmt->bind_param("i", $chronicId);

		if ($stmt->execute()) {
			// Redirect or display a success message
			echo "<script>alert('Deleted successfully.');</script>";
			echo "<script>window.location='asseschronic.php';</script>";  // Redirect to the chronic list page (adjust the filename as needed)
		} else {
			throw new Exception("Error deleting from chronic table: " . $stmt->error);
		}

		// Close the prepared statement
		$stmt->close();
	} else {
		throw new Exception("Error: Invalid or missing chronic ID.");
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
