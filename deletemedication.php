<?php

require_once 'config.php';
require_once 'class.php'; // Include database class

if (isset($_GET['id'])) {

	// Create a new instance of the db_class
	$db = new db_class();

	$medicationId = $_GET['id'];

	// Use prepared statements to prevent SQL injection
	$sql = "DELETE FROM medication WHERE medication_id = ?";
	$stmt = $db->conn->prepare($sql);
	$stmt->bind_param("i", $medicationId);

	if ($stmt->execute()) {
		// Redirect or display a success message
		echo "<script>alert('Medication successfully deleted.');</script>";
		echo "<script>window.location='addmedication.php';</script>"; // Redirect to the medication list page
	} else {
		// Display an error if the query failed
		echo "Error: " . $stmt->error;
	}

	// Close the prepared statement
	$stmt->close();
} else {
	echo "Error: Invalid request. Medication ID not provided.";
}

// Close the database connection
$db->conn->close();
