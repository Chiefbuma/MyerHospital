<?php
// Include your database connection file
require_once 'class.php';  // Adjust the path if necessary

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit'])) {

        // Get the form data

        $Call_results_id = $_POST['Call_results_id'];
        $Call_result = $_POST['Call_result'];



        // Create a new instance of the db_class
        $db = new db_class();

        // Prepare the SQL query to insert the record
        $query = "INSERT INTO call_results (Call_result, Call_results_id) VALUES (?, ?)";

        // Prepare the SQL statement
        if ($stmt = $db->conn->prepare($query)) {
            // Bind the parameters to the SQL query
            $stmt->bind_param("sis", $Call_result, $Call_results_id);

            // Execute the query
            if ($stmt->execute()) {
                // Redirect or display a success message
                echo "<script>alert('New call results added successfully.');</script>";
                echo "<script>window.location='addcallresults.php';</script>";  // Adjust the redirect page as needed
            } else {
                // Display an error if the query failed
                echo "Error: " . $stmt->error;
            }

            // Close the prepared statement
            $stmt->close();
        } else {
            echo "Error preparing the SQL query.";
        }

        // Close the database connection
        $db->conn->close();
    }
}
