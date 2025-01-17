<?php

require_once 'config.php';
require_once 'class.php';  // Assuming this contains the db_class definition

if (isset($_GET['id'])) {

	// Create a new instance of the db_class
	$db = new db_class();

	$userId = $_GET['id'];  // Get the user ID from the URL parameter

	// Use prepared statements to prevent SQL injection
	$sql = "DELETE FROM users WHERE id = ?";  // Adjust the table name if needed
	$stmt = $db->conn->prepare($sql);
	$stmt->bind_param("i", $userId);  // Bind the user ID as an integer

	if ($stmt->execute()) {
		// Redirect or display a success message
		echo "<script>alert('User successfully deleted.');</script>";
		echo "<script>window.location='adduser.php';</script>";  // Redirect to the users management page
	} else {
		// Display an error if the query failed
		echo "Error: " . $stmt->error;
	}

	// Close the prepared statement
	$stmt->close();
} else {
	echo "Error: User ID not provided.";
}

// Close the database connection
$db->conn->close();
