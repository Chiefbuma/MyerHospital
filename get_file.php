<?php

require_once 'config.php';
require_once 'class.php';  // Assuming this contains the db_class definition

try {
    $db = new db_class();

    // Retrieve the dates from query string and set defaults if not provided
    $from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '1900-01-01';  // default to a very old date
    $to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');  // default to today's date

    // Convert dates to Y-M-D format
    $from_date = DateTime::createFromFormat('Y-m-d', $from_date)->format('Y-m-d');
    $to_date = DateTime::createFromFormat('Y-m-d', $to_date)->format('Y-m-d');

    // Debugging: Output the received dates for verification
    echo "From Date: " . htmlspecialchars($from_date) . "<br>";
    echo "To Date: " . htmlspecialchars($to_date) . "<br>";

    // Query for psychosocial assessments
    $psychosocialQuery = "
        SELECT 
            psychosocial.patient_id,
            psychosocial.visit_date AS psychosocial_visit_date,
            psychosocial.revenue AS psychosocial_revenue,
            patient.firstname,
            patient.lastname,
            patient.gender
        FROM 
            psychosocial
        LEFT JOIN 
            patient ON psychosocial.patient_id = patient.patient_id
        WHERE 
            psychosocial.visit_date BETWEEN ? AND ?
    ";

    // Query for nutrition assessments
    $nutritionQuery = "
        SELECT 
            nutrition.patient_id,
            nutrition.visit_date AS nutrition_visit_date,
            nutrition.revenue AS nutrition_revenue,
            patient.firstname,
            patient.lastname,
            patient.gender
        FROM 
            nutrition
        LEFT JOIN 
            patient ON nutrition.patient_id = patient.patient_id
        WHERE 
            nutrition.visit_date BETWEEN ? AND ?
    ";

    // Execute the psychosocial query
    $stmt = $db->conn->prepare($psychosocialQuery);
    $stmt->bind_param("ss", $from_date, $to_date);
    $stmt->execute();
    $psychosocialResult = $stmt->get_result();

    if ($psychosocialResult === false) {
        throw new Exception("Error executing psychosocial query: " . $db->conn->error);
    }

    // Fetch psychosocial data
    $psychosocialData = [];
    while ($row = $psychosocialResult->fetch_assoc()) {
        $psychosocialData[] = $row;
    }
    $stmt->close();

    // Execute the nutrition query
    $stmt = $db->conn->prepare($nutritionQuery);
    $stmt->bind_param("ss", $from_date, $to_date);
    $stmt->execute();
    $nutritionResult = $stmt->get_result();

    if ($nutritionResult === false) {
        throw new Exception("Error executing nutrition query: " . $db->conn->error);
    }

    // Fetch nutrition data
    $nutritionData = [];
    while ($row = $nutritionResult->fetch_assoc()) {
        $nutritionData[] = $row;
    }
    $stmt->close();
} catch (Exception $e) {
    echo "Exception: " . htmlspecialchars($e->getMessage());
} finally {
    // Close the database connection
    if (isset($db->conn)) {
        $db->conn->close();
    }
}
