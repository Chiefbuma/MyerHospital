<?php
include('config.php');
require_once 'class.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $db = new db_class();  // Initialize your database class

        if (isset($_POST['route_id'], $_POST['route_name'], $_POST['address'], $_POST['latitude'], $_POST['longitude'])) {
            $route_id = $_POST['route_id'];
            $route_name = $_POST['route_name'];
            $address = $_POST['address'];
            $latitude = $_POST['latitude'];
            $longitude = $_POST['longitude'];

            // Validate form data
            if (empty($route_id) || empty($route_name) || empty($address) || empty($latitude) || empty($longitude)) {
                throw new Exception("All fields are required.");
            }

            // Use prepared statements to prevent SQL injection
            $sql = "UPDATE routes SET route_name = ?, address = ?, latitude = ?, longitude = ? WHERE route_id = ?";
            $stmt = $db->conn->prepare($sql);  // Prepare the query

            if ($stmt === false) {
                throw new Exception("Error preparing the SQL query: " . $db->conn->error);
            } else {
                $stmt->bind_param("ssddi", $route_name, $address, $latitude, $longitude, $route_id); // Bind parameters (string, string, double, double, integer)

                // Execute the query
                if ($stmt->execute()) {
                    // Redirect or display a success message
                    echo "<script>alert('Route updated successfully.');</script>";
                    echo "<script>window.location='addroutes.php';</script>";  // Redirect to the routes list page
                } else {
                    throw new Exception("Error executing query: " . $stmt->error);
                }

                // Close the prepared statement
                $stmt->close();
            }
        } else {
            throw new Exception("Error: Missing required fields.");
        }

        // Close the database connection
        $db->conn->close();
    }
} catch (Exception $e) {
    echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
}
