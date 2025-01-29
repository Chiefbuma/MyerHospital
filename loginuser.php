<?php
require_once 'class.php'; // Include your database class
require_once 'config.php'; // Include your config file (if necessary)

session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['login'])) {
  try {
    $db = new db_class();
    $username = $_POST['email'];  // Get the username (email)
    $password = $_POST['password'];  // Get the password

    // Check if username and password fields are not empty
    if (empty($username) || empty($password)) {
      throw new Exception("Username or password cannot be empty.");
    }

    // Attempt to call the login method
    $get_id = $db->login($username, $password);

    // Check if $get_id is valid and contains expected structure
    if (is_array($get_id) && isset($get_id['count']) && $get_id['count'] > 0) {
      // Retrieve user data
      $user_id = $get_id['user_id'];
      $stored_password_hash = $get_id['password_hash']; // Get the hashed password from the database

      // Use password_verify to check the entered password against the hashed password
      if (password_verify($password, $stored_password_hash)) {
        // Password is correct, proceed with the login
        $_SESSION['user_id'] = $user_id;
        unset($_SESSION['message']);
        echo "<script>alert('Login Successful')</script>";
        echo "<script>window.location='patients.php'</script>"; // Redirect to home page after successful login
      } else {
        // Password is incorrect
        $_SESSION['message'] = "Invalid email or Password";
        echo "<script>alert('Password or email is incorrect')</script>";
        echo "<script>window.location='login.php'</script>"; // Redirect back to login page
      }
    } else {
      // User not found or invalid login
      $_SESSION['message'] = "Invalid email or Password";
      echo "<script>alert('Failed attempt to log in')</script>";
      echo "<script>window.location='login.php'</script>"; // Redirect back to login page
    }
  } catch (Exception $e) {
    // Handle and log exceptions
    $_SESSION['message'] = "An error occurred during login. Please try again later.";
    echo "<script>alert('Failed attempt to log in')</script>";
    echo "<script>window.location='login.php'</script>"; // Redirect back to login page
  }
}
