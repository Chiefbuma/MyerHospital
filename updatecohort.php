<?php
include('config.php');
require_once 'class.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $db = new db_class();  // Initialize your database class

        if (isset($_POST['cohort_id'], $_POST['cohort_name'], $_POST['team_lead'])) {
            $cohort_id = $_POST['cohort_id'];
            $cohort_name = $_POST['cohort_name'];
            $team_lead = $_POST['team_lead'];

            // Validate form data
            if (empty($cohort_id) || empty($cohort_name) || empty($team_lead)) {
                throw new Exception("Cohort ID, Cohort Name, and Team Lead cannot be empty.");
            }

            // Use prepared statements to prevent SQL injection
            $sql = "UPDATE cohort SET cohort_name = ?, team_lead = ? WHERE cohort_id = ?";
            $stmt = $db->conn->prepare($sql);  // Prepare the query
            $stmt->bind_param("ssi", $cohort_name, $team_lead, $cohort_id); // Bind parameters (string, string, integer)

            if ($stmt->execute()) {
                // Redirect or display a success message
                echo "<script>alert('Cohort updated successfully.');</script>";
                echo "<script>window.location='addcohort.php';</script>";  // Redirect to the branches list page (adjust the filename as needed)
            } else {
                throw new Exception("Error executing query: " . $stmt->error);
            }

            // Close the prepared statement
            $stmt->close();
        } else {
            throw new Exception("Error: Missing required fields.");
        }

        // Close the database connection
        $db->conn->close();
    }
} catch (Exception $e) {
    echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
}
