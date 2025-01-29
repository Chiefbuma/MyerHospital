<?php

require_once 'config.php';
require_once 'class.php';  // Assuming this contains the db_class definition

try {
	if (isset($_GET['id']) && is_numeric($_GET['id'])) {

		// Create a new instance of the db_class
		$db = new db_class();

		$userId = intval($_GET['id']);  // Get the user ID from the URL parameter and ensure it's an integer

		// Start a transaction
		$db->conn->begin_transaction();

		// Use prepared statements to prevent SQL injection
		$sql = "DELETE FROM patient WHERE patient_id = ?";  // Adjust the table name if needed
		$stmt = $db->conn->prepare($sql);
		$stmt->bind_param("i", $userId);  // Bind the user ID as an integer

		if ($stmt->execute()) {
			// Commit the transaction
			$db->conn->commit();

			// Redirect or display a success message
			echo "<script>alert('User successfully deleted.');</script>";
			echo "<script>window.location='patients.php';</script>";  // Redirect to the users management page
		} else {
			throw new Exception("Error deleting from patient table: " . $stmt->error);
		}

		// Close the prepared statement
		$stmt->close();
	} else {
		throw new Exception("Error: Invalid or missing User ID.");
	}
} catch (Exception $e) {
	// Rollback the transaction in case of error
	if ($db->conn->in_transaction) {
		$db->conn->rollback();
	}
	// Display the error message
	echo "Exception: " . $e->getMessage();
} finally {
	// Close the database connection
	if (isset($db->conn)) {
		$db->conn->close();
	}
}
