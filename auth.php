<?php
// auth.php

// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'class.php';

$db = new db_class();

// Set timeout duration (5 minutes = 300 seconds)
$timeout_duration = 300;

// Regenerate session ID periodically to avoid session fixation
session_regenerate_id(true);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check for session hijacking (User-Agent mismatch)
if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Check for inactivity timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // Session expired, destroy session and redirect to login page
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=true"); // Redirect with timeout message
    exit();
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch the role and cohort from the database
$query = "SELECT branch_id, email, role, cohort_id FROM users WHERE id = '$user_id'";
$result = $db->conn->query($query);

if ($result && $row = mysqli_fetch_assoc($result)) {
    $user_location = $row['branch_id'];
    $user_role = $row['role'];
    $user_cohort = $row['cohort_id'];
    $user_email = $row['email'];
} else {
    echo "Error fetching user information: " . mysqli_error($db->conn);
    exit;
}

// Query to fetch branch name based on branch_id
$branch_query = "SELECT branch_name FROM branch WHERE branch_id = ?";
$stmt = $db->conn->prepare($branch_query);
$stmt->bind_param("i", $user_location);
$stmt->execute();
$branch_result = $stmt->get_result();

if ($branch_result && $branch_row = $branch_result->fetch_assoc()) {
    $branch_name = $branch_row['branch_name'];
} else {
    $branch_name = "Main branch"; // Default if no branch found
}
