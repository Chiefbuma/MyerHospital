<?php
date_default_timezone_set("Etc/GMT+8");
require_once 'class.php';

try {
    if (isset($_POST['submit'])) {
        $db = new db_class();

        // Collect form data
        $patient_id = $_POST['patient_no']; // Corrected field name to 'patient_no'
        $firstname = $_POST['first_name'];
        $lastname = $_POST['last_name'];
        $dob = $_POST['dob'];
        $gender = $_POST['gender'];
        $age = $_POST['age'];
        $location = $_POST['location'];
        $route_id = $_POST['route_id']; // Corrected field name to 'route'
        $phone_no = $_POST['phone_no']; // Corrected field name to 'phone_no'
        $email = $_POST['email'];
        $patient_no = $_POST['patient_no']; // Repeated patient_no, same as above
        $scheme_id = $_POST['scheme_id']; // Corrected field name to 'scheme'
        $diagnosis_id = $_POST['diagnosis_id']; // Corrected field name to 'diagnosis_id'
        $patient_status = $_POST['patient_status'];
        $branch_id = $_POST['branch_id']; // Corrected field name to 'branch_id'
        $cohort_id = $_POST['cohort_id']; // Added 'cohort_id' field from the form

        // Validate form data
        $missingFields = [];
        if (empty($firstname)) $missingFields[] = 'First Name';
        if (empty($lastname)) $missingFields[] = 'Last Name';
        if (empty($dob)) $missingFields[] = 'Date of Birth';
        if (empty($gender)) $missingFields[] = 'Gender';
        if (empty($age)) $missingFields[] = 'Age';
        if (empty($location)) $missingFields[] = 'Location';
        if (empty($route_id)) $missingFields[] = 'Route';
        if (empty($phone_no)) $missingFields[] = 'Phone Number';
        if (empty($email)) $missingFields[] = 'Email';
        if (empty($patient_no)) $missingFields[] = 'Patient Number';
        if (empty($scheme_id)) $missingFields[] = 'Scheme';
        if (empty($diagnosis_id)) $missingFields[] = 'Diagnosis';
        if (empty($patient_status)) $missingFields[] = 'Patient Status';
        if (empty($branch_id)) $missingFields[] = 'Branch';
        if (empty($cohort_id)) $missingFields[] = 'Cohort';

        if (!empty($missingFields)) {
            throw new Exception("The following fields are required: " . implode(', ', $missingFields));
        }

        // Insert patient record into the database
        $db->insert_patient($firstname, $lastname, $dob, $gender, $age, $location, $route_id, $phone_no, $email, $patient_no, $diagnosis_id, $patient_status, $branch_id, $scheme_id, $cohort_id); // Added cohort_id
        echo "<script>alert('Patient record added successfully')</script>";
        echo "<script>window.location='patients.php'</script>";
    }
} catch (Exception $e) {
    echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
    echo "<script>window.location='patients.php'</script>";
}
