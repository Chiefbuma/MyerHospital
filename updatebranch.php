<?php
include('config.php');

require_once 'class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $db = new db_class();

    if (isset($_POST['branch_id'], $_POST['branch_name'])) {
        $branchId = $_POST['branch_id'];
        $branchName = $_POST['branch_name'];

        // Use prepared statements to prevent SQL injection
        $sql = "UPDATE branch SET branch_name = ? WHERE branch_id = ?";
        $stmt =  $db->conn->prepare($sql);
        $stmt->bind_param("si", $branchName, $branchId); // "si" means string, integer
        if ($stmt->execute()) {
            // Redirect or display a success message
            echo "<script>alert('branch updated successfully.');</script>";
            echo "<script>window.location='addbranch.php';</script>";  // Redirect to the branches list page (adjust the filename as needed)
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
