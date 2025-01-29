<?php

require_once 'config.php';
require_once 'class.php';  // Assuming class.php contains the db_class definition

try {
	if (isset($_GET['id']) && is_numeric($_GET['id'])) {

		// Create a new instance of the db_class
		$db = new db_class();

		// Get the diagnosis_id from the URL parameter and ensure it's an integer
		$diagnosisId = intval($_GET['id']);

		// Use prepared statements to prevent SQL injection
		$sql = "DELETE FROM diagnosis WHERE diagnosis_id = ?";
		$stmt = $db->conn->prepare($sql);
		$stmt->bind_param("i", $diagnosisId);  // "i" means integer type for diagnosis_id

		if ($stmt->execute()) {
			// Redirect or display a success message
			echo "<script>alert('Diagnosis successfully deleted.');</script>";
			echo "<script>window.location='adddiagnosis.php';</script>";  // Adjust the filename to your diagnosis page
		} else {
			throw new Exception("Error deleting from diagnosis table: " . $stmt->error);
		}

		// Close the prepared statement
		$stmt->close();
	} else {
		throw new Exception("Error: Invalid or missing diagnosis ID.");
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
