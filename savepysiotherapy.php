<?php
date_default_timezone_set("Etc/GMT+8");
require_once 'class.php';

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $db = new db_class(); // Create an instance of db_class

        // Collect form data
        $patient_id = $_POST['patient_id'];
        $scheme_id = $_POST['scheme_id'];
        $pain_level = $_POST['pain_level'];
        $mobility_score = $_POST['mobility_score'];
        $range_of_motion = $_POST['rom']; // Updated column name
        $strength = $_POST['strength'];
        $balance = $_POST['balance'];
        $walking_ability = $_POST['walking_ability'];
        $posture_assessment = $_POST['posture']; // Updated column name
        $exercise_type = $_POST['exercise_type'];
        $frequency_per_week = $_POST['frequency']; // Updated column name
        $duration_per_session = $_POST['duration']; // Updated column name
        $intensity = $_POST['intensity'];
        $pain_level_before_exercise = $_POST['pain_before']; // Updated column name
        $pain_level_after_exercise = $_POST['pain_after']; // Updated column name
        $fatigue_level_before_exercise = $_POST['fatigue_before']; // Updated column name
        $fatigue_level_after_exercise = $_POST['fatigue_after']; // Updated column name
        $visit_date = $_POST['visit_date'];
        $treatment = $_POST['treatment'];
        $challenges = $_POST['challenges'];
        $revenue = $_POST['revenue'];

        // Validate form data
        $missingFields = [];
        if (empty($patient_id)) $missingFields[] = 'Patient ID';
        if (empty($scheme_id)) $missingFields[] = 'Scheme ID';
        if (empty($pain_level)) $missingFields[] = 'Pain Level';
        if (empty($mobility_score)) $missingFields[] = 'Mobility Score';
        if (empty($range_of_motion)) $missingFields[] = 'Range of Motion';
        if (empty($strength)) $missingFields[] = 'Strength';
        if (empty($balance)) $missingFields[] = 'Balance';
        if (empty($walking_ability)) $missingFields[] = 'Walking Ability';
        if (empty($posture_assessment)) $missingFields[] = 'Posture Assessment';
        if (empty($exercise_type)) $missingFields[] = 'Exercise Type';
        if (empty($frequency_per_week)) $missingFields[] = 'Frequency';
        if (empty($duration_per_session)) $missingFields[] = 'Duration';
        if (empty($intensity)) $missingFields[] = 'Intensity';
        if (empty($pain_level_before_exercise)) $missingFields[] = 'Pain Level Before Exercise';
        if (empty($pain_level_after_exercise)) $missingFields[] = 'Pain Level After Exercise';
        if (empty($fatigue_level_before_exercise)) $missingFields[] = 'Fatigue Level Before Exercise';
        if (empty($fatigue_level_after_exercise)) $missingFields[] = 'Fatigue Level After Exercise';
        if (empty($visit_date)) $missingFields[] = 'Visit Date';
        if (empty($treatment)) $missingFields[] = 'Treatment';
        if (empty($challenges)) $missingFields[] = 'Challenges';
        if (empty($revenue)) $missingFields[] = 'Revenue';

        if (!empty($missingFields)) {
            throw new Exception("The following fields are required: " . implode(', ', $missingFields));
        }

        // Prepare SQL query
        $query = "INSERT INTO physiotherapy (
            patient_id, scheme_id, pain_level, mobility_score, range_of_motion, strength, balance, walking_ability, posture_assessment, exercise_type, 
            frequency_per_week, duration_per_session, intensity, pain_level_before_exercise, pain_level_after_exercise, fatigue_level_before_exercise, fatigue_level_after_exercise, visit_date, treatment, challenges, revenue
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )";

        // Prepare and bind parameters using the $db instance
        if ($stmt = $db->conn->prepare($query)) {
            $stmt->bind_param(
                "iiiiiiisssiiiiiiisssi",
                $patient_id,
                $scheme_id,
                $pain_level,
                $mobility_score,
                $range_of_motion,
                $strength,
                $balance,
                $walking_ability,
                $posture_assessment,
                $exercise_type,
                $frequency_per_week,
                $duration_per_session,
                $intensity,
                $pain_level_before_exercise,
                $pain_level_after_exercise,
                $fatigue_level_before_exercise,
                $fatigue_level_after_exercise,
                $visit_date,
                $treatment,
                $challenges,
                $revenue
            );

            // Execute query
            if ($stmt->execute()) {
                echo "<script>alert('Physiotherapy record added successfully')</script>";
                echo "<script>window.location='assesphysiotherapy.php'</script>";
            } else {
                throw new Exception("Error executing query: " . $stmt->error);
                echo "<script>window.location='assesphysiotherapy.php'</script>";
            }

            // Close the statement
            $stmt->close();
        } else {
            throw new Exception("Error preparing the SQL query: " . $db->conn->error);
            echo "<script>window.location='assesphysiotherapy.php'</script>";
        }

        // Close the database connection
        $db->conn->close();
    }
} catch (Exception $e) {
    echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
    echo "<script>window.location='assesphysiotherapy.php'</script>";
}
