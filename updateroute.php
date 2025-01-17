<?php
include('config.php');
require_once 'class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $db = new db_class();  // Initialize your database class

    if (isset($_POST['cohort_id'], $_POST['cohort_name'], $_POST['team_lead'])) {
        $cohort_id = $_POST['cohort_id'];
        $cohort_name = $_POST['cohort_name'];
        $team_lead = $_POST['team_lead'];

        // Use prepared statements to prevent SQL injection
        $sql = "UPDATE cohort SET cohort_name = ?, team_lead = ? WHERE cohort_id = ?";
        $stmt = $db->conn->prepare($sql);  // Prepare the query
        $stmt->bind_param("ssi", $cohort_name, $team_lead, $cohort_id); // Bind parameters (string, string, integer)


        if ($stmt->execute()) {
            // Redirect or display a success message
            echo "<script>alert('Cohort updated succesfully.');</script>";
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
