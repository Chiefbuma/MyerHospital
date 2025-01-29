<?php

require_once 'config.php';
require_once 'class.php';  // Include your class for database connection

try {
	if (isset($_GET['id']) && is_numeric($_GET['id'])) {

		// Create a new instance of the db_class
		$db = new db_class();

		// Get the procedure_id from the URL parameter
		$procedureId = intval($_GET['id']);  // Ensure the procedure ID is an integer

		// Use prepared statements to prevent SQL injection
		$sql = "DELETE FROM procedures WHERE procedure_id = ?"; // Adjust your table name if it's different
		$stmt = $db->conn->prepare($sql);
		$stmt->bind_param("i", $procedureId);

		// Execute the statement and check if it was successful
		if ($stmt->execute()) {
			// Redirect or display a success message
			echo "<script>alert('Procedure deleted successfully.');</script>";
			echo "<script>window.location='addprocedure.php';</script>";  // Redirect to the procedures list page
		} else {
			throw new Exception("Error deleting from procedures table: " . $stmt->error);
		}

		// Close the prepared statement
		$stmt->close();
	} else {
		throw new Exception("Error: Invalid or missing procedure ID.");
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
