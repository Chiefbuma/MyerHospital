<?php
// Include the database class and configuration file
require_once 'class.php';  // Include your database class
require_once 'config.php';  // Include your config file (if necessary)

session_start();
session_regenerate_id(true); // Regenerate session ID to prevent session fixation attacks

// Enable error reporting for debugging (Turn off in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the login form is submitted via POST method
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
  try {
    $db = new db_class(); // Create a new database class instance
    $username = trim($_POST['email']); // Get the username (email) and trim extra spaces
    $password = $_POST['password']; // Get the password entered by the user

    // Prevent empty input fields
    if (empty($username) || empty($password)) {
      $_SESSION['message'] = "Username or password cannot be empty.";
      echo "<script>alert('Username or password cannot be empty'); window.location.href = 'login.php';</script>";
      exit();
    }

    // Check if the login method returns valid user data
    $get_id = $db->login($username, $password);

    if (is_array($get_id) && isset($get_id['count']) && $get_id['count'] > 0) {
      $user_id = $get_id['user_id'];
      $stored_password_hash = $get_id['password_hash']; // Get the hashed password from the database

      // Use password_verify to compare the entered password with the stored hash
      if (password_verify($password, $stored_password_hash)) {
        // Secure session handling
        session_regenerate_id(true); // Prevent session fixation
        $_SESSION['user_id'] = $user_id; // Store the user ID in the session
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT']; // Protect against session hijacking
        $_SESSION['last_activity'] = time(); // Track the last activity time for timeout purposes

        // Redirect securely after showing success message
        echo "<script>alert('Login Successful'); window.location.href = 'patients.php';</script>";
        exit();
      } else {
        $_SESSION['message'] = "Invalid email or password.";
        echo "<script>alert('Password or email is incorrect'); window.location.href = 'login.php';</script>";
        exit();
      }
    } else {
      $_SESSION['message'] = "Invalid email or password.";
      echo "<script>alert('Password or email is incorrect'); window.location.href = 'login.php';</script>";
      exit();
    }
  } catch (Exception $e) {
    $_SESSION['message'] = "An error occurred during login. Please try again later.";
    echo "<script>alert('An error occurred during login. Please try again later'); window.location.href = 'login.php';</script>";
    exit();
  }
} else {
  // If accessed directly without submitting the form
  header("Location: login.php");
  exit();
}
