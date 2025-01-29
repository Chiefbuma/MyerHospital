<?php

require_once 'config.php';
require_once 'class.php'; // Include database class

try {
	if (isset($_GET['id']) && is_numeric($_GET['id'])) {

		// Create a new instance of the db_class
		$db = new db_class();

		$medicationId = intval($_GET['id']);  // Ensure the medication ID is an integer

		// Use prepared statements to prevent SQL injection
		$sql = "DELETE FROM medication_use WHERE medication_use_id = ?";
		$stmt = $db->conn->prepare($sql);
		$stmt->bind_param("i", $medicationId);

		if ($stmt->execute()) {
			// Redirect or display a success message
			echo "<script>alert('Medication successfully deleted.');</script>";
			echo "<script>window.location='assesmeds.php';</script>"; // Redirect to the medication list page
		} else {
			throw new Exception("Error deleting from medication_use table: " . $stmt->error);
		}

		// Close the prepared statement
		$stmt->close();
	} else {
		throw new Exception("Error: Invalid or missing medication ID.");
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
