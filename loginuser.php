<?php
require_once 'class.php'; // Include your database class
require_once 'config.php'; // Include your config file (if necessary)

session_start();

// Enable error reporting for debugging (Turn off in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
  try {
    $db = new db_class();
    $username = trim($_POST['email']);  // Trim input for extra spaces
    $password = $_POST['password'];

    // Prevent empty input fields
    if (empty($username) || empty($password)) {
      $_SESSION['message'] = "Username or password cannot be empty.";
      header("Location: login.php");
      exit();
    }

    // Check if login method returns valid user data
    $get_id = $db->login($username, $password);

    if (is_array($get_id) && isset($get_id['count']) && $get_id['count'] > 0) {
      $user_id = $get_id['user_id'];
      $stored_password_hash = $get_id['password_hash']; // Get hashed password from database

      // Use password_verify to compare the entered password with the stored hash
      if (password_verify($password, $stored_password_hash)) {
        // Secure session handling
        session_regenerate_id(true); // Prevent session fixation
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT']; // Protect against session hijacking
        $_SESSION['last_activity'] = time(); // Track last activity for timeout

        // Redirect securely
        header("Location: patients.php");
        exit();
      } else {
        $_SESSION['message'] = "Invalid email or password.";
        header("Location: login.php");
        exit();
      }
    } else {
      $_SESSION['message'] = "Invalid email or password.";
      header("Location: login.php");
      exit();
    }
  } catch (Exception $e) {
    $_SESSION['message'] = "An error occurred during login. Please try again later.";
    header("Location: login.php");
    exit();
  }
} else {
  // If accessed directly without submitting the form
  header("Location: login.php");
  exit();
}
