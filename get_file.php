<?php

require_once 'config.php';
require_once 'class.php';  // Assuming this contains the db_class definition

$db = new db_class();

// Retrieve the dates from query string and set defaults if not provided
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '1900-01-01';  // default to a very old date
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');  // default to today's date

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
        psychosocial.visit_date BETWEEN '$from_date' AND '$to_date'
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
        nutrition.visit_date BETWEEN '$from_date' AND '$to_date'
";

// Execute the psychosocial query
$psychosocialResult = $db->conn->query($psychosocialQuery);

if ($psychosocialResult === false) {
    die("Error executing psychosocial query: " . $db->conn->error);
}

// Fetch psychosocial data
$psychosocialData = [];
while ($row = $psychosocialResult->fetch_assoc()) {
    $psychosocialData[] = $row;
}

// Execute the nutrition query
$nutritionResult = $db->conn->query($nutritionQuery);

if ($nutritionResult === false) {
    die("Error executing nutrition query: " . $db->conn->error);
}

// Fetch nutrition data
$nutritionData = [];
while ($row = $nutritionResult->fetch_assoc()) {
    $nutritionData[] = $row;
}
