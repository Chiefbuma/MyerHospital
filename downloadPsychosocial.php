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
		$condition .= " AND psychosocial.visit_date BETWEEN '$from_date' AND '$to_date'";
	} elseif ($from_date) {
		$condition .= " AND psychosocial.visit_date >= '$from_date'";
	} elseif ($to_date) {
		$condition .= " AND psychosocial.visit_date <= '$to_date'";
	}

	// Fetch data
	$query = "SELECT 
				psychosocial.*,
				patient.*,
				branch.*,
				cohort.*,
				calls.*,
				diagnosis.*,
				route.*,
				branch.branch_name AS branch,
				cohort.cohort_name AS cohort,
				scheme.*
				FROM 
				psychosocial
				INNER JOIN 
				patient ON psychosocial.patient_id = patient.patient_id
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
			'Last Visit',
			'Next Review',
			'Educational Level',
			'Career Business',
			'Marital Status',
			'Relationship Status',
			'Primary Relationship Status',
			'Ability To Enjoy Leisure Activities',
			'Spirituality',
			'Level Of Self Esteem',
			'Sex Life',
			'Ability To Cope Recover Disappointments',
			'Rate Of Personal Development Growth',
			'Achievement Of Balance In Life',
			'Social Support System',
			'Substance Use',
			'Substance Used',
			'Assessment Remarks',
			'Scheme',
			'payment_method',
			'Revenue'

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
				$row['last_visit'],
				$row['next_review'],
				$row['educational_level'],
				$row['career_business'],
				$row['marital_status'],
				$row['relationship_status'],
				$row['primary_relationship_status'],
				$row['ability_to_enjoy_leisure_activities'],
				$row['spirituality'],
				$row['level_of_self_esteem'],
				$row['sex_life'],
				$row['ability_to_cope_recover_disappointments'],
				$row['rate_of_personal_development_growth'],
				$row['achievement_of_balance_in_life'],
				$row['social_support_system'],
				$row['substance_use'],
				$row['substance_used'],
				$row['assessment_remarks'],
				$row['scheme_name'],
				$row['payment_method'],
				$row['revenue']

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
