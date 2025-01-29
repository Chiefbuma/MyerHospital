<?php
// Include your database connection file
require_once 'class.php';  // Adjust the path if necessary

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit'])) {
        try {
            // Get the form data
            $call_date = $_POST['call_date'];
            $patient_id = $_POST['patient_id'];
            $call_results = $_POST['call_results'];

            // Validate form data
            if (empty($call_date) || empty($patient_id) || empty($call_results)) {
                throw new Exception("All fields are required.");
            }

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
