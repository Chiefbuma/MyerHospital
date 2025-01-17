<?php
date_default_timezone_set("Etc/GMT+8");
require_once 'class.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $db = new db_class(); // Create an instance of db_class

    // Collect form data
    $patient_id = $_POST['patient_id'];
    $scheme_id = $_POST['scheme_id'];
    $visit_date = $_POST['visit_date'];
    $next_review = $_POST['next_review'];
    $muscle_mass = $_POST['muscle_mass'];
    $bone_mass = $_POST['bone_mass'];
    $weight = $_POST['weight'];
    $BMI = $_POST['BMI'];
    $subcutaneous_fat = $_POST['subcutaneous_fat'];
    $visceral_fat = $_POST['visceral_fat'];
    $weight_remarks = $_POST['weight_remarks'];
    $physical_activity = $_POST['physical_activity'];
    $meal_plan_set_up = $_POST['meal_plan_set_up'];
    $nutrition_adherence = $_POST['nutrition_adherence'];
    $nutrition_assessment_remarks = $_POST['nutrition_assessment_remarks'];
    $revenue = $_POST['revenue'];

    // SQL query to fetch the maximum visit date for a specific patient
    $query = "SELECT MAX(visit_date) AS last_visit_date FROM nutrition WHERE patient_id = ?";
    $stmt = $db->conn->prepare($query);
    $stmt->bind_param("i", $patient_id);  // Bind the patient_id as an integer
    $stmt->execute();

    $last_visit = NULL; // Initialize last_visit as NULL in case no record exists
    $result = $stmt->get_result();
    if ($result && $row = $result->fetch_assoc()) {
        // If there's a previous visit, use that date
        $last_visit = $row['last_visit_date'] ?: NULL; // Handle cases where last_visit_date is NULL
    }

    // Prepare SQL query to insert the new record
    $query = "INSERT INTO nutrition (
        patient_id, scheme_id, visit_date, last_visit, next_review, muscle_mass, bone_mass, weight, BMI, 
        subcutaneous_fat, visceral_fat, weight_remarks, physical_activity, meal_plan_set_up, 
        nutrition_adherence, nutrition_assessment_remarks, revenue
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    )";

    // Prepare and bind parameters using the $db instance
    if ($stmt = $db->conn->prepare($query)) {
        $stmt->bind_param(
            "iissssddddssssssd",
            $patient_id,
            $scheme_id,
            $visit_date,
            $last_visit,
            $next_review,
            $muscle_mass,
            $bone_mass,
            $weight,
            $BMI,
            $subcutaneous_fat,
            $visceral_fat,
            $weight_remarks,
            $physical_activity,
            $meal_plan_set_up,
            $nutrition_adherence,
            $nutrition_assessment_remarks,
            $revenue
        );

        // Execute query
        if ($stmt->execute()) {
            // Data inserted successfully
            echo "<script>alert('Patient record added successfully')</script>";
            echo "<script>window.location='assesnutrition.php'</script>";
        } else {
            // Error occurred
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error preparing the SQL query.";
    }

    // Close the database connection
    $db->conn->close(); // Close the database connection using the $db instance
}
