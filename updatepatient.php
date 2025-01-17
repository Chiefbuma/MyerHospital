<?php
date_default_timezone_set("Etc/GMT+8");
require_once 'class.php';

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
        echo "<script>alert('Failed to update patient record')</script>";
    }
    echo "<script>window.location='patients.php'</script>";
}
