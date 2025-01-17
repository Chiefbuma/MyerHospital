<?php

require_once 'config.php';
require_once 'class.php';  // Include your class for database connection

if (isset($_GET['id'])) {

	// Create a new instance of the db_class
	$db = new db_class();

	// Get the procedure_id from the URL parameter
	$procedureId = $_GET['id'];

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
		// Display an error if the query failed
		echo "Error: " . $stmt->error;
	}

	// Close the prepared statement
	$stmt->close();
} else {
	echo "Error: Procedure ID not found.";
}

// Close the database connection
$db->conn->close();
