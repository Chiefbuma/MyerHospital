<?php
date_default_timezone_set("Etc/GMT+8");

require_once 'class.php';

if (isset($_POST['submit'])) {
    $db = new db_class();

    // Check if Nutrition form is submitted
    if (isset($_POST['nutrition_details'])) {
        // Collect form data
        $patient_id = $_POST['patient_id'];
        $scheme_id = $_POST['scheme_id']; // Added scheme_id
        $last_visit = $_POST['last_visit'];
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
        $revenue = $_POST['revenue']; // Revenue field

        // Insert into the nutrition table
        $db->insert_nutrition(
            $patient_id,
            $scheme_id, // Added scheme_id
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
    }

    // Check if Chronic form is submitted
    if (isset($_POST['chronic_conditions'])) {
        $patient_id = $_POST['patient_id'];
        $last_visit = $_POST['last_visit'];
        $days_supplied = $_POST['days_supplied'];
        $procedure = $_POST['procedure'];
        $specialty = $_POST['specialty'];
        $refill_date = $_POST['refill_date'];
        $compliance = $_POST['compliance'];
        $exercise = $_POST['exercise'];
        $clinical_goals = $_POST['clinical_goals'];
        $nutrition_follow_up = $_POST['nutrition_follow_up'];
        $psychosocial = $_POST['psychosocial'];
        $vitals_monitoring = $_POST['vitals_monitoring'];
        $vital_signs_monitor = $_POST['vital_signs_monitor'];
        $revenue = $_POST['revenue'];
        $annual_check_up = $_POST['annual_check_up'];

        // Insert into the chronic care table
        $db->insert_chronic_care($patient_id, $last_visit, $days_supplied, $procedure, $specialty, $refill_date, $compliance, $exercise, $clinical_goals, $nutrition_follow_up, $psychosocial, $vitals_monitoring, $vital_signs_monitor, $revenue, $annual_check_up);
    }

    // Check if Psychosocial form is submitted
    if (isset($_POST['psychosocial_assessment'])) {
        // Collect Psychosocial form 
        $patient_id = $_POST['patient_id'];
        $last_visit_psychosocial = $_POST['last_visit_psychosocial'];
        $next_review_psychosocial = $_POST['next_review_psychosocial'];
        $educational_level = $_POST['educational_level'];
        $career_business = $_POST['career_business'];
        $marital_status = $_POST['marital_status'];
        $relationship_status = $_POST['relationship_status'];
        $primary_relationship_status = $_POST['primary_relationship_status'];
        $substance_use = $_POST['substance_use'];
        $substance_used = $_POST['substance_used'];
        $assessment_remarks = $_POST['assessment_remarks'];

        // Insert into the psychosocial table
        $db->insert_psychosocial_assessment(
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
            $branch_id,
            $patient_id
        );
    }


    // Check if the medication form is submitted
    if (isset($_POST['medication_details'])) {
        // Loop through each medication record
        $patient_id = $_POST['patient_id'];
        $medication_ids = $_POST['medication_id']; // Array of medication IDs
        $days_supplied = $_POST['days_supplied']; // Array of days supplied
        $no_pills_dispensed = $_POST['no_pills_dispensed']; // Array of pills dispensed
        $frequencies = $_POST['frequency']; // Array of frequencies

        // Loop to insert each record into the medication_use table
        for ($i = 0; $i < count($medication_ids); $i++) {
            $medication_id = $medication_ids[$i];
            $days = $days_supplied[$i];
            $no_pills = $no_pills_dispensed[$i];
            $frequency = $frequencies[$i];

            // Prepare the query to insert the data
            $db->insert_medication_use($days_supplied, $no_pills_dispensed, $frequency, $patient_id);
        }
    }

    // If the form submission was successful
    echo "<script>alert('Record added successfully');</script>";
    echo "<script>window.location='form_page.php';</script>";
}
