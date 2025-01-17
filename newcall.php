<?php
// Include your database connection file
require_once 'class.php';  // Adjust the path if necessary

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit'])) {
        // Get the form data
        $call_date = $_POST['call_date'];
        $patient_id = $_POST['patient_id'];
        $call_results = $_POST['call_results'];

        // Format the call_date to the proper date format (YYYY-MM-DD)
        $formatted_date = date('Y-m-d', strtotime($call_date));

        // Create a new instance of the db_class
        $db = new db_class();

        // Prepare the SQL query to insert the record
        $query = "INSERT INTO calls (call_results, patient_id, call_date) VALUES (?, ?, ?)";

        // Prepare the SQL statement
        if ($stmt = $db->conn->prepare($query)) {
            // Bind the parameters to the SQL query
            $stmt->bind_param("sis", $call_results, $patient_id, $formatted_date);

            // Execute the query
            if ($stmt->execute()) {
                // Redirect or display a success message
                echo "<script>alert('New call record added successfully.');</script>";
                echo "<script>window.location='addcalls.php';</script>";  // Adjust the redirect page as needed
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
