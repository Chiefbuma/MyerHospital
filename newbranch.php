<?php
// Include your database connection file
require_once 'class.php';  // Adjust the path if necessary

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit'])) {
        try {
            // Get the branch name from the form
            $branch_name = $_POST['branch'];

            // Validate branch name
            if (empty($branch_name)) {
                throw new Exception("Branch name cannot be empty.");
            }

            // Create a new instance of the db_class
            $db = new db_class();

            // Prepare the SQL query to insert the branch
            $query = "INSERT INTO branch (branch_name) VALUES (?)";

            // Prepare the SQL statement
            if ($stmt = $db->conn->prepare($query)) {
                // Bind the parameter to the SQL query
                $stmt->bind_param("s", $branch_name);

                // Execute the query
                if ($stmt->execute()) {
                    // Redirect or display a success message
                    echo "<script>alert('New branch added successfully.');</script>";
                    echo "<script>window.location='addbranch.php';</script>";  // Redirect to the branches list page (adjust the filename as needed)
                } else {
                    throw new Exception("Error executing query: " . $stmt->error);
                }

                // Close the prepared statement
                $stmt->close();
            } else {
                throw new Exception("Error preparing the SQL query.");
            }

            // Close the database connection
            $db->conn->close();
        } catch (Exception $e) {
            echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
        }
    }
}
