<?php
include('config.php');
require_once 'class.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $db = new db_class();  // Initialize your database class

        // Check if the necessary POST data is provided
        if (isset($_POST['user_id'], $_POST['username'], $_POST['email'], $_POST['role'], $_POST['cohort_id'], $_POST['branch_id'])) {

            // Get the user data from the form
            $user_id = $_POST['user_id'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $role = $_POST['role'];
            $cohort_id = $_POST['cohort_id'];
            $branch_id = $_POST['branch_id'];

            // Validate form data
            if (empty($user_id) || empty($username) || empty($email) || empty($role) || empty($cohort_id) || empty($branch_id)) {
                throw new Exception("All fields are required.");
            }

            // Prepare SQL to update user data in the database
            $sql = "UPDATE users SET username = ?, email = ?, role = ?, cohort_id = ?, branch_id = ? WHERE id = ?";

            // Prepare the statement and bind parameters
            $stmt = $db->conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Error preparing the SQL query: " . $db->conn->error);
            }

            $stmt->bind_param("sssiii", $username, $email, $role, $cohort_id, $branch_id, $user_id); // Bind parameters (string, string, string, integer, integer, integer)

            // Execute the query
            if ($stmt->execute()) {
                // Redirect or display a success message
                echo "<script>alert('User updated successfully.');</script>";
                echo "<script>window.location='adduser.php';</script>";  // Redirect to the users management page
            } else {
                throw new Exception("Error executing query: " . $stmt->error);
            }

            // Close the prepared statement
            $stmt->close();
        } else {
            throw new Exception("Please fill in all fields.");
        }

        // Close the database connection
        $db->conn->close();
    }
} catch (Exception $e) {
    echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
}
