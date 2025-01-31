<?php
date_default_timezone_set("Etc/GMT+8");
require_once 'class.php';

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $db = new db_class(); // Create an instance of db_class

        // Collect form data
        $patient_id = $_POST['patient_id'];
        $procedure_id = $_POST['procedure'];
        $scheme_id = $_POST['scheme_id'];
        $specialty_id = $_POST['specialty'];
        $refill_date = $_POST['refill_date'];
        $annual_check_up = isset($_POST['annual_check_up']) ? $_POST['annual_check_up'] : null; // if annual check-up field exists
        $specialist_review = isset($_POST['specialist_review']) ? $_POST['specialist_review'] : null; // if specialist review field exists
        $compliance = $_POST['compliance'];
        $exercise = $_POST['exercise'];
        $clinical_goals = $_POST['clinical_goals'];
        $vitals_monitoring = $_POST['vitals_monitoring'];
        $revenue = $_POST['revenue'];
        $vital_signs_monitor = $_POST['vital_signs_monitor'];

        // Validate form data
        $missingFields = [];
        if (empty($patient_id)) $missingFields[] = 'Patient ID';
        if (empty($procedure_id)) $missingFields[] = 'Procedure ID';
        if (empty($scheme_id)) $missingFields[] = 'Scheme ID';
        if (empty($specialty_id)) $missingFields[] = 'Specialty ID';
        if (empty($refill_date)) $missingFields[] = 'Refill Date';
        if (empty($compliance)) $missingFields[] = 'Compliance';
        if (empty($exercise)) $missingFields[] = 'Exercise';
        if (empty($clinical_goals)) $missingFields[] = 'Clinical Goals';
        if (empty($vitals_monitoring)) $missingFields[] = 'Vitals Monitoring';
        if (empty($revenue)) $missingFields[] = 'Revenue';
        if (empty($vital_signs_monitor)) $missingFields[] = 'Vital Signs Monitor';

        if (!empty($missingFields)) {
            throw new Exception("The following fields are required: " . implode(', ', $missingFields));
        }

        // SQL query to fetch the maximum visit date for a specific patient
        $query = "SELECT MAX(refill_date) AS last_visit_date FROM chronic WHERE patient_id = ?";
        $stmt = $db->conn->prepare($query);
        $stmt->bind_param("i", $patient_id);  // Bind the patient_id as an integer
        $stmt->execute();

        $last_visit = NULL; // Initialize last_visit as NULL in case no record exists
        $result = $stmt->get_result();
        if ($result && $row = $result->fetch_assoc()) {
            // If there's a previous visit, use that date
            $last_visit = $row['last_visit_date'] ?: NULL; // Handle cases where last_visit_date is NULL
        }

        // Prepare SQL query
        $query = "INSERT INTO chronic (
            patient_id, procedure_id, scheme_id, speciality_id, refill_date, last_visit, annual_check_up, specialist_review, compliance, exercise, 
            clinical_goals, vitals_monitoring, revenue, vital_signs_monitor
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )";

        // Prepare and bind parameters using the $db instance
        if ($stmt = $db->conn->prepare($query)) {
            $stmt->bind_param(
                "iiiissssssssis",
                $patient_id,
                $procedure_id,
                $scheme_id,
                $specialty_id,
                $refill_date,
                $last_visit,
                $annual_check_up,
                $specialist_review,
                $compliance,
                $exercise,
                $clinical_goals,
                $vitals_monitoring,
                $revenue,
                $vital_signs_monitor
            );

            // Execute query
            if ($stmt->execute()) {
                echo "<script>alert('Patient record added successfully')</script>";
                echo "<script>window.location='asseschronic.php'</script>";
            } else {
                throw new Exception("Error executing query: " . $stmt->error);
            }

            // Close the statement
            $stmt->close();
        } else {
            throw new Exception("Error preparing the SQL query: " . $db->conn->error);
        }

        // Close the database connection
        $db->conn->close();
    }
} catch (Exception $e) {
    echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
}
