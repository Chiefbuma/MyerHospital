<?php
session_start();

// Set the timeout period in seconds (5 minutes = 300 seconds)
$timeout_duration = 30;

// Check if 'user_id' session is set and if the timeout period has passed
if (isset($_SESSION['user_id'])) {
	// If the session variable 'last_activity' is set, check the time elapsed
	if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
		// If the session has been inactive for more than 5 minutes, destroy the session and redirect
		session_unset();     // Remove all session variables
		session_destroy();   // Destroy the session
		header('Location: loginuser.html');
		exit;
	}
	// Update the last activity time
	$_SESSION['last_activity'] = time();
} else {
	// Redirect to login page if the user is not logged in
	header('Location: loginuser.html');
	exit;
}
