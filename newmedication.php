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
                $_POST['medication_name'],
                $_POST['molecule_name'],
                $_POST['brand_name'],
                $_POST['type_of_brand'],
                $_POST['formulation']
            )
        ) {
            $medication_name = $_POST['medication_name'];
            $molecule_name = $_POST['molecule_name'];
            $brand_name = $_POST['brand_name'];
            $type_of_brand = $_POST['type_of_brand'];
            $formulation = $_POST['formulation'];

            // Validate form data
            if (empty($medication_name) || empty($molecule_name) || empty($brand_name) || empty($type_of_brand) || empty($formulation)) {
                throw new Exception("All fields are required.");
            }

            // Prepare SQL to insert data into the database
            $sql = "INSERT INTO medication (medication_name, molecule_name, brand_name, type_of_brand, formulation) 
                    VALUES (?, ?, ?, ?, ?)";

            // Prepare the statement and bind parameters
            if ($stmt = $db->conn->prepare($sql)) {
                $stmt->bind_param("sssss", $medication_name, $molecule_name, $brand_name, $type_of_brand, $formulation);

                // Execute the query
                if ($stmt->execute()) {
                    // Redirect or display a success message
                    echo "<script>alert('New medication added successfully.');</script>";
                    echo "<script>window.location='addmedication.php';</script>"; // Redirect to the medication list page
                } else {
                    throw new Exception("Error executing query: " . $stmt->error);
                }

                // Close the prepared statement
                $stmt->close();
            } else {
                throw new Exception("Error preparing the SQL query.");
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
