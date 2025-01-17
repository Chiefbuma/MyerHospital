<?php

include('config.php');
require_once 'class.php';  // Adjust the path if necessary

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debugging: Check the POST data

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
            $stmt = $db->conn->prepare($sql);
            $stmt->bind_param("ssssii", $username, $password_hash, $email, $role, $cohort_id, $branch_id);

            // Execute the query
            if ($stmt->execute()) {
                // Redirect or display a success message
                echo "<script>alert('New user added successfully.');</script>";
                echo "<script>window.location='adduser.php';</script>";  // Redirect to the users management page
            } else {
                // Display an error if the query failed
                echo "Error: " . $stmt->error;
            }

            // Close the prepared statement
            $stmt->close();
        }

        // Close the result set and the prepared statement
        $result->free();
        $stmt->close();
    } else {
        echo "Please fill in all fields.";
    }

    // Close the database connection
    $db->conn->close();
}
