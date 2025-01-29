<?php
date_default_timezone_set("Etc/GMT+8");
session_start();

require_once 'config.php';
require_once 'class.php';

try {
    $db = new db_class();

    // Get the logged-in user's ID
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User not logged in.");
    }
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
        throw new Exception("Error fetching user details.");
    }
    $stmt->close();

    // Fetch branch name
    $branch_query = "SELECT branch_name FROM branch WHERE branch_id = ?";
    $stmt = $db->conn->prepare($branch_query);
    $stmt->bind_param("i", $user_location);
    $stmt->execute();
    $branch_result = $stmt->get_result();

    $branch_name = ($branch_result && $branch_row = $branch_result->fetch_assoc()) ? $branch_row['branch_name'] : "1=1";
    $stmt->close();

    // Retrieve and format filters (GET parameters)
    $from_date = isset($_GET['chronic_from_date']) ? $_GET['chronic_from_date'] : null;
    $to_date = isset($_GET['chronic_to_date']) ? $_GET['chronic_to_date'] : null;

    // Convert dates from 'YYYY-MM-DD' format to DateTime objects and reformat
    if ($from_date) {
        $from_date = DateTime::createFromFormat('Y-m-d', $from_date)->format('Y-m-d');
    }
    if ($to_date) {
        $to_date = DateTime::createFromFormat('Y-m-d', $to_date)->format('Y-m-d');
    }

    // Set filtering conditions based on user role and date range
    $condition = ($user_role == 'admin') ? "1=1" : "chronic.branch_id = ?";
    $params = [];
    $types = '';

    if ($user_role != 'admin') {
        $types .= 'i';
        $params[] = $user_location;
    }

    // Add date filter conditions
    if ($from_date && $to_date) {
        $condition .= " AND chronic.refill_date BETWEEN ? AND ?";
        $types .= 'ss';
        $params[] = $from_date;
        $params[] = $to_date;
    } elseif ($from_date) {
        $condition .= " AND chronic.refill_date >= ?";
        $types .= 's';
        $params[] = $from_date;
    } elseif ($to_date) {
        $condition .= " AND chronic.refill_date <= ?";
        $types .= 's';
        $params[] = $to_date;
    }

    // Fetch data with the applied filtering
    $query = "SELECT 
        chronic.*,
        patient.*,
        branch.*,
        cohort.*,
        calls.*,
        specialist.*,
        route.*,
        branch.branch_name AS branch,
        cohort.cohort_name AS cohort,
        scheme.*
    FROM 
        chronic
    INNER JOIN 
        patient ON chronic.patient_id = patient.patient_id
    LEFT JOIN 
        calls ON patient.patient_id = calls.patient_id
    LEFT JOIN 
        branch ON patient.branch_id = branch.branch_id
    LEFT JOIN 
        cohort ON patient.cohort_id = cohort.cohort_id
    LEFT JOIN 
        specialist ON chronic.speciality_id = specialist.specialist_id
    LEFT JOIN 
        route ON patient.route_id = route.route_id
    LEFT JOIN 
        scheme ON patient.scheme_id = scheme.scheme_id
    WHERE 
        $condition";

    $stmt = $db->conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $report = $stmt->get_result();

    // Check if report data exists and display the rows
    if ($report && $report->num_rows > 0) {
        while ($row2 = $report->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . date("d-m-Y", strtotime($row2['refill_date'])) . "</td>";
            echo "<td>" . htmlspecialchars($row2['firstname'] . " " . htmlspecialchars($row2['lastname'])) . "</td>";
            echo "<td>" . htmlspecialchars($row2['patient_no']) . "</td>";
            echo "<td>" . htmlspecialchars($row2['patient_status']) . "</td>";
            echo "<td>" . htmlspecialchars($row2['branch']) . "</td>";
            echo "<td>" . htmlspecialchars($row2['cohort']) . "</td>";
            echo "<td>" . htmlspecialchars(number_format($row2['revenue'], 0)) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7' class='text-center'>No Data Found</td></tr>";
    }
    $stmt->close();
} catch (Exception $e) {
    echo "<tr><td colspan='7' class='text-center'>Exception: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
} finally {
    // Close the database connection
    if (isset($db->conn)) {
        $db->conn->close();
    }
}
