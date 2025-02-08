<?php

include('config.php');
require_once 'class.php'; // Adjust the path if necessary

try {
    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Create a new instance of the db_class
        $db = new db_class();

        if (
            isset(
                $_POST['item_name'],        // Corresponds to item_name in the form
                $_POST['formulation'],      // Corresponds to formulation in the form
                $_POST['category'],         // Corresponds to category in the form
                $_POST['brand'],            // Corresponds to brand in the form
                $_POST['formulation']       // Corresponds to formulation in the form
            )
        ) {
            // Retrieve form data
            $item_name = $_POST['item_name'];
            $formulation = $_POST['formulation'];
            $category = $_POST['category'];
            $brand = $_POST['brand'];

            // Validate form data
            if (empty($item_name) || empty($formulation) || empty($category) || empty($brand)) {
                throw new Exception("All fields are required.");
            }

            // Prepare SQL to insert data into the database
            $sql = "INSERT INTO medication (item_name, formulation, category, brand) 
                    VALUES (?, ?, ?, ?)";

            // Prepare the statement and bind parameters
            if ($stmt = $db->conn->prepare($sql)) {
                $stmt->bind_param("ssss", $item_name, $formulation, $category, $brand);

                // Execute the query
                if ($stmt->execute()) {
                    // Redirect or display a success message
                    echo "<script>alert('New medication added successfully.');</script>";
                    echo "<script>window.location='addmedication.php';</script>"; // Redirect to the medication list page
                } else {
                    throw new Exception("Error executing query: " . $stmt->error);
                    echo "<script>window.location='addmedication.php';</script>"; // Redirect to the medication list page
                }

                // Close the prepared statement
                $stmt->close();
            } else {
                throw new Exception("Error preparing the SQL query.");
                echo "<script>window.location='addmedication.php';</script>"; // Redirect to the medication list page
            }
        } else {
            throw new Exception("Error: Missing required fields.");
            echo "<script>window.location='addmedication.php';</script>"; // Redirect to the medication list page
        }

        // Close the database connection
        $db->conn->close();
    }
} catch (Exception $e) {
    echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
    echo "<script>window.location='addmedication.php';</script>"; // Redirect to the medication list page
}
