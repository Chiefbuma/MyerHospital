<?php
// Include your database connection file
require_once 'class.php';  // Adjust the path if necessary

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit'])) {
        try {
            // Get the cohort name and team lead from the form
            $cohort_name = $_POST['cohort_name'];
            $team_lead = $_POST['team_lead'];

            // Validate form data
            if (empty($cohort_name) || empty($team_lead)) {
                throw new Exception("Cohort name and team lead cannot be empty.");
            }

            // Create a new instance of the db_class
            $db = new db_class();

            // Prepare the SQL query to insert the cohort
            $query = "INSERT INTO cohort (cohort_name, team_lead) VALUES (?, ?)";

            // Prepare the SQL statement
            if ($stmt = $db->conn->prepare($query)) {
                // Bind the parameters to the SQL query
                $stmt->bind_param("ss", $cohort_name, $team_lead);

                // Execute the query
                if ($stmt->execute()) {
                    // Redirect or display a success message
                    echo "<script>alert('New cohort added successfully.');</script>";
                    echo "<script>window.location='addcohort.php';</script>";  // Redirect to the cohorts list page (adjust the filename as needed)
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
