<?php

require_once 'config.php';
require_once 'class.php';  // Assuming class.php contains the db_class definition

if (isset($_GET['id'])) {

	// Create a new instance of the db_class
	$db = new db_class();

	// Get the diagnosis_id from the URL
	$diagnosisId = $_GET['id'];

	// Use prepared statements to prevent SQL injection
	$sql = "DELETE FROM diagnosis WHERE diagnosis_id = ?";
	$stmt = $db->conn->prepare($sql);
	$stmt->bind_param("i", $diagnosisId);  // "i" means integer type for diagnosis_id

	if ($stmt->execute()) {
		// Redirect or display a success message
		echo "<script>alert('Diagnosis successfully deleted.');</script>";
		echo "<script>window.location='adddiagnosis.php';</script>";  // Adjust the filename to your diagnosis page
	} else {
		// Display an error if the query failed
		echo "Error: " . $stmt->error;
	}

	// Close the prepared statement
	$stmt->close();
} else {
	echo "Error: Missing diagnosis ID.";
}

// Close the database connection
$db->conn->close();
