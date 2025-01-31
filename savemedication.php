<?php
date_default_timezone_set("Etc/GMT+8");
require_once 'class.php';

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $db = new db_class(); // Create an instance of db_class

        // Get the patient ID (you can pass it from the form or get it from session)
        $patient_id = $_POST['patient_id'];  // Make sure to add a hidden field for this or fetch it from session

        // Collect form data
        $medication_ids = $_POST['medication_id'];
        $days_supplied = $_POST['days_supplied'];
        $no_pills_dispensed = $_POST['no_pills_dispensed'];
        $frequencies = $_POST['frequency'];
        $visit_date = $_POST['visit_date'];

        // Validate form data
        $missingFields = [];
        if (empty($patient_id)) $missingFields[] = 'Patient ID';
        if (empty($medication_ids)) $missingFields[] = 'Medication IDs';
        if (empty($days_supplied)) $missingFields[] = 'Days Supplied';
        if (empty($no_pills_dispensed)) $missingFields[] = 'No. Pills Dispensed';
        if (empty($frequencies)) $missingFields[] = 'Frequencies';
        if (empty($visit_date)) $missingFields[] = 'Visit Date';

        if (!empty($missingFields)) {
            throw new Exception("The following fields are required: " . implode(', ', $missingFields));
        }

        // Prepare SQL query for insertion
        $query = "INSERT INTO medication_use (medication_id, patient_id, days_supplied, no_pills_dispensed, frequency, visit_date) 
                  VALUES (?, ?, ?, ?, ?, ?)";

        // Prepare and bind parameters using the $db instance
        if ($stmt = $db->conn->prepare($query)) {
            // Loop through each form entry and insert into the database
            foreach ($medication_ids as $index => $medication_id) {
                $days = $days_supplied[$index];
                $pills = $no_pills_dispensed[$index];
                $frequency = $frequencies[$index];

                // Bind parameters for each row of data
                $stmt->bind_param("iiiiss", $medication_id, $patient_id, $days, $pills, $frequency, $visit_date);

                // Execute the statement
                if (!$stmt->execute()) {
                    throw new Exception("Error executing query: " . $stmt->error);
                }
            }

            // Close the prepared statement
            $stmt->close();

            // Redirect or display a success message
            echo "<script>alert('Patient record added successfully')</script>";
            echo "<script>window.location='assesmeds.php'</script>";
        } else {
            throw new Exception("Error preparing the SQL query.");
        }

        // Close the database connection
        $db->conn->close(); // Close the database connection using the $db instance
    }
} catch (Exception $e) {
    echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "');</script>";
}
