<?php

require_once 'config.php';
require_once 'class.php';  // Include database class

try {
	if (isset($_GET['id']) && is_numeric($_GET['id'])) {

		// Create a new instance of the db_class
		$db = new db_class();

		$specialistId = intval($_GET['id']);  // Ensure the specialist ID is an integer

		// Use prepared statements to prevent SQL injection
		$sql = "DELETE FROM specialist WHERE specialist_id = ?";
		$stmt = $db->conn->prepare($sql);
		$stmt->bind_param("i", $specialistId);

		if ($stmt->execute()) {
			// Redirect or display a success message
			echo "<script>alert('Specialist successfully deleted.');</script>";
			echo "<script>window.location='addspecialist.php';</script>";  // Redirect to the specialist list page
		} else {
			throw new Exception("Error deleting from specialist table: " . $stmt->error);
		}

		// Close the prepared statement
		$stmt->close();
	} else {
		throw new Exception("Error: Invalid or missing specialist ID.");
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
