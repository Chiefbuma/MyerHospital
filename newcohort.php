<?php
// Include your database connection file
require_once 'class.php';  // Adjust the path if necessary

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit'])) {
        // Get the branch name from the form
        $cohort_name = $_POST['cohort_name'];
        $team_lead = $_POST['team_lead'];

        // Create a new instance of the db_class
        $db = new db_class();

        // Prepare the SQL query to insert the branch
        $query = "INSERT INTO cohort (cohort_name,team_lead) VALUES (?,?)";

        // Prepare the SQL statement
        if ($stmt = $db->conn->prepare($query)) {
            // Bind the parameter to the SQL query
            $stmt->bind_param("ss", $cohort_name, $team_lead);

            // Execute the query
            if ($stmt->execute()) {
                // Redirect or display a success message
                echo "<script>alert('New branch added successfully.');</script>";
                echo "<script>window.location='addcohort.php';</script>";  // Redirect to the branches list page (adjust the filename as needed)
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
