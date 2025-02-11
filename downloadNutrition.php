<?php

date_default_timezone_set("Etc/GMT+8");

session_start();

require_once 'config.php';
require_once 'class.php'; // Assuming this contains the db_class definition

try {
	$db = new db_class();

	// Get the logged-in user's ID
	$user_id = $_SESSION['user_id'];

	// Fetch the role and cohort
	$query = "SELECT branch_id, email, role, cohort_id FROM users WHERE id = ?";
	$stmt = $db->conn->prepare($query);
	$stmt->bind_param("i", $user_id);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result && $row = $result->fetch_assoc()) {
		$user_location = $row['branch_id'];
		$user_role = $row['role'];
		$user_cohort = $row['cohort_id'];
		$user_email = $row['email'];
	} else {
		throw new Exception("Error fetching user details: " . $db->conn->error);
	}

	// Query to fetch branch name based on branch_id
	$branch_query = "SELECT branch_name FROM branch WHERE branch_id = ?";
	$stmt = $db->conn->prepare($branch_query);
	$stmt->bind_param("i", $user_location);
	$stmt->execute();
	$branch_result = $stmt->get_result();

	if ($branch_result && $branch_row = $branch_result->fetch_assoc()) {
		$branch_name = $branch_row['branch_name'];
	} else {
		$branch_name = "Unknown Branch"; // Default branch name in case no branch found
	}

	// Check if the date parameters are set
	if (isset($_GET['from']) && isset($_GET['to'])) {
		$from_date = $_GET['from'];
		$to_date = $_GET['to'];

		// Validate date format (YYYY-MM-DD)
		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to_date)) {
			throw new Exception("Invalid date format.");
		}

		// Convert dates to Y-M-D format (for database compatibility)
		$from_date = DateTime::createFromFormat('Y-m-d', $from_date)->format('Y-m-d');
		$to_date = DateTime::createFromFormat('Y-m-d', $to_date)->format('Y-m-d');

		// Set location filter dynamically based on user role
		if ($user_role == 'admin') {
			$condition = "1=1"; // No filtering for admin, fetch all locations
		} else {
			$condition = "nutrition.branch_id = ?"; // Filter by specific location for non-admins
		}

		// Add date filters
		$condition .= " AND nutrition.visit_date BETWEEN ? AND ?";

		// SQL query to fetch relevant data
		$sql = "SELECT 
                    nutrition.*,
                    patient.*,
                    branch.*,
                    cohort.*,
                    calls.*,
                    route.*,
                    diagnosis.*,
                    branch.branch_name AS branch,
                    cohort.cohort_name AS cohort,
                    scheme.*
                FROM 
                    nutrition
                INNER JOIN 
                    patient ON nutrition.patient_id = patient.patient_id
                LEFT JOIN 
                    calls ON patient.patient_id = calls.patient_id
                LEFT JOIN 
                    branch ON patient.branch_id = branch.branch_id
                LEFT JOIN 
                    cohort ON patient.cohort_id = cohort.cohort_id
                LEFT JOIN 
                    scheme ON nutrition.scheme_id = scheme.scheme_id
                LEFT JOIN 
                    route ON patient.route_id = route.route_id
                LEFT JOIN
                    diagnosis ON patient.diagnosis_id = diagnosis.diagnosis_id
                WHERE 
                    $condition";

		// Prepare the SQL statement
		$stmt = $db->conn->prepare($sql);
		if ($user_role == 'admin') {
			$stmt->bind_param("ss", $from_date, $to_date);
		} else {
			$stmt->bind_param("iss", $user_location, $from_date, $to_date);
		}

		if (!$stmt) {
			throw new Exception("Query preparation failed: " . $db->conn->error);
		}

		// Execute the query
		if (!$stmt->execute()) {
			throw new Exception("Query execution failed: " . $stmt->error);
		}

		$result = $stmt->get_result();

		// Check if any rows are returned
		if ($result->num_rows > 0) {
			// Set headers for CSV download
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment;filename="daily_summary.csv"');

			// Open output buffer
			$output = fopen('php://output', 'w');

			// Add the CSV headers
			fputcsv($output, [
				'Visit Date',
				'Patient Name',
				'Patient No',
				'Diagnosis',
				'Patient Status',
				'Phone Number',
				'Dob',
				'Route',
				'Location',
				'Age',
				'Gender',
				'Email',
				'Branch',
				'Last Visit',
				'Next Review',
				'Muscle Mass',
				'Bone Mass',
				'Weight',
				'BMI',
				'Subcutaneous Fat',
				'Visceral Fat',
				'Weight Remarks',
				'Physical Activity',
				'Meal Plan Set Up',
				'Nutrition Adherence',
				'Nutrition Assessment Remarks',
				'Payment Method',
				'Scheme Name',
				'Revenue'
			]);

			// Fetch rows and write to CSV
			while ($row = $result->fetch_assoc()) {
				fputcsv($output, [
					$row['visit_date'],
					$row['firstname'] . ' ' . $row['lastname'],
					$row['patient_no'],
					$row['diagnosis_name'],
					$row['patient_status'],
					$row['phone_no'],
					$row['dob'],
					$row['route_name'],
					$row['location'],
					$row['age'],
					$row['gender'],
					$row['email'],
					$row['branch_name'],
					$row['last_visit'],
					$row['next_review'],
					$row['muscle_mass'],
					$row['bone_mass'],
					$row['weight'],
					$row['BMI'],
					$row['subcutaneous_fat'],
					$row['visceral_fat'],
					$row['weight_remarks'],
					$row['physical_activity'],
					$row['meal_plan_set_up'],
					$row['nutrition_adherence'],
					$row['nutrition_assessment_remarks'],
					$row['payment_method'],
					$row['scheme_name'],
					$row['revenue']
				]);
			}

			// Close resources
			fclose($output);
			$stmt->close();
			$db->conn->close();
		} else {
			throw new Exception("No records found for the given date range.");
		}
	} else {
		throw new Exception("Please provide a valid date range.");
	}
} catch (Exception $e) {
	echo "<script>alert('Exception: " . htmlspecialchars($e->getMessage()) . "'); window.location.href = 'summary.php';</script>";
}
