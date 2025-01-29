<?php
date_default_timezone_set("Etc/GMT+8");
require_once 'class.php';

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $db = new db_class(); // Create an instance of db_class

        // Collect form data
        $patient_id = $_POST['patient_id']; // Patient ID
        $visit_date = $_POST['visit_date']; // Last visit date
        $next_review = $_POST['next_review']; // Next review date
        $educational_level = $_POST['educational_level']; // Educational level
        $career_business = $_POST['career_business']; // Career or business
        $marital_status = $_POST['marital_status']; // Marital status
        $relationship_status = $_POST['relationship_status']; // Relationship status
        $primary_relationship_status = $_POST['primary_relationship_status']; // Primary relationship status
        $ability_to_enjoy_leisure_activities = $_POST['ability_to_enjoy_leisure_activities']; // Ability to enjoy leisure activities
        $spirituality = $_POST['spirituality']; // Spirituality level
        $level_of_self_esteem = $_POST['level_of_self_esteem']; // Level of self-esteem
        $sex_life = $_POST['sex_life']; // Sex life
        $ability_to_cope_recover_disappointments = $_POST['ability_to_cope_and_recover']; // Ability to cope and recover from disappointments
        $rate_of_personal_development_growth = $_POST['rate_of_personal_development_growth']; // Rate of personal development/growth
        $achievement_of_balance_in_life = $_POST['achievement_of_balance_in_life']; // Achievement of balance in life
        $social_support_system = $_POST['social_support_system']; // Social support system
        $substance_use = $_POST['substance_use']; // Substance use
        $substance_used = $_POST['substance_used']; // Substance used (if applicable)
        $assessment_remarks = $_POST['assessment_remarks'];
        $revenue = $_POST['revenue']; // Additional assessment remarks
        $scheme_id = $_POST['scheme_id']; // Additional assessment remarks

        // SQL query to fetch the maximum visit date for a specific patient
        $query = "SELECT MAX(visit_date) AS last_visit_date FROM psychosocial WHERE patient_id = ?";
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
        $query = "INSERT INTO psychosocial (
            patient_id, visit_date, last_visit, next_review, educational_level, career_business, marital_status, 
            relationship_status, primary_relationship_status, ability_to_enjoy_leisure_activities, spirituality, 
            level_of_self_esteem, sex_life, ability_to_cope_recover_disappointments, rate_of_personal_development_growth, 
            achievement_of_balance_in_life, social_support_system, substance_use, substance_used, assessment_remarks, revenue, scheme_id
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )";

        // Prepare and bind parameters using the $db instance
        if ($stmt = $db->conn->prepare($query)) {
            $stmt->bind_param(
                "isssssssssssssssssssii",
                $patient_id,
                $visit_date,
                $last_visit,
                $next_review,
                $educational_level,
                $career_business,
                $marital_status,
                $relationship_status,
                $primary_relationship_status,
                $ability_to_enjoy_leisure_activities,
                $spirituality,
                $level_of_self_esteem,
                $sex_life,
                $ability_to_cope_recover_disappointments,
                $rate_of_personal_development_growth,
                $achievement_of_balance_in_life,
                $social_support_system,
                $substance_use,
                $substance_used,
                $assessment_remarks,
                $revenue,
                $scheme_id
            );

            // Execute query
            if ($stmt->execute()) {
                // Data inserted successfully
                echo "<script>alert('Psychosocial record added successfully')</script>";
                echo "<script>window.location='assespsycho.php'</script>";
            } else {
                // Error occurred
                throw new Exception("Error executing query: " . $stmt->error);
            }

            // Close the statement
            $stmt->close();
        } else {
            throw new Exception("Error preparing the SQL query: " . $db->conn->error);
        }

        // Close the database connection
        $db->conn->close(); // Close the database connection using the $db instance
    }
} catch (Exception $e) {
    echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
}
