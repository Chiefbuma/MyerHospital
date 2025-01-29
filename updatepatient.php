<?php
date_default_timezone_set("Etc/GMT+8");
require_once 'class.php';

try {
    if (isset($_POST['update'])) {
        $db = new db_class();

        // Collect form data
        $patient_id = $_POST['patient_no']; // Corrected field name to 'patient_no'
        $firstname = $_POST['first_name'];
        $lastname = $_POST['last_name'];
        $dob = $_POST['dob'];
        $gender = $_POST['gender'];
        $age = $_POST['age'];
        $location = $_POST['location'];
        $route_id = $_POST['route_id'];
        $phone_no = $_POST['phone_no'];
        $email = $_POST['email'];
        $patient_no = $_POST['patient_no'];
        $scheme_id = $_POST['scheme_id'];
        $diagnosis_id = $_POST['diagnosis_id'];
        $patient_status = $_POST['patient_status'];
        $branch_id = $_POST['branch_id'];
        $cohort_id = $_POST['cohort_id'];

        // Validate form data
        if (empty($patient_id) || empty($firstname) || empty($lastname) || empty($dob) || empty($gender) || empty($age) || empty($location) || empty($route_id) || empty($phone_no) || empty($email) || empty($patient_no) || empty($scheme_id) || empty($diagnosis_id) || empty($patient_status) || empty($branch_id) || empty($cohort_id)) {
            throw new Exception("All fields are required.");
        }

        // Update patient record in the database
        $result = $db->update_patient(
            $patient_id,
            $firstname,
            $lastname,
            $dob,
            $gender,
            $age,
            $location,
            $route_id,
            $phone_no,
            $email,
            $patient_no,
            $diagnosis_id,
            $patient_status,
            $branch_id,
            $scheme_id,
            $cohort_id
        );

        // Handle the response
        if ($result) {
            echo "<script>alert('Patient record updated successfully')</script>";
        } else {
            throw new Exception("Failed to update patient record.");
        }
        echo "<script>window.location='patients.php'</script>";
    }
} catch (Exception $e) {
    echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
}
