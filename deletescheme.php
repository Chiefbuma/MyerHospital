<?php

require_once 'config.php';

require_once 'class.php';  // Adj

if (isset($_GET['id'])) {

	// Create a new instance of the db_class
	$db = new db_class();

	$branchId = $_GET['id'];

	// Use prepared statements to prevent SQL injection
	$sql = "DELETE FROM scheme WHERE scheme_id = ?";
	$stmt = $db->conn->prepare($sql);
	$stmt->bind_param("i", $branchId);

	if ($stmt->execute()) {
		// Redirect or display a success message
		echo "<script>alert('New  scheme successfully.');</script>";
		echo "<script>window.location='addscheme.php';</script>";  // Redirect to the branches list page (adjust the filename as needed)
	} else {
		// Display an error if the query failed
		echo "Error: " . $stmt->error;
	}

	// Close the prepared statement
	$stmt->close();
} else {
	echo "Error preparing the SQL query.";
}

// Close the database connection
$db->conn->close();
