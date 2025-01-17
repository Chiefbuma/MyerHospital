<?php
// Include your database connection file
require_once 'class.php';  // Adjust the path if necessary

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the submit button was clicked
    if (isset($_POST['submit'])) {

        // Get the form data
        $Call_result = $_POST['Call_result'];

        // Create a new instance of the db_class
        $db = new db_class();

        // Prepare the SQL query to insert the record
        $query = "INSERT INTO call_results (Call_result) VALUES (?)";

        // Prepare the SQL statement
        if ($stmt = $db->conn->prepare($query)) {
            // Bind the parameter to the SQL query
            $stmt->bind_param("s", $Call_result);

            // Execute the query
            if ($stmt->execute()) {
                // Display success message and redirect
                echo "<script>alert('New call result added successfully.');</script>";
                echo "<script>window.location='addcallresults.php';</script>";  // Adjust the redirect page as needed
            } else {
                // Display an error if the query execution failed
                echo "Error: " . $stmt->error;
            }

            // Close the prepared statement
            $stmt->close();
        } else {
            // Display an error if the statement preparation failed
            echo "Error preparing the SQL query.";
        }

        // Close the database connection
        $db->conn->close();
    }
}
