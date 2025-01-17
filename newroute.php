<?php

include('config.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['route_name'], $_POST['address'], $_POST['latitude'], $_POST['longitude'])) {
        $route_name = $_POST['route_name'];
        $address = $_POST['address'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];

        // Prepare SQL to insert data into the database
        $sql = "INSERT INTO routes (route_name, address, latitude, longitude) VALUES (?, ?, ?, ?)";

        // Prepare the statement and bind parameters
        $stmt = $db->conn->prepare($sql);
        $stmt->bind_param("ssdd", $route_name, $address, $latitude, $longitude);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect or display a success message
            echo "<script>alert('New routeadded successfully.');</script>";
            echo "<script>window.location='addroute.php';</script>";  // Redirect to the branches list page (adjust the filename as needed)
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
