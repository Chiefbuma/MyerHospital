<?php

require_once 'config.php';
require_once 'class.php';  // Assuming this contains the db_class definition

try {
	if (isset($_GET['id']) && is_numeric($_GET['id'])) {

		// Create a new instance of the db_class
		$db = new db_class();

		$cohortId = intval($_GET['id']);  // Get the cohort_id from the URL parameter and ensure it's an integer

		// Use prepared statements to prevent SQL injection
		$sql = "DELETE FROM cohort WHERE cohort_id = ?";
		$stmt = $db->conn->prepare($sql);
		$stmt->bind_param("i", $cohortId);

		// Execute the query
		if ($stmt->execute()) {
			// Redirect or display a success message
			echo "<script>alert('Deleted successfully.');</script>";
			echo "<script>window.location='addcohort.php';</script>";  // Redirect to the cohort list page (adjust the filename as needed)
		} else {
			throw new Exception("Error deleting from cohort table: " . $stmt->error);
		}

		// Close the prepared statement
		$stmt->close();
	} else {
		throw new Exception("Error: Invalid or missing cohort ID.");
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
