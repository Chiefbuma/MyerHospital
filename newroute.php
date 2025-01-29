<?php

include('config.php');
require_once 'class.php';  // Adjust the path if necessary

try {
    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['route_name'], $_POST['address'], $_POST['latitude'], $_POST['longitude'])) {
            $route_name = $_POST['route_name'];
            $address = $_POST['address'];
            $latitude = $_POST['latitude'];
            $longitude = $_POST['longitude'];

            // Validate form data
            if (empty($route_name) || empty($address) || empty($latitude) || empty($longitude)) {
                throw new Exception("All fields are required.");
            }

            // Create a new instance of the db_class
            $db = new db_class();

            // Prepare SQL to insert data into the database
            $sql = "INSERT INTO routes (route_name, address, latitude, longitude) VALUES (?, ?, ?, ?)";

            // Prepare the statement and bind parameters
            if ($stmt = $db->conn->prepare($sql)) {
                $stmt->bind_param("ssdd", $route_name, $address, $latitude, $longitude);

                // Execute the query
                if ($stmt->execute()) {
                    // Redirect or display a success message
                    echo "<script>alert('New route added successfully.');</script>";
                    echo "<script>window.location='addroute.php';</script>";  // Redirect to the branches list page (adjust the filename as needed)
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
        } else {
            throw new Exception("Error: Missing required fields.");
        }
    }
} catch (Exception $e) {
    echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
}
