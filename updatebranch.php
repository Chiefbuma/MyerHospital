<?php
include('config.php');
require_once 'class.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $db = new db_class();

        if (isset($_POST['branch_id'], $_POST['branch_name'])) {
            $branchId = $_POST['branch_id'];
            $branchName = $_POST['branch_name'];

            // Validate form data
            if (empty($branchId) || empty($branchName)) {
                throw new Exception("Branch ID and Branch Name cannot be empty.");
            }

            // Use prepared statements to prevent SQL injection
            $sql = "UPDATE branch SET branch_name = ? WHERE branch_id = ?";
            $stmt = $db->conn->prepare($sql);
            $stmt->bind_param("si", $branchName, $branchId); // "si" means string, integer

            if ($stmt->execute()) {
                // Redirect or display a success message
                echo "<script>alert('Branch updated successfully.');</script>";
                echo "<script>window.location='addbranch.php';</script>";  // Redirect to the branches list page (adjust the filename as needed)
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
