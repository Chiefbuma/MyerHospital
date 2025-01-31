<?php
date_default_timezone_set("Etc/GMT+8");
require_once 'class.php';

try {
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

        // Validate form data
        $missingFields = [];
        if (empty($patient_id)) $missingFields[] = 'Patient ID';
        if (empty($scheme_id)) $missingFields[] = 'Scheme ID';
        if (empty($visit_date)) $missingFields[] = 'Visit Date';
        if (empty($next_review)) $missingFields[] = 'Next Review';
        if (empty($muscle_mass)) $missingFields[] = 'Muscle Mass';
        if (empty($bone_mass)) $missingFields[] = 'Bone Mass';
        if (empty($weight)) $missingFields[] = 'Weight';
        if (empty($BMI)) $missingFields[] = 'BMI';
        if (empty($subcutaneous_fat)) $missingFields[] = 'Subcutaneous Fat';
        if (empty($visceral_fat)) $missingFields[] = 'Visceral Fat';
        if (empty($weight_remarks)) $missingFields[] = 'Weight Remarks';
        if (empty($physical_activity)) $missingFields[] = 'Physical Activity';
        if (empty($meal_plan_set_up)) $missingFields[] = 'Meal Plan Set Up';
        if (empty($nutrition_adherence)) $missingFields[] = 'Nutrition Adherence';
        if (empty($nutrition_assessment_remarks)) $missingFields[] = 'Nutrition Assessment Remarks';
        if (empty($revenue)) $missingFields[] = 'Revenue';

        if (!empty($missingFields)) {
            throw new Exception("The following fields are required: " . implode(', ', $missingFields));
        }

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
                throw new Exception("Error executing query: " . $stmt->error);
            }

            // Close the statement
            $stmt->close();
        } else {
            throw new Exception("Error preparing the SQL query.");
        }

        // Close the database connection
        $db->conn->close(); // Close the database connection using the $db instance
    }
} catch (Exception $e) {
    echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
    echo "<script>window.location='assesnutrition.php'</script>";
}
