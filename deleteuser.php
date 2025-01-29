<?php

require_once 'config.php';
require_once 'class.php';  // Assuming this contains the db_class definition

try {
	if (isset($_GET['id']) && is_numeric($_GET['id'])) {

		// Create a new instance of the db_class
		$db = new db_class();

		$userId = intval($_GET['id']);  // Ensure the user ID is an integer

		// Use prepared statements to prevent SQL injection
		$sql = "DELETE FROM users WHERE id = ?";
		$stmt = $db->conn->prepare($sql);
		$stmt->bind_param("i", $userId);

		if ($stmt->execute()) {
			// Redirect or display a success message
			echo "<script>alert('User successfully deleted.');</script>";
			echo "<script>window.location='adduser.php';</script>";  // Redirect to the users management page
		} else {
			throw new Exception("Error deleting from users table: " . $stmt->error);
		}

		// Close the prepared statement
		$stmt->close();
	} else {
		throw new Exception("Error: Invalid or missing user ID.");
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
