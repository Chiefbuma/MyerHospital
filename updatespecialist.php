<?php
include('config.php');
require_once 'class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $db = new db_class();  // Initialize your database class

    if (isset($_POST['specialist_id'], $_POST['specialist_name'], $_POST['specialty'])) {
        $specialist_id = $_POST['specialist_id'];
        $specialist_name = $_POST['specialist_name'];
        $specialty = $_POST['specialty'];

        // Use prepared statements to prevent SQL injection
        $sql = "UPDATE specialist SET specialist_name = ?, specialty = ? WHERE specialist_id = ?";
        $stmt = $db->conn->prepare($sql);  // Prepare the query
        $stmt->bind_param("ssi", $specialist_name, $specialty, $specialist_id); // Bind parameters (string, string, integer)

        if ($stmt->execute()) {
            // Redirect or display a success message
            echo "<script>alert('Specialist updated successfully.');</script>";
            echo "<script>window.location='addspecialist.php';</script>";  // Redirect to the specialist list page
        } else {
            // Display an error if the query failed
            echo "Error: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        echo "Error: Missing required fields.";
    }

    // Close the database connection
    $db->conn->close();
}
