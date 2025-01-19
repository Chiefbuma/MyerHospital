<?php

date_default_timezone_set("Etc/GMT+8");

session_start();

require_once 'config.php';
require_once 'class.php'; // Assuming this contains the db_class definition

$db = new db_class();

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch the role and cohort
$query = "SELECT branch_id,email, role,cohort_id FROM users WHERE id = '$user_id'";

$result = $db->conn->query($query);

if ($result && $row = mysqli_fetch_assoc($result)) {
	$user_location = $row['branch_id'];
	$user_role = $row['role'];
	$user_cohort = $row['cohort_id'];
	$user_email = $row['email'];
} else {
	echo "Error fetching location_id: " . mysqli_error($db->conn);
	exit;
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
		die("Invalid date format.");
	}

	// Convert dates to Y-M-D format (for database compatibility)
	if ($from_date) {
		$from_date = DateTime::createFromFormat('Y-m-d', $from_date)->format('Y-m-d');
	}
	if ($to_date) {
		$to_date = DateTime::createFromFormat('Y-m-d', $to_date)->format('Y-m-d');
	}

	// Set location filter dynamically based on user role
	if ($user_role == 'admin') {
		$condition = "1=1"; // No filtering for admin, fetch all locations
	} else {
		$selected_location = $user_location;
		$condition = "chronic.branch_id = '$selected_location'"; // Filter by specific location for non-admins
	}

	// Add date filters
	if ($from_date && $to_date) {
		$condition .= " AND medication_use.visit_date BETWEEN '$from_date' AND '$to_date'";
	} elseif ($from_date) {
		$condition .= " AND medication_use.visit_date >= '$from_date'";
	} elseif ($to_date) {
		$condition .= " AND medication_use.visit_date <= '$to_date'";
	}

	// Fetch data
	$query = "SELECT 
				medication_use.*,
				patient.*,
				branch.*,
				cohort.*,
				calls.*,
				diagnosis.*,
				medication.*,
				route.*,
				branch.branch_name AS branch,
				cohort.cohort_name AS cohort,
				scheme.*
				FROM 
				medication_use
				INNER JOIN 
				patient ON medication_use.patient_id = patient.patient_id
				LEFT JOIN 
				calls ON patient.patient_id = calls.patient_id
				LEFT JOIN 
				branch ON patient.branch_id = branch.branch_id
				LEFT JOIN 
				cohort ON patient.cohort_id = cohort.cohort_id
				LEFT JOIN 
				route ON patient.route_id = route.route_id
				LEFT JOIN
				scheme ON patient.scheme_id = scheme.scheme_id
				LEFT JOIN
				diagnosis ON patient.diagnosis_id = diagnosis.diagnosis_id
				LEFT JOIN 
                medication ON medication_use.medication_id = medication.medication_id
				WHERE 
				$condition";


	// Prepare the SQL statement
	$stmt = $db->conn->prepare($query);

	if (!$stmt) {
		die("Query preparation failed: " . $db->conn->error);
	}

	// Execute the query
	if (!$stmt->execute()) {
		die("Query execution failed: " . $stmt->error);
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
			'Dob',
			'Gender',
			'Age',
			'Location',
			'Phone No',
			'Patient No',
			'Email',
			'Diagnosis',
			'Patient Status',
			'Cohort',
			'Branch Name',
			'Route',
			'Medication Name',
			'Type of Brand',
			'Days Supplied',
			'No Pills Dispensed',
			'Frequency'


		]);





		// Fetch rows and write to CSV
		while ($row = $result->fetch_assoc()) {
			fputcsv($output, [
				$row['visit_date'],
				$row['firstname'] . ' ' . $row['lastname'],
				$row['dob'],
				$row['gender'],
				$row['age'],
				$row['location'],
				$row['phone_no'],
				$row['patient_no'],
				$row['email'],
				$row['diagnosis_name'],
				$row['patient_status'],
				$row['cohort_name'],
				$row['branch_name'],
				$row['route_name'],
				$row['medication_name'],
				$row['type_of_brand'],
				$row['days_supplied'],
				$row['no_pills_dispensed'],
				$row['frequency']


			]);
		}

		// Close resources
		fclose($output);
		$stmt->close();
		$db->conn->close();
	} else {
		die("No records found for the given date range.");
	}
} else {
	die("Please provide a valid date range.");
}
