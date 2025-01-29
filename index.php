<?php
// Start the session
session_start();

try {
	// Check if the user is logged in
	if (!isset($_SESSION['user_id'])) {
		// Redirect to the login page if not logged in
		header('Location: login.php');
		exit();
	}

	// If logged in, proceed with the application
} catch (Exception $e) {
	echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
}
