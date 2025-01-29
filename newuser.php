<?php

include('config.php');
require_once 'class.php';  // Adjust the path if necessary

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Create a new instance of the db_class
        $db = new db_class();

        // Check if required fields are set
        if (isset($_POST['username'], $_POST['password'], $_POST['email'], $_POST['role'], $_POST['cohort_id'], $_POST['branch_id'])) {

            // Get form data
            $username = $_POST['username'];
            $password = $_POST['password'];  // You should hash the password before storing it
            $email = $_POST['email'];
            $role = $_POST['role'];
            $cohort_id = $_POST['cohort_id'];
            $branch_id = $_POST['branch_id'];

            // Validate form data
            if (empty($username) || empty($password) || empty($email) || empty($role) || empty($cohort_id) || empty($branch_id)) {
                throw new Exception("All fields are required.");
            }

            // Check if email already exists
            $email_check_sql = "SELECT * FROM users WHERE email = ?";
            $stmt = $db->conn->prepare($email_check_sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Email already exists
                echo "<script>alert('Email already exists. Please use a different email.');</script>";
                echo "<script>window.location='adduser.php';</script>";
            } else {
                // Hash the password for security
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                // Prepare SQL to insert data into the users table
                $sql = "INSERT INTO users (username, password_hash, email, role, cohort_id, branch_id) VALUES (?, ?, ?, ?, ?, ?)";

                // Prepare the statement and bind parameters
                if ($stmt = $db->conn->prepare($sql)) {
                    $stmt->bind_param("ssssii", $username, $password_hash, $email, $role, $cohort_id, $branch_id);

                    // Execute the query
                    if ($stmt->execute()) {
                        // Redirect or display a success message
                        echo "<script>alert('New user added successfully.');</script>";
                        echo "<script>window.location='adduser.php';</script>";  // Redirect to the users management page
                    } else {
                        throw new Exception("Error executing query: " . $stmt->error);
                    }

                    // Close the prepared statement
                    $stmt->close();
                } else {
                    throw new Exception("Error preparing the SQL query.");
                }
            }

            // Close the result set and the prepared statement
            $result->free();
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
